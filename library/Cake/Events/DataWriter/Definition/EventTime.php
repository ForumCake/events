<?php
namespace Cake\Events;

abstract class DataWriter_Definition_EventTime
{

    /**
     * Contains the structure returned from {@link _getTimeStructure()}.
     *
     * @var array
     */
    protected $_structure = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_structure = $this->_getTimeStructure();
    }

    /**
     * Gets the structure of the time record.
     * This only includes parts that are variable. Keys returned:
     * * table - name of the table (eg, cake_event_time)
     * * key - name of the time's key (eg, event_time_id)
     * * container - name of the container/event's key (eg, event_id)
     * * contentType - name of the content type the time uses (eg, cake_event)
     *
     * @return array
     */
    abstract protected function _getTimeStructure();

    /**
     * Gets the parts of the time configuration options that override the
     * defaults.
     * Options:
     * * hasParentEvent (false)
     *
     * @return array
     */
    protected function _getTimeConfiguration()
    {
        return array();
    }

    /**
     * Gets the event data writer for the given event ID.
     * If no event is given, should return false.
     *
     * @param integer $eventId
     * @param constant $errorHandler DW error handler constant (usually parent
     * DW's error handler)
     *
     * @return XenForo_DataWriter_Event|false
     */
    public function getEventDataWriter($eventId, $errorHandler)
    {
        return false;
    }

    /**
     * Gets the search data handler for this type of time.
     *
     * @return XenForo_Search_DataHandler_Abstract|false
     */
    public function getSearchDataHandler()
    {
        return false;
    }

    /**
     * Gets the effective time configuration.
     * This merges the defaults with
     * the specific class overrides. See {@link _getTimeConfiguration()} for
     * options.
     *
     * @return array
     */
    public function getTimeConfiguration()
    {
        $configuration = array(
            'hasParentEvent' => false
        );

        return array_merge($configuration, $this->_getTimeConfiguration());
    }

    /**
     * Gets the full time structure array.
     * See {@link _getTimeStructure()} for
     * data returned.
     *
     * @return array
     */
    public function getTimeStructure()
    {
        return $this->_structure;
    }

    public function getTimeTableName()
    {
        return $this->_structure['table'];
    }

    public function getTimeKeyName()
    {
        return $this->_structure['key'];
    }

    public function getContainerKeyName()
    {
        return $this->_structure['container'];
    }

    public function getContentType()
    {
        return $this->_structure['contentType'];
    }
}