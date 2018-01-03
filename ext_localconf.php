<?php
defined('TYPO3_MODE') or die();

$boot = function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\PaletteAndSingleContainer::class] = [
        'className' => \GeorgRinger\BackendDebug\Xclass\PaletteAndSingleContainer::class,
    ];
};

$boot();
unset($boot);
