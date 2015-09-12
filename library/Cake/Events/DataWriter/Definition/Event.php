<?php
namespace Cake\Events;

abstract class DataWriter_Definition_Event
{

    /**
     * Contains the structure returned from {@link _getEventStructure()}.
     *
     * @var array
     */
    protected $_structure = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->_structure = $this->_getEventStructure();
    }

    /**
     * Gets the structure of the event record.
     * This only includes parts that are variable. Keys returned:
     * * table - name of the table (eg, cake_event)
     * * key - name of the event's key (eg, event_id)
     * * contentType - name of the content type the event uses (eg, cake_event)
     *
     * @return array
     */
    abstract protected function _getEventStructure();

    /**
     * Gets the time data writer for the given time ID.
     * If no time is given, should return a "new" DW.
     *
     * @param integer $timeId
     * @param constant $errorHandler DW error handler constant (usually parent
     * DW's error handler)
     *
     * @return DataWriter_EventTime
     */
    abstract public function getTimeDataWriter($timeId, $errorHandler);

    /**
     * Gets the full event structure array.
     * See {@link _getEventStructure()} for data returned.
     *
     * @return array
     */
    public function getEventStructure()
    {
        return $this->_structure;
    }

    public function getEventTableName()
    {
        return $this->_structure['table'];
    }

    public function getEventKeyName()
    {
        return $this->_structure['key'];
    }

    public function getContentType()
    {
        return $this->_structure['contentType'];
    }
}