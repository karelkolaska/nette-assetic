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
	private $assetManager;

	/** @var AssetWriter */
	private $assetWriter;	
	
	/** @var type */
	private $assets;

	/**
	 * 
	 * @param AssetManager $assetManager
	 * @param AssetWriter $assetWriter
	 */
	public function __construct(AssetManager $assetManager, AssetWriter $assetWriter)
	{
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

			if (!file_exists($asset->getTargetPath()) || (file_exists($asset->getTargetPath()) && $asset->getLastModified() > filemtime($asset->getTargetPath()))) {
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
