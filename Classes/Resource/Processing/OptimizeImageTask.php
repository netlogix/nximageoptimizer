<?php
namespace Netlogix\Nximageoptimizer\Resource\Processing;

use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\Processing\AbstractTask;

class OptimizeImageTask extends AbstractTask {

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
		return 'nlx_' . $this->getSourceFile()->getNameWithoutExtension() . '_' . $this->getConfigurationChecksum() . '.' . $this->getTargetFileExtension();
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

