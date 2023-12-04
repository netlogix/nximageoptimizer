<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\EventListener;

use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\CommandUtility;

class ImageOptimizer extends AbstractImageOptimizer
{
    public function optimizeImage(AfterFileProcessingEvent $event): void
    {
        if (!$this->isEnabled($event->getProcessedFile())) {
            return;
        }

        $this->processImage($event->getProcessedFile());
    }

    /**
     * Replaces the processed file with an optimized one.
     */
    private function processImage(ProcessedFile $processedFile): void
    {
        $localProcessingPath = $processedFile->getForLocalProcessing(true);

        $path = CommandUtility::escapeShellArgument(realpath($localProcessingPath));
        switch ($processedFile->getMimeType()) {
            case 'image/jpeg':
                if (CommandUtility::checkCommand('jpegoptim')) {
                    $command = CommandUtility::getCommand('jpegoptim');
                    $parameters = sprintf('-m85 --strip-all --all-progressive %s', $path);
                    $this->exec($command . ' ' . $parameters . ' 2>&1');
                }

                break;
            case 'image/png':
                if (CommandUtility::checkCommand('pngquant')) {
                    $command = CommandUtility::getCommand('pngquant');
                    $parameters = sprintf('--force %s --output=%s', $path, $path);
                    $this->exec($command . ' ' . $parameters . ' 2>&1');
                }

                if (CommandUtility::checkCommand('optipng')) {
                    $command = CommandUtility::getCommand('optipng');
                    $parameters = sprintf('-i0 -o2 -quiet %s', $path);
                    $this->exec($command . ' ' . $parameters . ' 2>&1');
                }

                break;
            case 'image/gif':
                if (CommandUtility::checkCommand('gifsicle')) {
                    $command = CommandUtility::getCommand('gifsicle');
                    $parameters = sprintf('-b -O3 %s', $path);
                    $this->exec($command . ' ' . $parameters . ' 2>&1');
                }

                break;
            case 'image/svg+xml':
                if (CommandUtility::checkCommand('svgo')) {
                    $command = CommandUtility::getCommand('svgo');
                    $parameters = sprintf('--disable=cleanupIDs %s', $path);
                    $this->exec($command . ' ' . $parameters . ' 2>&1');
                }

                break;
            default:
                break;
        }

        $processedFile->updateWithLocalFile($localProcessingPath);
    }
}
