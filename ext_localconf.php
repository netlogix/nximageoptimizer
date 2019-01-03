<?php
defined('TYPO3_MODE') or die();

(function () {

	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
	$signalSlotDispatcher->connect(
		\TYPO3\CMS\Core\Resource\ResourceStorage::class,
		\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
		\Netlogix\Nximageoptimizer\Service\ImageOptimizer::class,
		'optimizeImage'
	);

	// Extend ImageService to force image processing (https://forge.typo3.org/issues/59067)
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Service\ImageService::class] = [
		'className' => \Netlogix\Nximageoptimizer\Service\ImageService::class
	];

	$loggerConfiguration = [];
	if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 9002000) {
		$loggerConfiguration['logFile'] = \TYPO3\CMS\Core\Core\Environment::getVarPath() . '/log/nximageoptimizer.log';
	} else {
		$loggerConfiguration['logFile'] = 'typo3temp/var/logs/nximageoptimizer.log';
	}

	$GLOBALS['TYPO3_CONF_VARS']['LOG']['Netlogix']['Nximageoptimizer']['writerConfiguration'] = [
		\TYPO3\CMS\Core\Log\LogLevel::ERROR => [
			\TYPO3\CMS\Core\Log\Writer\FileWriter::class => $loggerConfiguration
		],
	];

})();
