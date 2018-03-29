<?php

namespace GeorgRinger\BackendDebug\Xclass;

use GeorgRinger\BackendDebug\Utility\FormElementUtility;

class CheckboxElement extends \TYPO3\CMS\Backend\Form\Element\CheckboxElement
{

    /**
     * This functions builds the HTML output for the checkbox
     *
     * @param string $label Label of this item
     * @param int $itemCounter Number of this element in the list of all elements
     * @param int $formElementValue Value of this element
     * @param int $numberOfItems Full number of items
     * @param array $additionalInformation Information with additional configuration options.
     * @param bool $disabled TRUE if form element is disabled
     * @return string Single element HTML
     */
    protected function renderSingleCheckboxElement($label, $itemCounter, $formElementValue, $numberOfItems, $additionalInformation, $disabled)
    {
        $config = $additionalInformation['fieldConf']['config'];
        $inline = !empty($config['cols']) && $config['cols'] === 'inline';
        $checkboxParameters = $this->checkBoxParams(
            $additionalInformation['itemFormElName'],
            $formElementValue,
            $itemCounter,
            $numberOfItems,
            implode('', $additionalInformation['fieldChangeFunc'])
        );
        $checkboxId = $additionalInformation['itemFormElID'] . '_' . $itemCounter;
        // Xclass start
        return '
			<div class="checkbox' . ($inline ? ' checkbox-inline' : '') . (!$disabled ? '' : ' disabled') . '">
				<label>
					<input type="checkbox"
						value="1"
						data-formengine-input-name="' . htmlspecialchars($additionalInformation['itemFormElName']) . '"
						' . $checkboxParameters . '
						' . (!$disabled ?: ' disabled="disabled"') . '
						id="' . $checkboxId . '" />
					' . FormElementUtility::appendValueToLabelInDebugMode(($label ? htmlspecialchars($label) : '&nbsp;'), $formElementValue) . '
				</label>
			</div>';
        // Xclass end
    }

}