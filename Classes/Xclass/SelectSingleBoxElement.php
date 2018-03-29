<?php

namespace GeorgRinger\BackendDebug\Xclass;

use GeorgRinger\BackendDebug\Utility\FormElementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SelectSingleBoxElement extends \TYPO3\CMS\Backend\Form\Element\SelectSingleBoxElement
{

    /**
     * Renders a single <option> element
     *
     * @param string $value The option value
     * @param string $label The option label
     * @param array $attributes Map of attribute names and values
     * @return string
     */
    protected function renderOptionElement($value, $label, array $attributes = [])
    {
        $attributes['value'] = $value;
        $html = [
            '<option ' . GeneralUtility::implodeAttributes($attributes, true) . '>',
            htmlspecialchars(FormElementUtility::appendValueToLabelInDebugMode($label, $value), ENT_COMPAT, 'UTF-8', false),
            '</option>'

        ];

        return implode('', $html);
    }

}