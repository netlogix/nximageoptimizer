<?php

use Netlogix\Nximageoptimizer\Fal\Filter\GeneratedFileNamesFilter;
use Netlogix\Nximageoptimizer\Service\ImageService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

defined('TYPO3') or die();

(function () {

    // Extend ImageService to force image processing (https://forge.typo3.org/issues/59067)
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Service\ImageService::class] = [
        'className' => ImageService::class
    ];

    $GLOBALS['TYPO3_CONF_VARS']['LOG']['Netlogix']['Nximageoptimizer']['writerConfiguration'] = [
        LogLevel::ERROR => [
            FileWriter::class => [
                'logFile' => Environment::getVarPath() . '/log/nximageoptimizer.log'
            ]
        ],
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['defaultFilterCallbacks'][GeneratedFileNamesFilter::class] = [
        GeneratedFileNamesFilter::class,
        'filterGeneratedFiles'
    ];

})();
