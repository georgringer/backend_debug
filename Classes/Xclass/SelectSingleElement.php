<?php

namespace GeorgRinger\BackendDebug\Xclass;

use GeorgRinger\BackendDebug\Utility\FormElementUtility;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;
use TYPO3\CMS\Backend\Form\Utility\FormEngineUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class SelectSingleElement extends \TYPO3\CMS\Backend\Form\Element\SelectSingleElement
{


    /**
     * Render single element
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();

        $table = $this->data['tableName'];
        $field = $this->data['fieldName'];
        $row = $this->data['databaseRow'];
        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];

        $selectItems = $parameterArray['fieldConf']['config']['items'];

        // Check against inline uniqueness
        /** @var InlineStackProcessor $inlineStackProcessor */
        $inlineStackProcessor = GeneralUtility::makeInstance(InlineStackProcessor::class);
        $inlineStackProcessor->initializeByGivenStructure($this->data['inlineStructure']);
        $uniqueIds = null;
        if ($this->data['isInlineChild'] && $this->data['inlineParentUid']) {
            $inlineObjectName = $inlineStackProcessor->getCurrentStructureDomObjectIdPrefix($this->data['inlineFirstPid']);
            $inlineFormName = $inlineStackProcessor->getCurrentStructureFormPrefix();
            if ($this->data['inlineParentConfig']['foreign_table'] === $table
                && $this->data['inlineParentConfig']['foreign_unique'] === $field
            ) {
                $uniqueIds = $this->data['inlineData']['unique'][$inlineObjectName . '-' . $table]['used'];
                $parameterArray['fieldChangeFunc']['inlineUnique'] = 'inline.updateUnique(this,'
                    . GeneralUtility::quoteJSvalue($inlineObjectName . '-' . $table) . ','
                    . GeneralUtility::quoteJSvalue($inlineFormName) . ','
                    . GeneralUtility::quoteJSvalue($row['uid']) . ');';
            }
            // hide uid of parent record for symmetric relations
            if ($this->data['inlineParentConfig']['foreign_table'] === $table
                && (
                    $this->data['inlineParentConfig']['foreign_field'] === $field
                    || $this->data['inlineParentConfig']['symmetric_field'] === $field
                )
            ) {
                $uniqueIds[] = $this->data['inlineParentUid'];
            }
        }

        // Initialization:
        $selectId = StringUtility::getUniqueId('tceforms-select-');
        $selectedIcon = '';
        $size = (int)$config['size'];

        // Style set on <select/>
        $options = '';
        $disabled = false;
        if (!empty($config['readOnly'])) {
            $disabled = true;
        }

        // Prepare groups
        $selectItemCounter = 0;
        $selectItemGroupCount = 0;
        $selectItemGroups = [];
        $selectedValue = '';
        $hasIcons = false;

        if (!empty($parameterArray['itemFormElValue'])) {
            $selectedValue = (string)$parameterArray['itemFormElValue'][0];
        }

        foreach ($selectItems as $item) {
            if ($item[1] === '--div--') {
                // IS OPTGROUP
                if ($selectItemCounter !== 0) {
                    $selectItemGroupCount++;
                }
                $selectItemGroups[$selectItemGroupCount]['header'] = [
                    'title' => $item[0],
                ];
            } else {
                // IS ITEM
                $icon = !empty($item[2]) ? FormEngineUtility::getIconHtml($item[2], $item[0], $item[0]) : '';
                $selected = $selectedValue === (string)$item[1];

                if ($selected) {
                    $selectedIcon = $icon;
                }

                $selectItemGroups[$selectItemGroupCount]['items'][] = [
                    'title' => FormElementUtility::appendValueToLabelInDebugMode($item[0], $item[1]),
                    'value' => $item[1],
                    'icon' => $icon,
                    'selected' => $selected,
                ];
                $selectItemCounter++;
            }
        }

        // Fallback icon
        // @todo: assign a special icon for non matching values?
        if (!$selectedIcon && $selectItemGroups[0]['items'][0]['icon']) {
            $selectedIcon = $selectItemGroups[0]['items'][0]['icon'];
        }

        // Process groups
        foreach ($selectItemGroups as $selectItemGroup) {
            // suppress groups without items
            if (empty($selectItemGroup['items'])) {
                continue;
            }

            $optionGroup = is_array($selectItemGroup['header']);
            $options .= ($optionGroup ? '<optgroup label="' . htmlspecialchars($selectItemGroup['header']['title'], ENT_COMPAT, 'UTF-8', false) . '">' : '');

            if (is_array($selectItemGroup['items'])) {
                foreach ($selectItemGroup['items'] as $item) {
                    $options .= '<option value="' . htmlspecialchars($item['value']) . '" data-icon="' .
                        htmlspecialchars($item['icon']) . '"'
                        . ($item['selected'] ? ' selected="selected"' : '') . '>' . htmlspecialchars($item['title'], ENT_COMPAT, 'UTF-8', false) . '</option>';
                }
                $hasIcons = !empty($item['icon']);
            }

            $options .= ($optionGroup ? '</optgroup>' : '');
        }

        $selectAttributes = [
            'id' => $selectId,
            'name' => $parameterArray['itemFormElName'],
            'data-formengine-validation-rules' => $this->getValidationDataAsJsonString($config),
            'class' => 'form-control form-control-adapt',
        ];
        if ($size) {
            $selectAttributes['size'] = $size;
        }
        if ($disabled) {
            $selectAttributes['disabled'] = 'disabled';
        }

        $legacyWizards = $this->renderWizards();
        $legacyFieldWizardHtml = implode(LF, $legacyWizards['fieldWizard']);

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        $fieldWizardResult = $this->renderFieldWizard();
        $fieldWizardHtml = $legacyFieldWizardHtml . $fieldWizardResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        if (!$disabled) {
            $html[] = $fieldInformationHtml;
        }
        $html[] =   '<div class="form-control-wrap">';
        $html[] =       '<div class="form-wizards-wrap">';
        $html[] =           '<div class="form-wizards-element">';
        if ($hasIcons) {
            $html[] =           '<div class="input-group">';
            $html[] =               '<span class="input-group-addon input-group-icon">';
            $html[] =                   $selectedIcon;
            $html[] =               '</span>';
        }
        $html[] =                   '<select ' . GeneralUtility::implodeAttributes($selectAttributes, true) . '>';
        $html[] =                       $options;
        $html[] =                   '</select>';
        if ($hasIcons) {
            $html[] =           '</div>';
        }
        $html[] =           '</div>';
        if (!$disabled) {
            $html[] =       '<div class="form-wizards-items-bottom">';
            $html[] =           $fieldWizardHtml;
            $html[] =       '</div>';
        }
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';

        $resultArray['requireJsModules'][] = ['TYPO3/CMS/Backend/FormEngine/Element/SelectSingleElement' => implode(LF, [
            'function(SelectSingleElement) {',
            'require([\'jquery\'], function($) {',
            '$(function() {',
            'SelectSingleElement.initialize(',
            GeneralUtility::quoteJSvalue('#' . $selectId) . ',',
            '{',
            'onChange: function() {',
            implode('', $parameterArray['fieldChangeFunc']),
            '}',
            '}',
            ');',
            '});',
            '});',
            '}',
        ])];

        $resultArray['html'] = implode(LF, $html);
        return $resultArray;
    }

}