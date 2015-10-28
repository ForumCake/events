<?php
namespace Cake\Events;

class ControllerHelper_Event extends \Cake\ControllerHelper_Abstract
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
        return array();
    }
}