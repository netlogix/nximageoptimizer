<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Service;

use Netlogix\Nximageoptimizer\Service\ImageService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class ImageServiceTest extends UnitTestCase
{

    /**
     * @test
     * @return void
     */
    public function itAddsInterlaceParameterToImages()
    {
        $subject = new ImageService($this->createMock(ResourceFactory::class));

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
                self::assertStringContainsString('-interlace JPEG', $configuration['additionalParameters']);

                return $this->createMock(ProcessedFile::class);
            });

        $subject->applyProcessingInstructions($mockImage, []);
    }

    /**
     * @test
     * @return void
     */
    public function itAddsInterlaceParameterToPngImages()
    {
        $subject = new ImageService($this->createMock(ResourceFactory::class));

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
                self::assertStringContainsString('-interlace PNG', $configuration['additionalParameters']);

                return $this->createMock(ProcessedFile::class);
            });

        $mockImage->expects(self::any())->method('getMimeType')->willReturn('image/png');

        $subject->applyProcessingInstructions($mockImage, []);
    }

    /**
     * @test
     * @return void
     */
    public function itAddsQualityParameterToImages()
    {
        $desiredQuality = rand(10, 100);
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] = $desiredQuality;

        $subject = new ImageService($this->createMock(ResourceFactory::class));

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage, $desiredQuality) {
                self::assertStringContainsString('-quality ' . $desiredQuality, $configuration['additionalParameters']);

                return $this->createMock(ProcessedFile::class);
            });

        $subject->applyProcessingInstructions($mockImage, []);
    }

    /**
     * @test
     * @return void
     */
    public function itAddsColorspaceParameterToImages()
    {
        $subject = new ImageService($this->createMock(ResourceFactory::class));

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
                self::assertStringContainsString('-colorspace sRGB', $configuration['additionalParameters']);

                return $this->createMock(ProcessedFile::class);
            });

        $subject->applyProcessingInstructions($mockImage, []);
    }

}
