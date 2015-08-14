<?php
namespace Netlogix\Nximageoptimizer\Resource\Processing;

use TYPO3\CMS\Core\Resource\Processing\ProcessorInterface;
use TYPO3\CMS\Core\Resource\Processing\TaskInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class OptimizeImageProcessor implements ProcessorInterface {

	/**
	 * @var array
	 */
	protected $settings = [];

	public function __construct() {
		$this->settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['nximageoptimizer']);
	}

	/**
	 * @inheritdoc
	 */
	public function canProcessTask(TaskInterface $task) {
		$canProcessTask = $task->getType() === 'Image' && $task->getName() === 'OptimizeImage';
		return $canProcessTask;
	}

	/**
	 * @inheritdoc
	 */
	public function processTask(TaskInterface $task) {
		if (!$this->canProcessTask($task)) {
			throw new \InvalidArgumentException('Cannot process task of type "' . $task->getType() . '.' . $task->getName() . '"', 1439552828);
		}
		try {
			$result = $this->process($task);
			if ($result === NULL) {
				$task->setExecuted(TRUE);
				$task->getTargetFile()->setUsesOriginalFile();
			} elseif (!empty($result['filePath']) && file_exists($result['filePath'])) {
				$task->setExecuted(TRUE);
				$imageDimensions = $this->getGraphicalFunctionsObject()->getImageDimensions($result['filePath']);
				$task->getTargetFile()->setName($task->getTargetFileName());
				$task->getTargetFile()->updateProperties(
					array('width' => $imageDimensions[0], 'height' => $imageDimensions[1], 'size' => filesize($result['filePath']), 'checksum' => $task->getConfigurationChecksum())
				);
				$task->getTargetFile()->updateWithLocalFile($result['filePath']);
				unlink($result['filePath'] . '.bak');
			} else {
				// Seems we have no valid processing result
				$task->setExecuted(FALSE);
			}
		} catch (\Exception $e) {
			$task->setExecuted(FALSE);
		}
	}

	/**
	 * @param TaskInterface $task
	 * @return array|null
	 */
	protected function process(TaskInterface $task) {

		$result = NULL;
		$sourceFile = $task->getOriginalProcessedFile() ?: $task->getSourceFile();
		$sourceFilePath = realpath($sourceFile->getForLocalProcessing(FALSE));
		$targetFilePath = realpath(GeneralUtility::tempnam('_processed_/nlx-tempfile-', '.' . $sourceFile->getExtension()));

		switch ($sourceFile->getExtension()) {
			case 'jpg':
			case 'jpeg':
				if ($this->settings['jpg.']['enabled'] === FALSE) {
					return $result;
				}
				$library = 'jpegtran';
				$arguments = sprintf('-copy none -optimize %s -outfile %s %s', $this->settings['jpg.']['progressive'] === TRUE ? '-progressive' : '', escapeshellarg($targetFilePath), escapeshellarg($sourceFilePath));
				break;
			case 'png':
				if ($this->settings['png.']['enabled'] === FALSE) {
					return $result;
				}
				$library = 'optipng';
				$arguments = sprintf('-o%u -strip all -fix -clobber -force -out %s %s', $this->settings['png.']['optimizationLevel'], escapeshellarg($targetFilePath), escapeshellarg($sourceFilePath));
				break;
			case 'gif':
				if ($this->settings['gif.']['enabled'] === FALSE) {
					return $result;
				}
				$library = 'gifsicle';
				$arguments = sprintf('--batch -O%u -o %s %s', $this->settings['gif.']['optimizationLevel'],  escapeshellarg($targetFilePath), escapeshellarg($sourceFilePath));
				break;
			case 'svg':
				if ($this->settings['svg.']['enabled'] === FALSE) {
					return $result;
				}
				$library = 'svgo';
				$arguments = sprintf('%s %s', escapeshellarg($sourceFilePath), escapeshellarg($targetFilePath));
				break;
			default:
				return $result;
		}

		$cmd = escapeshellcmd($library) . ' ' . $arguments;
		$output = [];
		exec($cmd, $output, $return);

		if ($return === 0) {
			$result = array(
				'filePath' => $targetFilePath,
			);
		}

		return $result;
	}

	/**
	 * @return \TYPO3\CMS\Core\Imaging\GraphicalFunctions
	 */
	protected function getGraphicalFunctionsObject() {
		static $graphicalFunctionsObject = NULL;

		if ($graphicalFunctionsObject === NULL) {
			$graphicalFunctionsObject = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions');
		}

		return $graphicalFunctionsObject;
	}
}

