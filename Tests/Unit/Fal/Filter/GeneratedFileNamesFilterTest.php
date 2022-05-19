<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Fal\Filter;

use Netlogix\Nximageoptimizer\Fal\Filter\GeneratedFileNamesFilter;
use Netlogix\Nximageoptimizer\Tests\Unit\Fixtures\FalDriverFixture;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;

class GeneratedFileNamesFilterTest extends UnitTestCase
{


    /**
     * @test
     * @dataProvider imageDataProvider
     * @return void
     */
    public function itAllowsKnownImageFiles(string $imagePath) {
        $driverMock = $this->createMock(LocalDriver::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertTrue($res);
    }
    /**
     * @test
     * @dataProvider blockedImageDataProvider
     * @return void
     */
    public function itRemovesGeneratedImageFiles(string $imagePath) {
        $driverMock = $this->createMock(LocalDriver::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertEquals(-1, $res);
    }

    /**
     * @dataProvider blockedImageDataProvider
     * @test
     * @return void
     */
    public function itPassesBlockedFilesForNonLocalDriver(string $imagePath) {
        $driverMock = $this->createMock(FalDriverFixture::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertTrue($res);
    }


    public function imageDataProvider(): array
    {
        $allowedExtensions = 'gif,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg';

        $data = [];

        foreach (explode(',', $allowedExtensions) as $allowedExtension) {
            $data[$allowedExtension] = [uniqid() . '.' . $allowedExtension];

        }


        return $data;
    }


    public function blockedImageDataProvider(): array
    {
        $blockedExtensions = 'jpg.webp,jpeg.webp';

        $data = [];

        foreach (explode(',', $blockedExtensions) as $blockedExtension) {
            $data[$blockedExtension] = [uniqid() . '.' . $blockedExtension];

        }


        return $data;
    }
}