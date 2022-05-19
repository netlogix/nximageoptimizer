<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Fixtures;

use TYPO3\CMS\Core\Resource\Driver\DriverInterface;

class FalDriverFixture implements DriverInterface {
    public function processConfiguration()
    {
        // TODO: Implement processConfiguration() method.
    }

    public function setStorageUid($storageUid)
    {
        // TODO: Implement setStorageUid() method.
    }

    public function initialize()
    {
        // TODO: Implement initialize() method.
    }

    public function getCapabilities()
    {
        // TODO: Implement getCapabilities() method.
    }

    public function mergeConfigurationCapabilities($capabilities)
    {
        // TODO: Implement mergeConfigurationCapabilities() method.
    }

    public function hasCapability($capability)
    {
        // TODO: Implement hasCapability() method.
    }

    public function isCaseSensitiveFileSystem()
    {
        // TODO: Implement isCaseSensitiveFileSystem() method.
    }

    public function sanitizeFileName($fileName, $charset = '')
    {
        // TODO: Implement sanitizeFileName() method.
    }

    public function hashIdentifier($identifier)
    {
        // TODO: Implement hashIdentifier() method.
    }

    public function getRootLevelFolder()
    {
        // TODO: Implement getRootLevelFolder() method.
    }

    public function getDefaultFolder()
    {
        // TODO: Implement getDefaultFolder() method.
    }

    public function getParentFolderIdentifierOfIdentifier($fileIdentifier)
    {
        // TODO: Implement getParentFolderIdentifierOfIdentifier() method.
    }

    public function getPublicUrl($identifier)
    {
        // TODO: Implement getPublicUrl() method.
    }

    public function createFolder($newFolderName, $parentFolderIdentifier = '', $recursive = false)
    {
        // TODO: Implement createFolder() method.
    }

    public function renameFolder($folderIdentifier, $newName)
    {
        // TODO: Implement renameFolder() method.
    }

    public function deleteFolder($folderIdentifier, $deleteRecursively = false)
    {
        // TODO: Implement deleteFolder() method.
    }

    public function fileExists($fileIdentifier)
    {
        // TODO: Implement fileExists() method.
    }

    public function folderExists($folderIdentifier)
    {
        // TODO: Implement folderExists() method.
    }

    public function isFolderEmpty($folderIdentifier)
    {
        // TODO: Implement isFolderEmpty() method.
    }

    public function addFile($localFilePath, $targetFolderIdentifier, $newFileName = '', $removeOriginal = true)
    {
        // TODO: Implement addFile() method.
    }

    public function createFile($fileName, $parentFolderIdentifier)
    {
        // TODO: Implement createFile() method.
    }

    public function copyFileWithinStorage($fileIdentifier, $targetFolderIdentifier, $fileName)
    {
        // TODO: Implement copyFileWithinStorage() method.
    }

    public function renameFile($fileIdentifier, $newName)
    {
        // TODO: Implement renameFile() method.
    }

    public function replaceFile($fileIdentifier, $localFilePath)
    {
        // TODO: Implement replaceFile() method.
    }

    public function deleteFile($fileIdentifier)
    {
        // TODO: Implement deleteFile() method.
    }

    public function hash($fileIdentifier, $hashAlgorithm)
    {
        // TODO: Implement hash() method.
    }

    public function moveFileWithinStorage($fileIdentifier, $targetFolderIdentifier, $newFileName)
    {
        // TODO: Implement moveFileWithinStorage() method.
    }

    public function moveFolderWithinStorage($sourceFolderIdentifier, $targetFolderIdentifier, $newFolderName)
    {
        // TODO: Implement moveFolderWithinStorage() method.
    }

    public function copyFolderWithinStorage($sourceFolderIdentifier, $targetFolderIdentifier, $newFolderName)
    {
        // TODO: Implement copyFolderWithinStorage() method.
    }

    public function getFileContents($fileIdentifier)
    {
        // TODO: Implement getFileContents() method.
    }

    public function setFileContents($fileIdentifier, $contents)
    {
        // TODO: Implement setFileContents() method.
    }

    public function fileExistsInFolder($fileName, $folderIdentifier)
    {
        // TODO: Implement fileExistsInFolder() method.
    }

    public function folderExistsInFolder($folderName, $folderIdentifier)
    {
        // TODO: Implement folderExistsInFolder() method.
    }

    public function getFileForLocalProcessing($fileIdentifier, $writable = true)
    {
        // TODO: Implement getFileForLocalProcessing() method.
    }

    public function getPermissions($identifier)
    {
        // TODO: Implement getPermissions() method.
    }

    public function dumpFileContents($identifier)
    {
        // TODO: Implement dumpFileContents() method.
    }

    public function isWithin($folderIdentifier, $identifier)
    {
        // TODO: Implement isWithin() method.
    }

    public function getFileInfoByIdentifier($fileIdentifier, array $propertiesToExtract = [])
    {
        // TODO: Implement getFileInfoByIdentifier() method.
    }

    public function getFolderInfoByIdentifier($folderIdentifier)
    {
        // TODO: Implement getFolderInfoByIdentifier() method.
    }

    public function getFileInFolder($fileName, $folderIdentifier)
    {
        // TODO: Implement getFileInFolder() method.
    }

    public function getFilesInFolder(
        $folderIdentifier,
        $start = 0,
        $numberOfItems = 0,
        $recursive = false,
        array $filenameFilterCallbacks = [],
        $sort = '',
        $sortRev = false
    ) {
        // TODO: Implement getFilesInFolder() method.
    }

    public function getFolderInFolder($folderName, $folderIdentifier)
    {
        // TODO: Implement getFolderInFolder() method.
    }

    public function getFoldersInFolder(
        $folderIdentifier,
        $start = 0,
        $numberOfItems = 0,
        $recursive = false,
        array $folderNameFilterCallbacks = [],
        $sort = '',
        $sortRev = false
    ) {
        // TODO: Implement getFoldersInFolder() method.
    }

    public function countFilesInFolder($folderIdentifier, $recursive = false, array $filenameFilterCallbacks = [])
    {
        // TODO: Implement countFilesInFolder() method.
    }

    public function countFoldersInFolder($folderIdentifier, $recursive = false, array $folderNameFilterCallbacks = [])
    {
        // TODO: Implement countFoldersInFolder() method.
    }
}

