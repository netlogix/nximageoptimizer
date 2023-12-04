<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\EventListener;

use Netlogix\Nximageoptimizer\EventListener\ImageOptimizer;
use Netlogix\Nximageoptimizer\Tests\Unit\Fixtures\ContainerFixture;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ImageOptimizerTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    #[Test]
    public function constructShouldSetConfiguration(): void
    {
        $configuration = [
            'disableAutomaticWebpCreation' => 0,
        ];

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock($configuration));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        self::assertEquals($configuration, $subject->_get('configuration'));
    }

    #[Test]
    public function isEnableShouldReturnFalseIfNotFrontendApplicationType(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::exactly(1))
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        $processedFile = $this->createMock(ProcessedFile::class);

        self::assertFalse($subject->_call('isEnabled', $processedFile));
    }

    #[Test]
    public function isEnableShouldReturnFalseIfIsBackendImagePreview(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::exactly(1))
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        $processedFile = $this->createMock(ProcessedFile::class);

        $processedFile->expects(self::once())
            ->method('getTaskIdentifier')
            ->willReturn(ProcessedFile::CONTEXT_IMAGEPREVIEW);

        self::assertFalse($subject->_call('isEnabled', $processedFile));
    }

    #[Test]
    public function isEnableShouldReturnFalseIfImageIsNotUpdated(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::exactly(1))
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        $processedFile = $this->createMock(ProcessedFile::class);

        $processedFile->expects(self::once())
            ->method('getTaskIdentifier')
            ->willReturn(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK);

        $processedFile->expects(self::once())
            ->method('isUpdated')
            ->willReturn(false);

        self::assertFalse($subject->_call('isEnabled', $processedFile));
    }

    #[Test]
    public function isEnableShouldReturnFalseIfFileTypeIsNotImage(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::exactly(1))
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        $processedFile = $this->createMock(ProcessedFile::class);

        $processedFile->expects(self::once())
            ->method('getTaskIdentifier')
            ->willReturn(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK);

        $processedFile->expects(self::once())
            ->method('isUpdated')
            ->willReturn(true);

        $processedFile->expects(self::once())
            ->method('getType')
            ->willReturn(AbstractFile::FILETYPE_UNKNOWN);

        self::assertFalse($subject->_call('isEnabled', $processedFile));
    }

    #[Test]
    public function isEnableShouldReturnTrueIfAllConditionsAreFulfilled(): void
    {
        $serverRequest = $this->createMock(ServerRequestInterface::class);
        $serverRequest->expects(self::exactly(1))
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_FE);

        $GLOBALS['TYPO3_REQUEST'] = $serverRequest;

        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);

        $processedFile = $this->createMock(ProcessedFile::class);

        $processedFile->expects(self::once())
            ->method('getTaskIdentifier')
            ->willReturn(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK);

        $processedFile->expects(self::once())
            ->method('isUpdated')
            ->willReturn(true);

        $processedFile->expects(self::once())
            ->method('getType')
            ->willReturn(AbstractFile::FILETYPE_IMAGE);

        self::assertTrue($subject->_call('isEnabled', $processedFile));
    }

    #[Test]
    public function execShouldLogOutputToErrorLog(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error')
            ->with('sample output', [
                'command' => 'echo "sample output"',
            ]);

        $subject = $this->getAccessibleMock(ImageOptimizer::class, null);
        $subject->setLogger($logger);

        $subject->_call('exec', 'echo "sample output"');
    }

    #[Test]
    public function optimizeImageShouldNotProcessImageIfNotEnabled(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, ['isEnabled', 'processImage']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $subject->expects(self::never())
            ->method('processImage');

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $this->createMock(ProcessedFile::class),
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->optimizeImage($afterFileProcessingEvent);
    }

    #[Test]
    public function optimizeImageShouldProcessImageIfEnabled(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([]));

        $subject = $this->getAccessibleMock(ImageOptimizer::class, ['isEnabled', 'processImage']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $subject->expects(self::once())
            ->method('processImage');

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $this->createMock(ProcessedFile::class),
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->optimizeImage($afterFileProcessingEvent);
    }

    private function getExtensionConfigurationMock(array $configuration): ExtensionConfiguration
    {
        $extensionConfiguration = $this->createMock(ExtensionConfiguration::class);
        $extensionConfiguration->expects(self::once())
            ->method('get')
            ->with('nximageoptimizer')
            ->willReturn($configuration);

        return $extensionConfiguration;
    }

    private function initContainer(): ContainerFixture
    {
        $container = new ContainerFixture();
        GeneralUtility::setContainer($container);

        return $container;
    }
}
