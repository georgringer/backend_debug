<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Backend debug',
    'description' => 'Some support for backend debugging',
    'category' => 'be',
    'author' => 'Georg Ringer',
    'state' => 'beta',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.0.0-9.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'GeorgRinger\\BackendDebug\\' => 'Classes'
        ]
    ],
];
