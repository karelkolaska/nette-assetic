<?php

namespace KarelKolaska\NetteAssetic\Latte;

use KarelKolaska\NetteAssetic\AssetRenderer;

/**
 * Asset Filter
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class AssetFilter
{
	/** @var AssetRenderer */
	private $assetRenderer;

	public function __construct(AssetRenderer $assetRenderer)
	{
		$this->assetRenderer = $assetRenderer;
	}

	/**
	 * @param string $assets
	 * @return string
	 */
	public function __invoke($assets)
	{
		$this->assetRenderer->setAssets($assets);
		return $this->assetRenderer->renderHtml();	
	}
}