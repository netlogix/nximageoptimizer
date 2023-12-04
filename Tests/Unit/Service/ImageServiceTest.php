<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Service;

use Netlogix\Nximageoptimizer\Service\ImageService;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Service\EnvironmentService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ImageServiceTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    protected ImageService $subject;

    protected function setUp(): void
    {
        $this->subject = new ImageService($this->createMock(ResourceFactory::class));

        parent::setUp();
    }

    #[Test]
    public function itAddsInterlaceParameterToImages(): void
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage): ProcessedFile {
                self::assertStringContainsString('-interlace JPEG', $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

    protected function createMockProcessedFile(): ProcessedFile
    {
        $res = $this->createMock(ProcessedFile::class);
        $res
            ->expects(self::any())
            ->method('getOriginalFile')
            ->willReturn($this->createMock(File::class));

        $res
            ->expects(self::any())
            ->method('getPublicUrl')
            ->willReturn('https://www.example.com/' . uniqid());

        return $res;
    }

    #[Test]
    public function itAddsInterlaceParameterToPngImages(): void
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage): ProcessedFile {
                self::assertStringContainsString('-interlace PNG', $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $mockImage->expects(self::any())->method('getMimeType')->willReturn('image/png');

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

    #[Test]
    public function itAddsQualityParameterToImages(): void
    {
        $desiredQuality = random_int(10, 100);
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] = $desiredQuality;

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use (
                $mockImage,
                $desiredQuality
            ): ProcessedFile {
                self::assertStringContainsString('-quality ' . $desiredQuality, $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

    #[Test]
    public function itAddsColorspaceParameterToImages(): void
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage): ProcessedFile {
                self::assertStringContainsString('-colorspace sRGB', $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $this->subject->applyProcessingInstructions($mockImage, []);
    }
}
