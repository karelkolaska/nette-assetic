<?php

namespace KarelKolaska\NetteAssetic;

use Assetic\AssetManager,
	Assetic\AssetWriter;
use Nette\Utils\Strings,
	Nette\Utils\Html;

/**
 * Asset Renderer
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class AssetRenderer
{	
	/** @var AssetManager */
	protected $assetManager;

	/** @var AssetWriter */
	protected $assetWriter;

	/** @var string */
	protected $wwwDir;
	
	/** @var type */
	protected $assets;

	/**
	 * 
	 * @param string $wwwDir
	 * @param AssetManager $assetManager
	 * @param AssetWriter $assetWriter
	 */
	public function __construct($wwwDir, AssetManager $assetManager, AssetWriter $assetWriter)
	{
		$this->wwwDir = $wwwDir;
		$this->assetManager = $assetManager;
		$this->assetWriter = $assetWriter;
	}
	
	/**
	 * 
	 * @param string $assets
	 */
	public function setAssets($assets)
	{
		if (is_array($assets)) {
			$this->assets = $assets;
		} else {
			foreach(explode(',', $assets) as $asset) {
				$this->assets[] = trim($asset);
			}
		}
	}
	
	/**
	 * 
	 * 
	 */
	public function renderHtml()
	{
		foreach($this->assets as $assetName) {
			$asset = $this->assetManager->get($assetName);			
			$assetPath = $this->wwwDir . $asset->getTargetPath();
			
			if (!file_exists($assetPath) || (file_exists($assetPath) && $asset->getLastModified() > filemtime($assetPath))) {
				$this->assetWriter->writeAsset($asset);
			}			
			
			if (Strings::endsWith($asset->getTargetPath(), '.js')) {
				echo Html::el('script')->type('text/javascript')->src($asset->getTargetPath() . '?v=' . $asset->getLastModified());
			}
			
			if (Strings::endsWith($asset->getTargetPath(), '.css')) {
				echo Html::el('link')->rel('stylesheet')->href($asset->getTargetPath() . '?v=' . $asset->getLastModified());
			}
		}
	}
}
