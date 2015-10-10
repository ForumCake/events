<?php
namespace Cake\Events;

abstract class DataWriter_Event extends \XenForo_DataWriter
{

    use \Cake\Trait_Core;

    /**
     * Gets the object that represents the definition of this type of event.
     *
     * @return DataWriter_Definition_Event
     */
    abstract public function getEventDefinition();

    /**
     * Gets the object that represents the definition of the time within this
     * event.
     *
     * @return DataWriter_Definition_EventTime
     */
    abstract public function getEventTimeDefinition();

    /**
     * Gets simple information about all times in this event.
     * Fields are assumed to be the standard event time fields, not including
     * the actual time unless specifically requested.
     *
     * @param boolean $includeTime If true, includes the time contents
     *
     * @return array Format: [event time id] => info
     */
    abstract protected function _getTimesInEventSimple($includeTime = false);

    /**
     * Gets the IDs of all times in this event.
     * Designed to be overridden.
     *
     * @return array
     */
    protected function _getTimeIdsInEvent()
    {
        return array_keys($this->_getEventTimes(false));
    }

    /**
     * Option to control whether a time is required on insert of a new event.
     * Default is true.
     *
     * @var string
     */
    const OPTION_REQUIRE_INSERT_TIME = 'requireInsertTime';

    /**
     * Data about the event's definition.
     *
     * @var XenForo_EventTime_Definition_Abstract
     */
    protected $_eventDefinition = null;

    /**
     * Data about the definition of times within.
     *
     * @var XenForo_EventTime_Definition_Abstract
     */
    protected $_timeDefinition = null;

    /**
     * Data writer for the time in this event.
     *
     * @var DataWriter_EventTime|null
     */
    protected $_timeDw = null;

    /**
     * Data writer for the calendar entry for this event.
     *
     * @var Calendars/DataWriter_CalendarEntry|null
     */
    protected $_calendarEntryDw = null;

    /**
     * Constructor.
     *
     * @param constant Error handler. See {@link ERROR_EXCEPTION} and related.
     * @param array|null Dependency injector. Array keys available: db, cache.
     */
    public function __construct($errorHandler = self::ERROR_EXCEPTION, array $inject = null)
    {
        $this->_eventDefinition = $this->getEventDefinition();

        $this->_timeDefinition = $this->getEventTimeDefinition();

        parent::__construct($errorHandler, $inject);
    }

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getCommonFields()
    {
        $structure = $this->_eventDefinition->getEventStructure();

        $data = array(
            $structure['table'] => array(
                $structure['key'] => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ),
                'event_time_id' => array(
                    'type' => self::TYPE_UINT,
                    'default' => 0
                )
            )
        );

        return $data;
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        $keyName = $this->getEventKeyName();

        return $keyName . ' = ' . $this->_db->quote($this->getExisting($keyName));
    }

    /**
     * Gets the default set of options for this data writer.
     *
     * @return array
     */
    protected function _getDefaultOptions()
    {
        return array(
            self::OPTION_REQUIRE_INSERT_TIME => true
        );
    }

    /**
     * Gets a data writer that represents the time.
     * This is primarily used for inserts, but may also be used for updates.
     *
     * @return DataWriter_EventTime
     */
    public function getTimeDw()
    {
        if (!$this->_timeDw) {
            $this->_timeDw = $this->_eventDefinition->getTimeDataWriter($this->get('event_time_id'),
                $this->_errorHandler);
            $this->_timeDw->setEventDataWriter($this, $this->isInsert());
        }

        return $this->_timeDw;
    }

    public function setEvent(array $event, array $viewingUser)
    {
        $timeDw = $this->getTimeDw();

        if ($this->isModuleActive('Calendars') && !empty($event['calendar_id']) && $this->isInsert()) {
            /* @var $calendarModel Calendars\Model_Calendar */
            $calendarModel = $this->getModelFromCache('Cake\Events\Calendars\Model_Calendar');
        
            $calendar = $calendarModel->getCalendarById($event['calendar_id']);
        
            if (!$calendar) {
                $this->error(new \XenForo_Phrase('cake_requested_calendar_not_found'));
            }
            
            // TODO add $calendar to $dw?
            
            $allowRegularEvent = $calendar['allow_event'];
            $allowAllDayEvent = $calendar['allow_event_all_day'];
            $allowAllDayMultiple = $calendar['allow_event_all_day_multiple'];
            
            /* @var $calendarEntryDw Calendars\DataWriter_CalendarEntry */
            $calendarEntryDw = \XenForo_DataWriter::create('Cake\Events\Calendars\DataWriter_CalendarEntry');
        
            $this->_calendarEntryDw = $calendarEntryDw;
        } else {
            $allowRegularEvent = true;
            $allowAllDayEvent = true;
            $allowAllDayMultiple = true;
        }
        
        if (empty($event['all_day']) && $allowRegularEvent) {
            $startTime = new \DateTime(
                $event['start_date'] . ' ' . $event['start_time_hh'] . ':' . $event['start_time_mm'],
                new \DateTimeZone($event['start_timezone']));
            $event['start_time'] = $startTime->getTimestamp();
            if ($event['end_date']) {
                $endTime = new \DateTime($event['end_date'] . ' ' . $event['end_time_hh'] . ':' . $event['end_time_mm'],
                    new \DateTimeZone($event['end_timezone']));
                $event['end_time'] = $endTime->getTimestamp();
            } else {
                $event['end_time'] = $event['start_time'];
            }
        } else {
            $startTime = new \DateTime($event['start_date'] . '00:00', new \DateTimeZone('UTC'));
            $event['start_time'] = $startTime->getTimestamp();
            $event['start_timezone'] = '';
            if ($allowAllDayMultiple) {
                if (!$event['end_date']) {
                    $event['end_date'] = $event['start_date'];
                }
                $endTime = new \DateTime($event['end_date'] . '00:00', new \DateTimeZone('UTC'));
                $event['end_time'] = $endTime->getTimestamp();
            } else {
                $event['end_time'] = $event['start_time'];
            }
            $event['end_timezone'] = '';
        }

        $timeDw->bulkSet(
            array(
                'start_time' => $event['start_time'],
                'start_timezone' => $event['start_timezone'],
                'end_time' => $event['end_time'],
                'end_timezone' => $event['end_timezone']
            ));
        
        if ($this->_calendarEntryDw) {
            $this->_calendarEntryDw->bulkSet(array(
                'calendar_id' => $event['calendar_id'],
                'content_type' => $this->getContentType(),
                'first_start_time' => $event['start_time'],
                'last_end_time' => $event['end_time'],
                'timezone_adjust' => 0,
                'user_id' => $viewingUser['user_id'],
                'username' => $viewingUser['username']
            ));
        }
    }

    /**
     * Generic Event Time Pre Save handler
     */
    protected final function _preSave()
    {
        if ($this->isInsert() && $this->getOption(self::OPTION_REQUIRE_INSERT_TIME) && !$this->_timeDw) {
            throw new \XenForo_Exception('An event insert was attempted without the required time.');
        }

        $this->_eventPreSave();

        if ($this->_timeDw) {
            $this->_syncTimeDw();
            $this->_preSaveTimeDw();
        }

        if ($this->_calendarEntryDw) {
            $this->_syncCalendarEntryDw();
            $this->_preSaveCalendarEntryDw();
        }
    }

    /**
     * Synchronizes the time DW with data set in this event before saving.
     */
    protected function _syncTimeDw()
    {
        if ($this->isInsert()) {
            $this->_timeDw->set($this->_timeDw->getContainerKeyName(), 0);
        }
    }

    /**
     * Validate that the time DW is saveable and merge any errors into this DW.
     */
    protected function _preSaveTimeDw()
    {
        $timeDw = $this->_timeDw;

        $timeDw->preSave();
        $timeErrors = $timeDw->getErrors();
        if ($timeErrors) {
            $this->_errors = array_merge($this->_errors, $timeErrors);

        }
    }

    /**
     * Synchronizes the calendar entry DW with data set in this event before saving.
     */
    protected function _syncCalendarEntryDw()
    {
        if ($this->isInsert()) {
            $this->_calendarEntryDw->set('content_id', 0);
        }
    }

    /**
     * Validate that the calendar entry DW is saveable and merge any errors into this DW.
     */
    protected function _preSaveCalendarEntryDw()
    {
        $calendarEntryDw = $this->_calendarEntryDw;

        $calendarEntryDw->preSave();
        $calendarEntryErrors = $calendarEntryDw->getErrors();
        if ($calendarEntryErrors) {
            $this->_errors = array_merge($this->_errors, $calendarEntryErrors);
        }
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _eventPreSave()
    {
    }

    /**
     * Cache of the times in this event so they're only retrieved when needed
     *
     * @var array
     */
    protected $_timeCache = array();

    /**
     * Gets all times in this event, in order
     *
     * @param bool $includeTime
     * @return array
     */
    protected function _getEventTimes($includeTime = false)
    {
        $cacheKey = 'times' . ($includeTime ? '-time' : '');
        if (!isset($this->_timeCache[$cacheKey])) {
            $this->_timeCache[$cacheKey] = $this->_getTimesInEventSimple($includeTime);
        }

        return $this->_timeCache[$cacheKey];
    }

    protected function _getEventTimeIds()
    {
        if (!isset($this->_timeCache['timeIds'])) {
            if (isset($this->_timeCache['times'])) {
                $this->_timeCache['timeIds'] = array_keys($this->_timeCache['times']);
            } else {
                $this->_timeCache['timeIds'] = $this->_getTimeIdsInEvent();
            }
        }

        return $this->_timeCache['timeIds'];
    }

    /**
     * Generic Event Time Post Save handler
     */
    protected final function _postSave()
    {
        if ($this->_timeDw) {
            $this->_saveTimeDw();
        }

        if ($this->_calendarEntryDw) {
            $this->_saveCalendarEntryDw();
        }

        $this->_eventPostSave();
    }

    /**
     * Saves the time DW and merges and required data from it back to this (eg,
     * time ID).
     */
    protected function _saveTimeDw()
    {
        $timeDw = $this->_timeDw;

        if ($this->isInsert()) {
            $eventId = $this->get($this->getEventKeyName());
            $timeDw->set($timeDw->getContainerKeyName(), $eventId, '',
                array(
                    'setAfterPreSave' => true
                ));
        }

        if ($timeDw->hasChanges()) {
            $timeDw->setEventDataWriter(null, $this->isInsert());

            $timeDw->save();
        }

        if ($this->isInsert()) {
            $timeId = $timeDw->getEventTimeId();

            $toUpdate = array(
                'event_time_id' => $timeId
            );

            $keyName = $this->getEventKeyName();
            $condition = $keyName . ' = ' . $this->_db->quote($this->get($keyName));

            $this->_db->update($this->getEventTableName(), $toUpdate, $condition);
            $this->bulkSet($toUpdate, array(
                'setAfterPreSave' => true
            ));
        }
    }

    /**
     * Saves the calendar entry DW.
     */
    protected function _saveCalendarEntryDw()
    {
        $calendarEntryDw = $this->_calendarEntryDw;

        if ($this->isInsert()) {
            $eventId = $this->get($this->getEventKeyName());
            $calendarEntryDw->set('content_id', $eventId, '',
                array(
                    'setAfterPreSave' => true
                ));
        }

        if ($calendarEntryDw->hasChanges()) {
            $calendarEntryDw->save();
        }
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _eventPostSave()
    {
    }

    /**
     * Generic event pre-delete handler.
     */
    protected final function _preDelete()
    {
        $this->_eventPreDelete();
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _eventPreDelete()
    {
    }

    /**
     * Generic event post-delete handler.
     */
    protected final function _postDelete()
    {
        $this->_deleteEventTimes();

        $this->_eventPostDelete();
    }

    /**
     * Deletes all times in this event.
     */
    protected function _deleteEventTimes()
    {
        // TODO
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _eventPostDelete()
    {
    }

    /**
     * Gets the current value of the event ID for this event.
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->get($this->getEventKeyName());
    }

    /**
     * The name of the table that holds the event data.
     *
     * @return string
     */
    public function getEventTableName()
    {
        return $this->_eventDefinition->getEventTableName();
    }

    /**
     * The name of the event table's primary key.
     * This must be an auto increment field.
     *
     * @return string
     */
    public function getEventKeyName()
    {
        return $this->_eventDefinition->getEventKeyName();
    }

    /**
     * Gets the content type for tables that contain multiple data types
     * together.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_eventDefinition->getContentType();
    }
}