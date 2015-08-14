<?php
namespace Netlogix\Nximageoptimizer\Resource\Service;

use Netlogix\Nximageoptimizer\Resource\Processing\OptimizeImageTask;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Service\FileProcessingService;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImageOptimizer implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \TYPO3\CMS\Core\Resource\ProcessedFileRepository
	 * @inject
	 */
	protected $processedFileRepository;

	/**
	 * @var \Netlogix\Nximageoptimizer\Resource\Processing\OptimizeImageProcessor
	 */
	protected $optimizeImageProcessor;

	/**
	 * @param FileProcessingService $fileProcessingService
	 * @param DriverInterface $driver
	 * @param ProcessedFile $processedFile
	 * @param File $file
	 * @param $taskType
	 * @param array $configuration
	 */
	public function optimizeImage(FileProcessingService $fileProcessingService, DriverInterface $driver, ProcessedFile $processedFile, File $file, $taskType, array $configuration = []) {
		if ($processedFile->getType() !== AbstractFile::FILETYPE_IMAGE) {
			return;
		}
		$optimizedProcessedFile = $this->processedFileRepository->findOneByOriginalFileAndTaskTypeAndConfiguration($file, $this->getTaskTypeForExtension($processedFile->getExtension()), $configuration);
		if (!$optimizedProcessedFile->isProcessed()) {
			$this->process($optimizedProcessedFile, $processedFile);
		}

		// This is a hack because we can not return the optimized file
		if ($optimizedProcessedFile->isProcessed() && $optimizedProcessedFile->getIdentifier() !== '') {
			$processedFile->setName($optimizedProcessedFile->getName());
		}
	}

	/**
	 * @param string $fileExtension
	 * @return string
	 */
	protected function getTaskTypeForExtension($fileExtension) {
		$taskType = 'Image.CropScaleMask';
		switch ($fileExtension) {
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'svg':
				$taskType = 'Optimize.Image';
				break;
			default:
				break;
		}

		return $taskType;
	}

	/**
	 * Processes the file
	 *
	 * @param ProcessedFile $optimizedProcessedFile
	 * @param ProcessedFile $processedFile
	 */
	protected function process(ProcessedFile $optimizedProcessedFile, ProcessedFile $processedFile) {
		if ($optimizedProcessedFile->isNew() || !$optimizedProcessedFile->exists() || $optimizedProcessedFile->isOutdated()) {
			/** @var OptimizeImageTask $task */
			$task = $optimizedProcessedFile->getTask();
			if ($processedFile->isProcessed() && !$processedFile->usesOriginalFile()) {
				$task->setOriginalProcessedFile($processedFile);
			}
			$this->getOptimizeImageProcessor()->processTask($task);

			if ($task->isExecuted() && $task->isSuccessful() && $optimizedProcessedFile->isProcessed()) {
				$this->processedFileRepository->add($optimizedProcessedFile);
			}
		}
	}

	protected function getOptimizeImageProcessor() {
		if ($this->optimizeImageProcessor === NULL) {
			$this->optimizeImageProcessor = GeneralUtility::makeInstance('Netlogix\\Nximageoptimizer\\Resource\\Processing\\OptimizeImageProcessor');
		}

		return $this->optimizeImageProcessor;
	}

}

