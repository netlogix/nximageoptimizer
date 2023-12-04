<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Fal\Filter;

use Netlogix\Nximageoptimizer\Fal\Filter\GeneratedFileNamesFilter;
use Netlogix\Nximageoptimizer\Tests\Unit\Fixtures\FalDriverFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class GeneratedFileNamesFilterTest extends UnitTestCase
{
    #[Test]
    #[DataProvider('imageDataProvider')]
    public function itAllowsKnownImageFiles(string $imagePath): void
    {
        $driverMock = $this->createMock(LocalDriver::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertTrue($res);
    }

    #[Test]
    #[DataProvider('blockedImageDataProvider')]
    public function itRemovesGeneratedImageFiles(string $imagePath): void
    {
        $driverMock = $this->createMock(LocalDriver::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertEquals(-1, $res);
    }

    #[Test]
    #[DataProvider('blockedImageDataProvider')]
    public function itPassesBlockedFilesForNonLocalDriver(string $imagePath): void
    {
        $driverMock = $this->createMock(FalDriverFixture::class);

        $res = GeneratedFileNamesFilter::filterGeneratedFiles($imagePath, '', '', [], $driverMock);

        self::assertTrue($res);
    }

    public static function imageDataProvider(): array
    {
        $allowedExtensions = 'gif,tif,tiff,bmp,pcx,tga,png,pdf,ai,svg';

        $data = [];

        foreach (explode(',', $allowedExtensions) as $allowedExtension) {
            $data[$allowedExtension] = [uniqid() . '.' . $allowedExtension];
        }

        return $data;
    }

    public static function blockedImageDataProvider(): array
    {
        $blockedExtensions = 'jpg.webp,jpeg.webp';

        $data = [];

        foreach (explode(',', $blockedExtensions) as $blockedExtension) {
            $data[$blockedExtension] = [uniqid() . '.' . $blockedExtension];
        }

        return $data;
    }
}
