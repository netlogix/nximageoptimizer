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
		$targetFileName = $processedFile->getName() . '.webp';
		$targetFolder = $processedFile->getParentFolder();

		$localProcessingPath = $processedFile->getForLocalProcessing();

		$path = realpath($localProcessingPath);
		$targetPath = $path . '.webp';
		switch ($processedFile->getMimeType()) {
			case 'image/jpeg':
			case 'image/png':
				if (CommandUtility::checkCommand('cwebp')) {
					$output = $targetPath;
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

		if ($targetFolder->hasFile($targetFileName)) {
			$fileIdentifier = $driver->getFileInFolder(
				$targetFileName,
				$targetFolder->getIdentifier()
			);
			$driver->replaceFile($fileIdentifier, $localProcessingPath);
		} else {
			$driver->addFile($localProcessingPath, $targetFolder->getIdentifier(), $targetFileName);
		}
	}

}

