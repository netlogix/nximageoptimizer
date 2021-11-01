<?php
declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\EventListener;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImageOptimizer implements SingletonInterface
{

    protected Logger $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
    }

    public function optimizeImage(AfterFileProcessingEvent $event)
    {
        // Backend introduced a DeferredBackendImageProcessor and creates thumbnails async.
        // Currently there is no api to check if the image is processed asynchronously, therefore we disable processing for backend preview images.
        // https://forge.typo3.org/issues/92188
        // https://forge.typo3.org/issues/93245
        $processedFile = $event->getProcessedFile();
        if ($processedFile->getTaskIdentifier() === ProcessedFile::CONTEXT_IMAGEPREVIEW) {
            return;
        }

        if ($processedFile->getType() === AbstractFile::FILETYPE_IMAGE && $processedFile->isUpdated()) {
            $this->createWebpImage($processedFile);
            $this->processImage($processedFile);
        }
    }

    private function processImage(ProcessedFile $processedFile): void
    {
        $path = CommandUtility::escapeShellArgument(realpath($processedFile->getForLocalProcessing(false)));
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
                return;
        }
    }

    private function createWebpImage(ProcessedFile $processedFile): void
    {
        $path = realpath($processedFile->getForLocalProcessing(false));
        switch ($processedFile->getMimeType()) {
            case 'image/jpeg':
            case 'image/png':
                if (CommandUtility::checkCommand('cwebp')) {
                    $output = $path . '.webp';
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
    }

    private function exec(string $command): void
    {
        $lastOutputLine = CommandUtility::exec($command, $output, $returnValue);
        if ($returnValue !== 0) {
            $this->logger->error($lastOutputLine, ['command' => $command]);
        }
    }

}

