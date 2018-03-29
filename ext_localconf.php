<?php
defined('TYPO3_MODE') or die();

$boot = function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\PaletteAndSingleContainer::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\PaletteAndSingleContainer::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\CheckboxElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\CheckboxElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\GroupElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\GroupElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\RadioElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\RadioElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\SelectCheckBoxElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\SelectCheckBoxElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\SelectMultipleSideBySideElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\SelectMultileSideBySideElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\SelectSingleBoxElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\SelectSingleBoxElement::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Element\SelectSingleElement::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\SelectSingleElement::class,
    ];
};

$boot();
unset($boot);
