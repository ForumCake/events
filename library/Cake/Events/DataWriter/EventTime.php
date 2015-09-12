<?php
namespace Cake\Events;

abstract class DataWriter_EventTime extends \XenForo_DataWriter
{

    /**
     * Gets the object that represents the definition of this type of time.
     *
     * @return DataWriter_Definition_EventTime
     */
    abstract public function getEventTimeDefinition();

    /**
     * Identifies if a time has a parent event item.
     * Must overload {@see getEventDataWriter()} if set to true.
     *
     * @var boolean
     */
    protected $_hasParentEvent = true;

    /**
     * Data about the time's definition.
     *
     * @var DataWriter_Definition_EventTime
     */
    protected $_timeDefinition = null;

    /**
     * The event data writer.
     *
     * @var DataWriter_Event
     */
    protected $_eventDw = null;

    /**
     * The insert/update mode of the event data writer
     *
     * @var string insert|update
     */
    protected $_eventMode = null;

    /**
     * Constructor.
     *
     * @param mixed $errorHandler Error handler. See {@link ERROR_EXCEPTION} and
     * related.
     * @param array|null $inject Dependency injector. Array keys available: db,
     * cache.
     */
    public function __construct($errorHandler = self::ERROR_EXCEPTION, array $inject = null)
    {
        $this->_timeDefinition = $this->getEventTimeDefinition();

        $config = $this->_timeDefinition->getTimeConfiguration();
        $this->_hasParentEvent = $config['hasParentEvent'];

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
        $structure = $this->_timeDefinition->getTimeStructure();

        $fields = array(
            $structure['table'] => array(
                $structure['key'] => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ),
                $structure['container'] => array(
                    'type' => self::TYPE_UINT,
                    'required' => true
                ),
                'start_time' => array(
                    'type' => self::TYPE_UINT,
                    'required' => true
                ),
                'end_time' => array(
                    'type' => self::TYPE_UINT,
                    'required' => true
                ),
                'start_timezone' => array(
                    'type' => self::TYPE_STRING,
                    'default' => ''
                ),
                'end_timezone' => array(
                    'type' => self::TYPE_STRING,
                    'default' => ''
                )
            )
        );

        return $fields;
    }

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        $keyName = $this->getEventTimeKeyName();

        return $keyName . ' = ' . $this->_db->quote($this->getExisting($keyName));
    }

    /**
     * Generic event time pre-save handler.
     */
    protected final function _preSave()
    {
        $this->_timePreSave();
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _timePreSave()
    {
    }

    /**
     * Generic event time post-save handler.
     */
    protected final function _postSave()
    {
        $this->_timePostSave();

        $this->_saveEventDataWriter();
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _timePostSave()
    {
    }

    /**
     * Generic event time pre-delete handler.
     */
    protected final function _preDelete()
    {
        $this->_timePreDelete();
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _timePreDelete()
    {
    }

    /**
     * Generic event time post-delete handler.
     */
    protected final function _postDelete()
    {
        $this->_timePostDelete();
    }

    /**
     * Designed to be overridden by child classes
     */
    protected function _timePostDelete()
    {
    }

    /**
     * Gets the event data writer.
     * Note that if the container value changes, this cache will not be removed.
     *
     * @return XenForo_DataWriter_Event|false
     */
    public function getEventDataWriter()
    {
        if (!$this->_hasParentEvent) {
            return false;
        }

        if ($this->_eventDw === null) {
            $containerId = $this->get($this->getContainerKeyName());
            if (!$containerId) {
                $this->_eventDw = false;
            } else {
                $this->_eventDw = $this->_timeDefinition->getEventDataWriter($containerId, $this->_errorHandler);
                if ($this->_eventDw && $this->_eventMode === null) {
                    $this->_eventMode = 'update';
                }
            }
        }

        return $this->_eventDw;
    }

    /**
     * Sets the data writer for the event this time is in--or will be in.
     *
     * @param DataWriter_Event|null $eventDw
     * @param boolean True if $eventDataWriter->isInsert()
     */
    public function setEventDataWriter(DataWriter_Event $eventDw = null, $isInsert = null)
    {
        $this->_eventDw = $eventDw;

        if ($isInsert !== null) {
            $this->_eventMode = ($isInsert ? 'insert' : 'update');
        }
    }

    /**
     * Saves the event data writer if it exists and has changed.
     */
    protected function _saveEventDataWriter()
    {
        if ($this->_eventDw && $this->_eventDw->hasChanges()) {
            $this->_eventDw->save();
        }
    }

    /**
     * Gets the data about the event this time is in.
     * This may use the event data writer, or some other source if needed.
     *
     * @return array|null
     */
    public function getEventData()
    {
        if ($this->_hasParentEvent) {
            $eventDw = $this->getEventDataWriter();
            if ($eventDw) {
                return $eventDw->getMergedData();
            }
        }

        return null;
    }

    /**
     * Gets the current value of the event time ID for this time.
     *
     * @return integer
     */
    public function getEventTimeId()
    {
        return $this->get($this->getEventTimeKeyName());
    }

    /**
     * The name of the table that holds this type of event time.
     *
     * @return string
     */
    public function getEventTimeTableName()
    {
        return $this->_timeDefinition->getTimeTableName();
    }

    /**
     * The name of the event time primary key.
     * Must be an auto increment column.
     *
     * @return string
     */
    public function getEventTimeKeyName()
    {
        return $this->_timeDefinition->getTimeKeyName();
    }

    /**
     * Gets the field name of the container this time belongs to (eg, event).
     *
     * @return string
     */
    public function getContainerKeyName()
    {
        return $this->_timeDefinition->getContainerKeyName();
    }

    /**
     * Gets the content type for tables that contain multiple data types
     * together.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->_timeDefinition->getContentType();
    }
}