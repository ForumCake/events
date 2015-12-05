<?php
namespace Cake\Events;

class Helper_Date
{

    public static function getMonthsOfTheYear($firstMonth = 1)
    {
        $languages = \XenForo_Application::get('languages');

        $xenOptions = \XenForo_Application::getOptions();

        $defaultLanguage = $languages[$xenOptions->defaultLanguageId];

        $phraseCache = $defaultLanguage['phrase_cache'];

        $months = array(
            1 => $phraseCache['month_1'],
            2 => $phraseCache['month_2'],
            3 => $phraseCache['month_3'],
            4 => $phraseCache['month_4'],
            5 => $phraseCache['month_5'],
            6 => $phraseCache['month_6'],
            7 => $phraseCache['month_7'],
            8 => $phraseCache['month_8'],
            9 => $phraseCache['month_9'],
            10 => $phraseCache['month_10'],
            11 => $phraseCache['month_11'],
            12 => $phraseCache['month_12']
        );

        for ($i = 1; $i < $firstMonth; $i ++) {
            unset($months[$i]);
            $months[$i] = $phraseCache['month_' . $i];
        }

        return $months;
    }

    public static function getYears($firstMonth = 1)
    {
        $years = array();

        $timezone = new \DateTimeZone(\XenForo_Visitor::getInstance()->timezone);
        $dateTime = new \DateTime(null, $timezone);

        $currentYear = $dateTime->format('Y');

        for ($i = - 3; $i <= 3; $i ++) {
            if ($firstMonth == 1) {
                $years[$currentYear + $i] = $currentYear + $i;
            } else {
                $years[$currentYear + $i] = $currentYear + $i . '/' . substr($currentYear + $i + 1, -2);
            }
        }

        return $years;
    }
}