<?php

namespace GeorgRinger\BackendDebug\Xclass;

use GeorgRinger\BackendDebug\Utility\FormElementUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class SelectMultileSideBySideElement extends \TYPO3\CMS\Backend\Form\Element\SelectMultipleSideBySideElement
{


    /**
     * Render side by side element.
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $languageService = $this->getLanguageService();
        $resultArray = $this->initializeResultArray();

        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        $elementName = $parameterArray['itemFormElName'];

        if ($config['readOnly']) {
            // Early return for the relatively simple read only case
            return $this->renderReadOnly();
        }

        $possibleItems = $config['items'];
        $selectedItems = $parameterArray['itemFormElValue'] ?: [];
        $selectedItemsCount = count($selectedItems);

        $maxItems = $config['maxitems'];
        $autoSizeMax = MathUtility::forceIntegerInRange($config['autoSizeMax'], 0);
        $size = 2;
        if (isset($config['size'])) {
            $size = (int)$config['size'];
        }
        if ($autoSizeMax >= 1) {
            $size = MathUtility::forceIntegerInRange($selectedItemsCount + 1, MathUtility::forceIntegerInRange($size, 1), $autoSizeMax);
        }
        $itemCanBeSelectedMoreThanOnce = !empty($config['multiple']);

        $listOfSelectedValues = [];
        $selectedItemsHtml = [];
        foreach ($selectedItems as $itemValue) {
            foreach ($possibleItems as $possibleItem) {
                if ($possibleItem[1] == $itemValue) {
                    $title = $possibleItem[0];
                    $listOfSelectedValues[] = $itemValue;
                    $selectedItemsHtml[] = '<option value="' . htmlspecialchars($itemValue) . '" title="' . htmlspecialchars($title) . '">' . htmlspecialchars(FormElementUtility::appendValueToLabelInDebugMode($title, $itemValue)) . '</option>';
                    break;
                }
            }
        }

        $selectableItemsHtml = [];
        foreach ($possibleItems as $possibleItem) {
            $disabledAttr = '';
            $classAttr = '';
            if (!$itemCanBeSelectedMoreThanOnce && in_array((string)$possibleItem[1], $selectedItems, true)) {
                $disabledAttr = ' disabled="disabled"';
                $classAttr = ' class="hidden"';
            }
            $selectableItemsHtml[] =
                '<option value="'
                . htmlspecialchars($possibleItem[1])
                . '" title="' . htmlspecialchars($possibleItem[0]) . '"'
                . $classAttr . $disabledAttr
                . '>'
                . htmlspecialchars(FormElementUtility::appendValueToLabelInDebugMode($possibleItem[0], $possibleItem[1])) .
                '</option>';
        }

        // Html stuff for filter and select filter on top of right side of multi select boxes
        $filterTextfield = [];
        if ($config['enableMultiSelectFilterTextfield']) {
            $filterTextfield[] = '<span class="input-group input-group-sm">';
            $filterTextfield[] =    '<span class="input-group-addon">';
            $filterTextfield[] =        '<span class="fa fa-filter"></span>';
            $filterTextfield[] =    '</span>';
            $filterTextfield[] =    '<input class="t3js-formengine-multiselect-filter-textfield form-control" value="">';
            $filterTextfield[] = '</span>';
        }
        $filterDropDownOptions = [];
        if (isset($config['multiSelectFilterItems']) && is_array($config['multiSelectFilterItems']) && count($config['multiSelectFilterItems']) > 1) {
            foreach ($config['multiSelectFilterItems'] as $optionElement) {
                $value = $languageService->sL($optionElement[0]);
                $label = $value;
                if (isset($optionElement[1]) && trim($optionElement[1]) !== '') {
                    $label = $languageService->sL($optionElement[1]);
                }
                $filterDropDownOptions[] = '<option value="' . htmlspecialchars($value) . '">' . htmlspecialchars($label) . '</option>';
            }
        }
        $filterHtml = [];
        if (!empty($filterTextfield) || !empty($filterDropDownOptions)) {
            $filterHtml[] = '<div class="form-multigroup-item-wizard">';
            if (!empty($filterTextfield) && !empty($filterDropDownOptions)) {
                $filterHtml[] = '<div class="t3js-formengine-multiselect-filter-container form-multigroup-wrap">';
                $filterHtml[] =     '<div class="form-multigroup-item form-multigroup-element">';
                $filterHtml[] =         '<select class="form-control input-sm t3js-formengine-multiselect-filter-dropdown">';
                $filterHtml[] =             implode(LF, $filterDropDownOptions);
                $filterHtml[] =         '</select>';
                $filterHtml[] =     '</div>';
                $filterHtml[] =     '<div class="form-multigroup-item form-multigroup-element">';
                $filterHtml[] =         implode(LF, $filterTextfield);
                $filterHtml[] =     '</div>';
                $filterHtml[] = '</div>';
            } elseif (!empty($filterTextfield)) {
                $filterHtml[] = implode(LF, $filterTextfield);
            } else {
                $filterHtml[] = '<select class="form-control input-sm t3js-formengine-multiselect-filter-dropdown">';
                $filterHtml[] =     implode(LF, $filterDropDownOptions);
                $filterHtml[] = '</select>';
            }
            $filterHtml[] = '</div>';
        }

        $classes = [];
        $classes[] = 'form-control';
        $classes[] = 'tceforms-multiselect';
        if ($maxItems === 1) {
            $classes[] = 'form-select-no-siblings';
        }
        $multipleAttribute = '';
        if ($maxItems !== 1 && $size !== 1) {
            $multipleAttribute = ' multiple="multiple"';
        }
        $selectedListStyle = '';
        if (isset($config['selectedListStyle'])) {
            GeneralUtility::deprecationLog('TCA property selectedListStyle is deprecated since TYPO3 v8 and will be removed in v9');
            $selectedListStyle = ' style="' . htmlspecialchars($config['selectedListStyle']) . '"';
        }
        $selectableListStyle = '';
        if (isset($config['itemListStyle'])) {
            GeneralUtility::deprecationLog('TCA property itemListStyle is deprecated since TYPO3 v8 and will be removed in v9');
            $selectableListStyle = ' style="' . htmlspecialchars($config['itemListStyle']) . '"';
        }

        $legacyWizards = $this->renderWizards();
        $legacyFieldControlHtml = implode(LF, $legacyWizards['fieldControl']);
        $legacyFieldWizardHtml = implode(LF, $legacyWizards['fieldWizard']);

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        $fieldControlResult = $this->renderFieldControl();
        $fieldControlHtml = $legacyFieldControlHtml . $fieldControlResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldControlResult, false);

        $fieldWizardResult = $this->renderFieldWizard();
        $fieldWizardHtml = $legacyFieldWizardHtml . $fieldWizardResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   $fieldInformationHtml;
        $html[] =   '<div class="form-wizards-wrap">';
        $html[] =       '<div class="form-wizards-element">';
        $html[] =           '<input type="hidden" data-formengine-input-name="' . htmlspecialchars($elementName) . '" value="' . (int)$itemCanBeSelectedMoreThanOnce . '" />';
        $html[] =           '<div class="form-multigroup-wrap t3js-formengine-field-group">';
        $html[] =               '<div class="form-multigroup-item form-multigroup-element">';
        $html[] =                   '<label>';
        $html[] =                       htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.selected'));
        $html[] =                   '</label>';
        $html[] =                   '<div class="form-wizards-wrap form-wizards-aside">';
        $html[] =                       '<div class="form-wizards-element">';
        $html[] =                           '<select';
        $html[] =                               ' id="' . StringUtility::getUniqueId('tceforms-multiselect-') . '"';
        $html[] =                               ' size="' . $size . '"';
        $html[] =                               ' class="' . implode(' ', $classes) . '"';
        $html[] =                               $multipleAttribute;
        $html[] =                               ' data-formengine-input-name="' . htmlspecialchars($elementName) . '"';
        $html[] =                               $selectedListStyle;
        $html[] =                           '>';
        $html[] =                               implode(LF, $selectedItemsHtml);
        $html[] =                           '</select>';
        $html[] =                       '</div>';
        $html[] =                       '<div class="form-wizards-items-aside">';
        $html[] =                           '<div class="btn-group-vertical">';
        if ($maxItems > 1 && $size >= 5) {
            $html[] =                           '<a href="#"';
            $html[] =                               ' class="btn btn-default t3js-btn-moveoption-top"';
            $html[] =                               ' data-fieldname="' . htmlspecialchars($elementName) . '"';
            $html[] =                               ' title="' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.move_to_top')) . '"';
            $html[] =                           '>';
            $html[] =                               $this->iconFactory->getIcon('actions-move-to-top', Icon::SIZE_SMALL)->render();
            $html[] =                           '</a>';
        }
        if ($maxItems > 1) {
            $html[] =                           '<a href="#"';
            $html[] =                               ' class="btn btn-default t3js-btn-moveoption-up"';
            $html[] =                               ' data-fieldname="' . htmlspecialchars($elementName) . '"';
            $html[] =                               ' title="' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.move_up')) . '"';
            $html[] =                           '>';
            $html[] =                               $this->iconFactory->getIcon('actions-move-up', Icon::SIZE_SMALL)->render();
            $html[] =                           '</a>';
            $html[] =                           '<a href="#"';
            $html[] =                               ' class="btn btn-default t3js-btn-moveoption-down"';
            $html[] =                               ' data-fieldname="' . htmlspecialchars($elementName) . '"';
            $html[] =                               ' title="' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.move_down')) . '"';
            $html[] =                           '>';
            $html[] =                               $this->iconFactory->getIcon('actions-move-down', Icon::SIZE_SMALL)->render();
            $html[] =                           '</a>';
        }
        if ($maxItems > 1 && $size >= 5) {
            $html[] =                           '<a href="#"';
            $html[] =                               ' class="btn btn-default t3js-btn-moveoption-bottom"';
            $html[] =                               ' data-fieldname="' . htmlspecialchars($elementName) . '"';
            $html[] =                               ' title="' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.move_to_bottom')) . '"';
            $html[] =                           '>';
            $html[] =                               $this->iconFactory->getIcon('actions-move-to-bottom', Icon::SIZE_SMALL)->render();
            $html[] =                           '</a>';
        }
        $html[] =                               '<a href="#"';
        $html[] =                                   ' class="btn btn-default t3js-btn-removeoption"';
        $html[] =                                   ' data-fieldname="' . htmlspecialchars($elementName) . '"';
        $html[] =                                   ' title="' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.remove_selected')) . '"';
        $html[] =                               '>';
        $html[] =                                   $this->iconFactory->getIcon('actions-selection-delete', Icon::SIZE_SMALL)->render();
        $html[] =                               '</a>';
        $html[] =                           '</div>';
        $html[] =                       '</div>';
        $html[] =                   '</div>';
        $html[] =               '</div>';
        $html[] =               '<div class="form-multigroup-item form-multigroup-element">';
        $html[] =                   '<label>';
        $html[] =                       htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.items'));
        $html[] =                   '</label>';
        $html[] =                   '<div class="form-wizards-wrap form-wizards-aside">';
        $html[] =                       '<div class="form-wizards-element">';
        $html[] =                           implode(LF, $filterHtml);
        $html[] =                           '<select';
        $html[] =                               ' data-relatedfieldname="' . htmlspecialchars($elementName) . '"';
        $html[] =                               ' data-exclusivevalues="' . htmlspecialchars($config['exclusiveKeys']) . '"';
        $html[] =                               ' id="' . StringUtility::getUniqueId('tceforms-multiselect-') . '"';
        $html[] =                               ' data-formengine-input-name="' . htmlspecialchars($elementName) . '"';
        $html[] =                               ' class="form-control t3js-formengine-select-itemstoselect"';
        $html[] =                               ' size="' . $size . '"';
        $html[] =                               ' onchange="' . htmlspecialchars(implode('', $parameterArray['fieldChangeFunc'])) . '"';
        $html[] =                               ' data-formengine-validation-rules="' . htmlspecialchars($this->getValidationDataAsJsonString($config)) . '"';
        $html[] =                               $selectableListStyle;
        $html[] =                           '>';
        $html[] =                               implode(LF, $selectableItemsHtml);
        $html[] =                           '</select>';
        $html[] =                       '</div>';
        $html[] =                       '<div class="form-wizards-items-aside">';
        $html[] =                           '<div class="btn-group-vertical">';
        $html[] =                               $fieldControlHtml;
        $html[] =                           '</div>';
        $html[] =                       '</div>';
        $html[] =                   '</div>';
        $html[] =               '</div>';
        $html[] =           '</div>';
        $html[] =           '<input type="hidden" name="' . htmlspecialchars($elementName) . '" value="' . htmlspecialchars(implode(',', $listOfSelectedValues)) . '" />';
        $html[] =       '</div>';
        $html[] =       '<div class="form-wizards-items-bottom">';
        $html[] =           $fieldWizardHtml;
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';

        $resultArray['html'] = implode(LF, $html);
        return $resultArray;
    }

}