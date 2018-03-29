<?php

namespace GeorgRinger\BackendDebug\Xclass;

use GeorgRinger\BackendDebug\Utility\FormElementUtility;

class RadioElement extends \TYPO3\CMS\Backend\Form\Element\RadioElement
{

    /**
     * This will render a series of radio buttons.
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();

        $disabled = '';
        if ($this->data['parameterArray']['fieldConf']['config']['readOnly']) {
            $disabled = ' disabled';
        }

        $html = [];
        foreach ($this->data['parameterArray']['fieldConf']['config']['items'] as $itemNumber => $itemLabelAndValue) {
            $label =  $itemLabelAndValue[0];
            $value = $itemLabelAndValue[1];
            $radioId = htmlspecialchars($this->data['parameterArray']['itemFormElID'] . '_' . $itemNumber);
            $radioChecked = (string)$value === (string)$this->data['parameterArray']['itemFormElValue'] ? ' checked="checked"' : '';

            $fieldInformationResult = $this->renderFieldInformation();
            $fieldInformationHtml = $fieldInformationResult['html'];
            $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

            $fieldWizardResult = $this->renderFieldWizard();
            $fieldWizardHtml = $fieldWizardResult['html'];
            $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldWizardResult, false);

            $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
            if (!$disabled) {
                $html[] = $fieldInformationHtml;
            }
            $html[] =   '<div class="form-wizards-wrap">';
            $html[] =       '<div class="form-wizards-element">';
            $html[] =           '<div class="radio' . $disabled . '">';
            $html[] =               '<label for="' . $radioId . '">';
            $html[] =                   '<input type="radio"';
            $html[] =                       ' name="' . htmlspecialchars($this->data['parameterArray']['itemFormElName']) . '"';
            $html[] =                       ' id="' . $radioId . '"';
            $html[] =                       ' value="' . htmlspecialchars($value) . '"';
            $html[] =                       $radioChecked;
            $html[] =                       $disabled;
            $html[] =                       ' onclick="' . htmlspecialchars(implode('', $this->data['parameterArray']['fieldChangeFunc'])) . '"';
            $html[] =                   '/>';
            // Xclass start
            $html[] =                       htmlspecialchars(FormElementUtility::appendValueToLabelInDebugMode($label, $value));
            // Xclass end
            $html[] =               '</label>';
            $html[] =           '</div>';
            $html[] =       '</div>';
            if (!$disabled) {
                $html[] =   '<div class="form-wizards-items-bottom">';
                $html[] =       $fieldWizardHtml;
                $html[] =   '</div>';
            }
            $html[] =   '</div>';
            $html[] = '</div>';
        }

        $resultArray['html'] = implode(LF, $html);
        return $resultArray;
    }

}