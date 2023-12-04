<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Fixtures;

use TYPO3\CMS\Core\Resource\Driver\DriverInterface;

class FalDriverFixture implements DriverInterface
{
    public function processConfiguration(): void
    {
    }

    public function setStorageUid($storageUid): void
    {
    }

    public function initialize(): void
    {
    }

    public function getCapabilities(): void
    {
    }

    public function mergeConfigurationCapabilities($capabilities): void
    {
    }

    public function hasCapability($capability): void
    {
    }

    public function isCaseSensitiveFileSystem(): void
    {
    }

    public function sanitizeFileName($fileName, $charset = ''): void
    {
    }

    public function hashIdentifier($identifier): void
    {
    }

    public function getRootLevelFolder(): void
    {
    }

    public function getDefaultFolder(): void
    {
    }

    public function getParentFolderIdentifierOfIdentifier($fileIdentifier): void
    {
    }

    public function getPublicUrl($identifier): void
    {
    }

    public function createFolder($newFolderName, $parentFolderIdentifier = '', $recursive = false): void
    {
    }

    public function renameFolder($folderIdentifier, $newName): void
    {
    }

    public function deleteFolder($folderIdentifier, $deleteRecursively = false): void
    {
    }

    public function fileExists($fileIdentifier): void
    {
    }

    public function folderExists($folderIdentifier): void
    {
    }

    public function isFolderEmpty($folderIdentifier): void
    {
    }

    public function addFile($localFilePath, $targetFolderIdentifier, $newFileName = '', $removeOriginal = true): void
    {
    }

    public function createFile($fileName, $parentFolderIdentifier): void
    {
    }

    public function copyFileWithinStorage($fileIdentifier, $targetFolderIdentifier, $fileName): void
    {
    }

    public function renameFile($fileIdentifier, $newName): void
    {
    }

    public function replaceFile($fileIdentifier, $localFilePath): void
    {
    }

    public function deleteFile($fileIdentifier): void
    {
    }

    public function hash($fileIdentifier, $hashAlgorithm): void
    {
    }

    public function moveFileWithinStorage($fileIdentifier, $targetFolderIdentifier, $newFileName): void
    {
    }

    public function moveFolderWithinStorage($sourceFolderIdentifier, $targetFolderIdentifier, $newFolderName): void
    {
    }

    public function copyFolderWithinStorage($sourceFolderIdentifier, $targetFolderIdentifier, $newFolderName): void
    {
    }

    public function getFileContents($fileIdentifier): void
    {
    }

    public function setFileContents($fileIdentifier, $contents): void
    {
    }

    public function fileExistsInFolder($fileName, $folderIdentifier): void
    {
    }

    public function folderExistsInFolder($folderName, $folderIdentifier): void
    {
    }

    public function getFileForLocalProcessing($fileIdentifier, $writable = true): void
    {
    }

    public function getPermissions($identifier): void
    {
    }

    public function dumpFileContents($identifier): void
    {
    }

    public function isWithin($folderIdentifier, $identifier): void
    {
    }

    public function getFileInfoByIdentifier($fileIdentifier, array $propertiesToExtract = []): void
    {
    }

    public function getFolderInfoByIdentifier($folderIdentifier): void
    {
    }

    public function getFileInFolder($fileName, $folderIdentifier): void
    {
    }

    public function getFilesInFolder(
        $folderIdentifier,
        $start = 0,
        $numberOfItems = 0,
        $recursive = false,
        array $filenameFilterCallbacks = [],
        $sort = '',
        $sortRev = false
    ): void {
    }

    public function getFolderInFolder($folderName, $folderIdentifier): void
    {
    }

    public function getFoldersInFolder(
        $folderIdentifier,
        $start = 0,
        $numberOfItems = 0,
        $recursive = false,
        array $folderNameFilterCallbacks = [],
        $sort = '',
        $sortRev = false
    ): void {
    }

    public function countFilesInFolder($folderIdentifier, $recursive = false, array $filenameFilterCallbacks = []): void
    {
    }

    public function countFoldersInFolder(
        $folderIdentifier,
        $recursive = false,
        array $folderNameFilterCallbacks = []
    ): void {
    }
}
