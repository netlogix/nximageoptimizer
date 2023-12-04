<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Service;

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\MathUtility;

class ImageService extends \TYPO3\CMS\Extbase\Service\ImageService
{
    public function applyProcessingInstructions($image, array $processingInstructions): ProcessedFile
    {
        $quality = MathUtility::forceIntegerInRange($GLOBALS['TYPO3_CONF_VARS']['GFX']['jpg_quality'], 10, 100, 75);
        $processingInstructions['additionalParameters'] ??= '';
        if ($image->getMimeType() === 'image/png') {
            $processingInstructions['additionalParameters'] .= ' -interlace PNG -colorspace sRGB -quality ' . $quality;
        } else {
            $processingInstructions['additionalParameters'] .= ' -interlace JPEG -colorspace sRGB -quality ' . $quality;
        }

        return parent::applyProcessingInstructions($image, $processingInstructions);
    }
}
