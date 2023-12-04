<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\EventListener;

use Netlogix\Nximageoptimizer\EventListener\WebpCreator;
use Netlogix\Nximageoptimizer\Tests\Unit\Fixtures\ContainerFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class WebpCreatorTest extends UnitTestCase
{
    #[Test]
    public function createWebpVersionShouldNotCreateWebpImageIfDisabledViaConfiguration(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([
            'disableAutomaticWebpCreation' => 1
        ]));

        $subject = $this->getAccessibleMock(WebpCreator::class, ['createWebpImage']);

        $subject->expects(self::never())
            ->method('createWebpImage');

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $this->createMock(ProcessedFile::class),
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->createWebpVersion($afterFileProcessingEvent);
    }

    #[Test]
    public function createWebpVersionShouldNotCreateWebpImageIfNotEnabled(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([
            'disableAutomaticWebpCreation' => 0
        ]));

        $subject = $this->getAccessibleMock(WebpCreator::class, ['isEnabled', 'createWebpImage']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $subject->expects(self::never())
            ->method('createWebpImage');

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $this->createMock(ProcessedFile::class),
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->createWebpVersion($afterFileProcessingEvent);
    }

    #[Test]
    public function createWebpVersionShouldNotCreateWebpImageIfSourceImageTypeIsNotSupported(): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([
            'disableAutomaticWebpCreation' => 0
        ]));

        $subject = $this->getAccessibleMock(WebpCreator::class, ['isEnabled', 'createWebpImage']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $subject->expects(self::never())
            ->method('createWebpImage');

        $processedFile = $this->createMock(ProcessedFile::class);
        $processedFile->expects(self::once())
            ->method('getMimeType')
            ->willReturn('image/tiff');

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $processedFile,
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->createWebpVersion($afterFileProcessingEvent);
    }

    #[DataProvider('mimeTypeDataProvider')]
    #[Test]
    public function createWebpVersionShouldNotCreateWebpImageIfCwebpIsNotInstalled(string $mimeType): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([
            'disableAutomaticWebpCreation' => 0
        ]));

        $subject = $this->getAccessibleMock(WebpCreator::class, ['isEnabled', 'createWebpImage', 'isCwebpInstalled']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $subject->expects(self::once())
            ->method('isCwebpInstalled')
            ->willReturn(false);

        $subject->expects(self::never())
            ->method('createWebpImage');

        $processedFile = $this->createMock(ProcessedFile::class);
        $processedFile->expects(self::once())
            ->method('getMimeType')
            ->willReturn($mimeType);

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $this->createMock(DriverInterface::class),
            $processedFile,
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('warning')
            ->with('Command "cwebp" not found. This is needed for generating WebP files');

        $subject->setLogger($logger);

        $subject->createWebpVersion($afterFileProcessingEvent);
    }

    #[DataProvider('mimeTypeDataProvider')]
    #[Test]
    public function createWebpVersionShouldCreateWebpImage(string $mimeType): void
    {
        $container = $this->initContainer();
        $container->set(ExtensionConfiguration::class, $this->getExtensionConfigurationMock([
            'disableAutomaticWebpCreation' => 0
        ]));

        $subject = $this->getAccessibleMock(WebpCreator::class, ['isEnabled', 'createWebpImage', 'isCwebpInstalled']);

        $subject->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $subject->expects(self::once())
            ->method('isCwebpInstalled')
            ->willReturn(true);

        $driver = $this->createMock(DriverInterface::class);

        $processedFile = $this->createMock(ProcessedFile::class);
        $processedFile->expects(self::once())
            ->method('getMimeType')
            ->willReturn('image/jpeg');

        $subject->expects(self::once())
            ->method('createWebpImage')
            ->with($processedFile, $driver);

        $afterFileProcessingEvent = new AfterFileProcessingEvent(
            $driver,
            $processedFile,
            $this->createMock(FileInterface::class),
            '',
            []
        );

        $subject->createWebpVersion($afterFileProcessingEvent);
    }

    public static function mimeTypeDataProvider(): array
    {
        return [
            ['image/jpeg'],
            ['image/png']
        ];
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
