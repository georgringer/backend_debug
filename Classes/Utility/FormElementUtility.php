<?php

namespace GeorgRinger\BackendDebug\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class FormElementUtility
{

    /**
     * Append the value of a form field to its label
     *
     * @param string|int $label The label which can also be an integer
     * @param string|int $value The value which can also be an integer
     * @return string|int
     */
    public static function appendValueToLabelInDebugMode($label, $value)
    {
        if ($value !== '' && self::getBackendUser()->isAdmin()) {
            return $label . ' [' . $value . ']';
        }

        return $label;
    }

    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication
     */
    protected static function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}