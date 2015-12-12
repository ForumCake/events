<?php
namespace Cake\Events;

trait Trait_RoutePrefix
{
    use \Cake\Trait_RoutePrefix;

    /**
     * Resolves the action from a route that looks like year/month/day/action
     * and sets the year, month and date params into the specified parameter
     * names.
     *
     * Supports year/month/day/action1/action2 (returns "action1/action2"). If
     * given "action1/action2", this will return the full string as the action.
     *
     * @param string $routePath
     *            Full path to route against. This should not
     *            include a prefix.
     * @param Zend_Controller_Request_Http $request
     *            Request object
     * @param string $paramName
     *            Name of the parameter to be registered with the
     *            request object (if found)
     * @param string $defaultActionWithParam
     *            If there's no action and there is
     *            an int param, use this as the default action
     * @param string $yearParamName
     *            Name of the year parameter to be registered
     *            with the request object (if found)
     * @param string $monthParamName
     *            Name of the month parameter to be
     *            registered with the request object (if found)
     * @param string $dayParamName
     *            Name of the day parameter to be registered
     *            with the request object (if found)
     *
     * @return string The requested action
     */
    public function resolveActionWithDateParam($routePath, \Zend_Controller_Request_Http $request, $paramName = '', $defaultActionWithParam = '', $yearParamName = 'year', $monthParamName = 'month', $dayParamName = 'day', $weekParamName = 'week')
    {
        if ($paramName) {
            $parts = explode('/', $routePath, 2);
            $action = isset($parts[1]) ? $parts[1] : '';

            $paramParts = explode(\XenForo_Application::URL_ID_DELIMITER, $parts[0]);
            $paramId = end($paramParts);

            if (count($paramParts) > 1 && $paramId === strval(intval($paramId))) {
                $request->setParam($paramName, intval($paramId));
                if ($action === '') {
                    $action = $defaultActionWithParam;
                }
            } else {
                $action = $routePath;
            }
        } else {
            $action = $routePath;
        }

        $months = Helper_Date::getMonthsOfTheYear();
        $months = implode('|', array_map('strtolower', $months));
        if (preg_match('#^(' . $months . ')/(\d{4})(?:/(\d+))?(?:/(.*))?#i', $action, $match)) {
            if (isset($match[4])) {
                $action = $match[4];
            } else {
                $action = '';
            }
            $request->setParam($monthParamName, $match[1]);
            if (! empty($match[3])) {
                $request->setParam($dayParamName, $match[2]);
                $request->setParam($yearParamName, $match[3]);
            } else {
                $request->setParam($yearParamName, $match[2]);
            }
        } elseif (preg_match('#^(\d{4})/(\d+)(?:/(.*))?#i', $action, $match)) {
            if (isset($match[3])) {
                $action = $match[3];
            } else {
                $action = '';
            }
            $request->setParam($yearParamName, $match[1]);
            $request->setParam($weekParamName, $match[2]);
        } elseif (preg_match('#^(\d{4})(?:/(.*))?#i', $action, $match)) {
            if (isset($match[2])) {
                $action = $match[2];
            } else {
                $action = '';
            }
            $request->setParam($yearParamName, $match[1]);
        }

        return $action;
    }

    /**
     * Builds the URL component for a date.
     * Outputs <year>/<month> or <year>/<month>/<day>.
     *
     * @param integer $integer
     * @param string $title
     * @param boolean $romanize
     *            If true, non-latin strings are romanized
     *
     * @return string
     */
    public function buildDateUrlComponent($year, $month = '', $day = '', $week = '')
    {
        $months = Helper_Date::getMonthsOfTheYear();
        $months = array_map('strtolower', $months);

        if ($week && $day) {
            $time = strtotime($year . '-W' . $week);
            for ($i = 0; $i < 7; $i ++) {
                $newTime = $time + ($i - 1) * 24 * 60 * 60;
                if (date('j', $newTime) == $day) {
                    $week = '';
                    $month = date('n', $newTime);
                }
            }
        }

        if ($month && $day) {
            return $months[$month] . '/' . $day . '/' . $year;
        }

        if ($month) {
            return $months[$month] . '/' . $year;
        }

        if ($week) {
            return 'week-' . $week . '/' . $year;
        }

        return $year;
    }

    /**
     * Builds a basic link for a request that may have an integer param.
     * Output will be in the format [prefix]/[title].[int]/[action]/ or similar,
     * based on whether the correct values in data are set.
     *
     * @param string $prefix
     *            Link prefix
     * @param string $action
     *            Link action
     * @param string $extension
     *            Link extension (for content type)
     * @param mixed $data
     *            Specific data to link to. If available, an array or an
     *            object that implements ArrayAccess
     * @param mixed $extraParams
     *            Extra data
     * @param string $yearField
     *            The name of the field that holds the year
     *            identifier
     * @param string $monthField
     *            If there is a month field, the name of the
     *            field that holds the month identifier
     * @param string $dayField
     *            If there is a day field, the name of the field
     *            that holds the day identifier
     * @param string $weekField
     *            If there is a week field, the name of the field
     *            that holds the week identifier
     *
     * @return false string if no data is provided, the link otherwise
     */
    public function buildBasicLinkWithDateParam($prefix, $action, $extension, $data, $intField = '', $titleField = '', &$extraParams, $yearField = 'year', $monthField = 'month', $dayField = 'day', $weekField = 'week')
    {
        if (((is_array($data) || $data instanceof \ArrayAccess) && $intField && ! empty($data[$intField])) || ((is_array($extraParams) || $extraParams instanceof \ArrayAccess) && ($yearField && ! empty($extraParams[$yearField])))) {
            \XenForo_Link::prepareExtensionAndAction($extension, $action);

            if ($intField && ! empty($data[$intField])) {
                $title = (($titleField && ! empty($data[$titleField])) ? $data[$titleField] : '');
                $intComponent = \XenForo_Link::buildIntegerAndTitleUrlComponent($data[$intField], $title) . '/';
            } else {
                $intComponent = '';
            }

            if ($yearField && ! empty($extraParams[$yearField])) {
                $year = $extraParams[$yearField];
                $month = (($monthField && ! empty($extraParams[$monthField])) ? $extraParams[$monthField] : '');
                $day = (($dayField && ! empty($extraParams[$dayField])) ? $extraParams[$dayField] : '');
                $week = (($weekField && ! empty($extraParams[$weekField])) ? $extraParams[$weekField] : '');
                unset($extraParams[$yearField], $extraParams[$monthField], $extraParams[$dayField], $extraParams[$weekField]);
                $dateComponent = self::buildDateUrlComponent($year, $month, $day, $week) . '/';
            } else {
                $dateComponent = '';
            }

            return $prefix . '/' . $intComponent . $dateComponent . $action . $extension;
        } else {
            return false;
        }
    }
}
