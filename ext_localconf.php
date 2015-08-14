<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
	'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
	\TYPO3\CMS\Core\Resource\Service\FileProcessingService::SIGNAL_PostFileProcess,
	'Netlogix\\Nximageoptimizer\\Resource\\Service\\ImageOptimizer',
	'optimizeImage'
);
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fal']['processingTaskTypes']['Optimize.Image'] = 'Netlogix\\Nximageoptimizer\\Resource\\Processing\\OptimizeImageTask';