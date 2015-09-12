<?php
namespace Cake\Events;

abstract class ViewPublic_Add extends \XenForo_ViewPublic_Base
{

    public function renderHtml()
    {
        $hours = array();
        for ($i = 0; $i < 24; $i++) {
            $hh = str_pad($i, 2, '0', STR_PAD_LEFT);
            $hours[$hh] = $hh;
        }
        $this->_params['hours'] = $hours;

        $minutes = array();
        for ($i = 0; $i < 60; $i += 5) {
            $mm = str_pad($i, 2, '0', STR_PAD_LEFT);
            $minutes[$mm] = $mm;
        }
        $this->_params['minutes'] = $minutes;

        $this->_params['timezones'] = \XenForo_Helper_TimeZone::getTimeZones();
    }
}