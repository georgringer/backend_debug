<?php

namespace GeorgRinger\BackendDebug\Xclass;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class PaletteAndSingleContainer extends \TYPO3\CMS\Backend\Form\Container\PaletteAndSingleContainer {

    /**
     * Wrap a single element
     *
     * @param array $element Given element as documented above
     * @param array $additionalPaletteClasses Additional classes to be added to HTML
     * @return string Wrapped element
     */
    protected function wrapSingleFieldContentWithLabelAndOuterDiv(array $element, array $additionalPaletteClasses = [])
    {
        $fieldName = $element['fieldName'];

        $paletteFieldClasses = [
            'form-group',
            't3js-formengine-validation-marker',
            't3js-formengine-palette-field',
        ];
        foreach ($additionalPaletteClasses as $class) {
            $paletteFieldClasses[] = $class;
        }

        $label = BackendUtility::wrapInHelp($this->data['tableName'], $fieldName, htmlspecialchars($element['fieldLabel']));

        if ($this->getBackendUser()->isAdmin()) {
            $label .= '<code>[' . htmlspecialchars($fieldName) . ']</code>';
        }
        $content = [];
        $content[] = '<div class="' . implode(' ', $paletteFieldClasses) . '">';
        $content[] =    '<label class="t3js-formengine-label">';
        $content[] =        $label;
        $content[] =    '</label>';
        $content[] =    $element['fieldHtml'];
        $content[] = '</div>';

        return implode(LF, $content);
    }

    /**
     * Returns the current BE user.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
