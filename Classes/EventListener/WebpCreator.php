<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\EventListener;

use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\CommandUtility;

class WebpCreator extends AbstractImageOptimizer
{

	public function createWebpVersion(AfterFileProcessingEvent $event)
	{
        if ($this->configuration['disableAutomaticWebpCreation']) {
            return;
        }

		if (!$this->isEnabled($event->getProcessedFile())) {
			return;
		}

		$this->createWebpImage($event->getProcessedFile(), $event->getDriver());
	}

	/**
	 * Creates a .webp version of the processed image file.
	 *
	 * @param ProcessedFile $processedFile
	 * @param DriverInterface $driver
	 * @return void
	 */
	private function createWebpImage(ProcessedFile $processedFile, DriverInterface $driver): void
	{
		$webpFileName = $processedFile->getName() . '.webp';
		$targetFolder = $processedFile->getParentFolder();

		$originalProcessingPath = $processedFile->getForLocalProcessing();

		$path = realpath($originalProcessingPath);
		$webpTempPath = $path . '.webp';
		switch ($processedFile->getMimeType()) {
			case 'image/jpeg':
			case 'image/png':
				if (CommandUtility::checkCommand('cwebp')) {
					$output = $webpTempPath;
					$command = CommandUtility::getCommand('cwebp');
					$parameters = sprintf(
						'-q 85 %s -o %s',
						CommandUtility::escapeShellArgument($path),
						CommandUtility::escapeShellArgument($output)
					);
					$this->exec($command . ' ' . $parameters . ' 2>&1');
				}
				break;
			default:
				return;
		}

		if ($targetFolder->hasFile($webpFileName)) {
			$webpFileIdentifier = $driver->getFileInFolder(
				$webpFileName,
				$targetFolder->getIdentifier()
			);
			$driver->replaceFile($webpFileIdentifier, $webpTempPath);
		} else {
			$driver->addFile($webpTempPath, $targetFolder->getIdentifier(), $webpFileName);
		}
	}

}

