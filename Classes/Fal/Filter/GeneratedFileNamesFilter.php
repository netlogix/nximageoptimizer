<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Fal\Filter;

use TYPO3\CMS\Core\Resource\Driver\DriverInterface;
use TYPO3\CMS\Core\Resource\Driver\LocalDriver;

class GeneratedFileNamesFilter
{
    public static function filterGeneratedFiles(
        string $itemName,
        string $itemIdentifier,
        string $parentIdentifier,
        array $additionalInformation,
        DriverInterface $driverInstance
    ) {
        if (!$driverInstance instanceof LocalDriver) {
            return true;
        }

        if (preg_match('%.+\.(jpe?g|png)\.webp$%i', $itemName)) {
            return -1;
        }

        return true;
    }
}
