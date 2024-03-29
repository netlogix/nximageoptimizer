<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\EventListener;

use PHPUnit\Framework\Attributes\CodeCoverageIgnore;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WebpCreator extends AbstractImageOptimizer
{
    public function createWebpVersion(AfterFileProcessingEvent $event): void
    {
        if ($this->configuration['disableAutomaticWebpCreation']) {
            return;
        }

        if (!$this->isEnabled($event->getProcessedFile())) {
            return;
        }

        // stop processing if source image type is not supported
        if (!in_array($event->getProcessedFile()->getMimeType(), ['image/jpeg', 'image/png'], true)) {
            return;
        }

        // stop processing if required command is not present
        if (!$this->isCwebpInstalled()) {
            $this->logger->warning('Command "cwebp" not found. This is needed for generating WebP files');

            return;
        }

        $this->createWebpImage($event->getProcessedFile(), $event->getDriver());
    }

    #[CodeCoverageIgnore]
    protected function isCwebpInstalled(): bool
    {
        return (bool) CommandUtility::checkCommand('cwebp');
    }

    /**
     * Creates a .webp version of the processed image file.
     */
    protected function createWebpImage(ProcessedFile $processedFile, DriverInterface $driver): void
    {
        $webpFileName = $processedFile->getName() . '.webp';
        $targetFolder = $processedFile->getParentFolder();

        $originalProcessingPath = $processedFile->getForLocalProcessing();

        $path = realpath($originalProcessingPath);
        $webpTempPath = $path . '.webp';

        $output = $webpTempPath;
        $command = CommandUtility::getCommand('cwebp');
        $parameters = sprintf(
            '-q 85 %s -o %s',
            CommandUtility::escapeShellArgument($path),
            CommandUtility::escapeShellArgument($output)
        );
        $this->exec($command . ' ' . $parameters . ' 2>&1');

        // the source temp file is not needed anymore
        GeneralUtility::unlink_tempfile($originalProcessingPath);

        if ($targetFolder->hasFile($webpFileName)) {
            $webpFileIdentifier = $driver->getFileInFolder($webpFileName, $targetFolder->getIdentifier());
            $driver->replaceFile($webpFileIdentifier, $webpTempPath);
        } else {
            $driver->addFile($webpTempPath, $targetFolder->getIdentifier(), $webpFileName);
        }
    }
}
