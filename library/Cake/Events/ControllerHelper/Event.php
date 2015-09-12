<?php
namespace Cake\Events;

class ControllerHelper_Event extends \XenForo_ControllerHelper_Abstract
{

    /**
     *
     * @var \XenForo_Input
     */
    protected $_input;

    /**
     * Additional constructor setup behavior.
     */
    protected function _constructSetup()
    {
        $this->_input = $this->_controller->getInput();
    }

    public function getDefaultAddEventViewParams()
    {
        $calendarId = $this->_input->filterSingle('calendar_id', \XenForo_Input::UINT);

        return array(
            'selectedCalendarId' => $calendarId
        );
    }
}