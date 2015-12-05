<?php
namespace Cake\Events;

class Model_Event extends \XenForo_Model
{

    public function prepareEvent(array $event, array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        if (!empty($event['start_time'])) {
            if (!$event['start_timezone']) {
                $event['all_day'] = true;
                $event['start_timezone'] = $viewingUser['timezone'];
                $event['end_timezone'] = $viewingUser['timezone'];
                $timezone = new \DateTimeZone($viewingUser['timezone']);
                $now = new \DateTime(null, $timezone);
                $event['start_time'] -= $now->getOffset();
                $event['end_time'] -= $now->getOffset();
            }
        }

        return $event;
    }

    public function prepareEvents(array $events, array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        foreach ($events as &$event) {
            $event = $this->prepareEvent($event, $viewingUser);
        }

        return $events;
    }

    public function getDefaultEvent(array $viewingUser = null)
    {
        $this->standardizeViewingUserReference($viewingUser);

        return array(
            'start_timezone' => $viewingUser['timezone'],
            'end_timezone' => $viewingUser['timezone'],
            'start_time_hh' => 12,
            'start_time_mm' => 0,
            'end_time_hh' => 13,
            'end_time_mm' => 0
        );
    }
}