<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\EventListener;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractImageOptimizer implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected array $configuration;

    public function __construct()
    {
        $this->configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('nximageoptimizer');
    }

    protected function isEnabled(ProcessedFile $processedFile): bool
    {
        // this is needed for backwards compatibility
        // @extensionScannerIgnoreLine
        if ((TYPO3_REQUESTTYPE & ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) === 0) {
            // this is not needed for TYPO3 backend and would break deferred image processing
            return false;
        }

        // Backend introduced a DeferredBackendImageProcessor and creates thumbnails async.
        // Currently, there is no api to check if the image is processed asynchronously, therefore we disable processing for backend preview images.
        // https://forge.typo3.org/issues/92188
        // https://forge.typo3.org/issues/93245
        if ($processedFile->getTaskIdentifier() === ProcessedFile::CONTEXT_IMAGEPREVIEW) {
            return false;
        }

        // if the processed file did not change then there is nothing to do here
        if (!$processedFile->isUpdated()) {
            return false;
        }

        // only optimize images here
        return $processedFile->getType() === AbstractFile::FILETYPE_IMAGE;
    }

    protected function exec(string $command): void
    {
        $output = null;
        $returnValue = null;
        $lastOutputLine = CommandUtility::exec($command, $output, $returnValue);
        $this->logger->error($lastOutputLine, [
            'command' => $command,
        ]);
    }
}
