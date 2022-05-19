<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Service;

use Netlogix\Nximageoptimizer\Service\ImageService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Service\EnvironmentService;

class ImageServiceTest extends UnitTestCase
{
    protected ImageService $subject;

    public function setUp(): void
    {
        parent::setUp();

        switch ((new Typo3Version())->getMajorVersion()) {
            case 10:
                $this->subject = new ImageService(
                    $this->createMock(EnvironmentService::class),
                    $this->createMock(ResourceFactory::class)
                );
                break;
            default:
                $this->subject = new ImageService($this->createMock(ResourceFactory::class));
                break;
        }
    }

    /**
     * @test
     * @return void
     */
    public function itAddsInterlaceParameterToImages()
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
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

    /**
     * @test
     * @return void
     */
    public function itAddsInterlaceParameterToPngImages()
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
                self::assertStringContainsString('-interlace PNG', $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $mockImage->expects(self::any())->method('getMimeType')->willReturn('image/png');

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

    /**
     * @test
     * @return void
     */
    public function itAddsQualityParameterToImages()
    {
        $desiredQuality = rand(10, 100);
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'] = $desiredQuality;

        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage, $desiredQuality) {
                self::assertStringContainsString('-quality ' . $desiredQuality, $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

    /**
     * @test
     * @return void
     */
    public function itAddsColorspaceParameterToImages()
    {
        $mockImage = $this->createMock(File::class);
        $mockImage
            ->expects(self::once())
            ->method('process')
            ->willReturnCallback(function ($taskType, array $configuration) use ($mockImage) {
                self::assertStringContainsString('-colorspace sRGB', $configuration['additionalParameters']);

                return $this->createMockProcessedFile();
            });

        $this->subject->applyProcessingInstructions($mockImage, []);
    }

}
