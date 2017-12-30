<?php

namespace Netlogix\Nximageoptimizer\Service;

use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Service\FileProcessingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;

class ImageOptimizer implements SingletonInterface
{

	/**
	 * @param FileProcessingService $fileProcessingService
	 * @param DriverInterface $driver
	 * @param ProcessedFile $processedFile
	 * @param File $file
	 * @param $taskType
	 * @param array $configuration
	 */
	public function optimizeImage(
		FileProcessingService $fileProcessingService,
		DriverInterface $driver,
		ProcessedFile $processedFile,
		File $file,
		$taskType,
		array $configuration = []
	) {
		if ($processedFile->getType() === AbstractFile::FILETYPE_IMAGE && $processedFile->isUpdated()) {
			$this->processImage($processedFile);
		}
	}

	/**
	 * @param ProcessedFile $processedFile
	 */
	protected function processImage(ProcessedFile $processedFile)
	{
		$path = CommandUtility::escapeShellArgument(realpath($processedFile->getForLocalProcessing(false)));
		switch ($processedFile->getExtension()) {
			case 'jpg':
			case 'jpeg':
				if (CommandUtility::checkCommand('jpegoptim')) {
					$command = CommandUtility::getCommand('jpegoptim');
					$parameters = sprintf('-m85 --strip-all --all-progressive %s', $path);
					CommandUtility::exec($command . ' ' . $parameters);
				}
				break;
			case 'png':
				if (CommandUtility::checkCommand('pngquant')) {
					$command = CommandUtility::getCommand('pngquant');
					$parameters = sprintf('--force %s --output=%s', $path, $path);
					CommandUtility::exec($command . ' ' . $parameters);
				}
				if (CommandUtility::checkCommand('optipng')) {
					$command = CommandUtility::getCommand('optipng');
					$parameters = sprintf('-i0 -o2 -quiet %s', $path);
					CommandUtility::exec($command . ' ' . $parameters);
				}
				break;
			case 'gif':
				if (CommandUtility::checkCommand('gifsicle')) {
					$command = CommandUtility::getCommand('gifsicle');
					$parameters = sprintf('-b -O3 %s', $path);
					CommandUtility::exec($command . ' ' . $parameters);
					return;
				}
				break;
			case 'svg':
				if (CommandUtility::checkCommand('svgo') === false) {
					$command = CommandUtility::getCommand('svgo');
					$parameters = sprintf('--disable=cleanupIDs %s', $path);
					CommandUtility::exec($command . ' ' . $parameters);
				}
				break;
			default:
				return;
		}

	}

}

