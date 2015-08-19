<?php
namespace Netlogix\Nximageoptimizer\Resource\Processing;

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Processing\AbstractGraphicalTask;

class OptimizeImageTask extends AbstractGraphicalTask {

	/**
	 * @var string
	 */
	protected $type = 'Image';

	/**
	 * @var string
	 */
	protected $name = 'OptimizeImage';

	/**
	 * @var ProcessedFile
	 */
	protected $originalProcessedFile;

	/**
	 * @return string
	 */
	public function getTargetFileName() {
		return 'opt_' . parent::getTargetFilename();
	}

	/**
	 * @inheritdoc
	 */
	protected function isValidConfiguration(array $configuration) {
		// TODO: Implement isValidConfiguration() method.
	}

	/**
	 * @inheritdoc
	 */
	public function fileNeedsProcessing() {
		// TODO: Implement fileNeedsProcessing() method.
	}

	/**
	 * @return ProcessedFile
	 */
	public function hasOriginalProcessedFile() {
		return !!$this->originalProcessedFile;
	}

	/**
	 * @return ProcessedFile
	 */
	public function getOriginalProcessedFile() {
		return $this->originalProcessedFile;
	}

	/**
	 * @param ProcessedFile $originalProcessedFile
	 */
	public function setOriginalProcessedFile($originalProcessedFile) {
		$this->originalProcessedFile = $originalProcessedFile;
	}

}

