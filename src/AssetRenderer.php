<?php

declare(strict_types=1);

namespace KarelKolaska\NetteAssetic;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Nette\Utils\Html;
use Nette\Utils\Strings;

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

	/** @var bool */
	protected $rebuildAssets;
	
	/** @var type */
	protected $assets;

	/**
	 * @param string $wwwDir
	 * @param AssetManager $assetManager
	 * @param AssetWriter $assetWriter
	 */
	public function __construct($wwwDir, $rebuildAssets, AssetManager $assetManager, AssetWriter $assetWriter)
	{
		$this->wwwDir = $wwwDir;
		$this->rebuildAssets = $rebuildAssets;
		$this->assetManager = $assetManager;
		$this->assetWriter = $assetWriter;
	}

	/**
	 * @param array|string $assets
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
	 */
	public function renderHtml()
	{
		if ($this->rebuildAssets) {
			$this->assetWriter->writeManagerAssets($this->assetManager);
		}

		foreach($this->assets as $assetName) {
			$asset = $this->assetManager->get($assetName);
			if (Strings::endsWith($asset->getTargetPath(), '.js')) {
				echo Html::el('script')->type('text/javascript')->src('/' . $asset->getTargetPath() . '?v=' . $asset->getLastModified());
			}
			if (Strings::endsWith($asset->getTargetPath(), '.css')) {
				echo Html::el('link')->rel('stylesheet')->href('/' . $asset->getTargetPath() . '?v=' . $asset->getLastModified());
			}
		}
	}
}
