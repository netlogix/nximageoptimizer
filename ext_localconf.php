<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {

	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
	$signalSlotDispatcher->connect(
		\TYPO3\CMS\Core\Resource\ResourceStorage::class,
		\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
		\Netlogix\Nximageoptimizer\Service\ImageOptimizer::class,
		'optimizeImage'
	);

	// Extend ImageService to force image processing (https://forge.typo3.org/issues/59067)
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Service\ImageService::class] = array(
		'className' => \Netlogix\Nximageoptimizer\Service\ImageService::class
	);

	$GLOBALS['TYPO3_CONF_VARS']['LOG']['Netlogix']['Nximageoptimizer']['writerConfiguration'] = [
		\TYPO3\CMS\Core\Log\LogLevel::ERROR => [
			'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => [
				'logFile' => 'typo3temp/var/logs/nximageoptimizer.log'
			]
		],
	];

});
