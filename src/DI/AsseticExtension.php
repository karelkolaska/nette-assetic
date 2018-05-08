<?php

namespace KarelKolaska\NetteAssetic\DI;

use Nette\DI\Statement,
	Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

/**
 * Assetic Extension
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class AsseticExtension extends CompilerExtension
{
	/** @var array */
	private static $configDefaults = [
		'wwwDir' => '%wwwDir%',		
		'debug' => '%debugMode%',
		'output' => '%tempPrefix%/*',		
		'filters' => [
			'less' => 'Assetic\Filter\LessphpFilter',
			'cssmin' => 'Assetic\Filter\CssMinFilter',
			'jsmin' => 'Assetic\Filter\JSMinFilter'
		],
		'workers' => []
	];

	/**
	 * 
	 * 
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$configDefaults);
		
		// Asset Manager
		$builder->addDefinition($this->prefix('assetManager'))
			->setClass('Assetic\AssetManager');

		// Asset Factory
		$builder->addDefinition($this->prefix('assetFactory'))
			->setClass('Assetic\Factory\AssetFactory', [$config['wwwDir']])
			->addSetup('setAssetManager')
			->addSetup('setFilterManager')
			->addSetup('setDefaultOutput', [$config['output']])
			->addSetup('setDebug', [$config['debug']]);

		// Asset Writer
		$builder->addDefinition($this->prefix('assetWriter'))
			->setClass('Assetic\AssetWriter', [$config['wwwDir']]);
		
		// Asset Renderer
		$builder->addDefinition($this->prefix('assetRenderer'))
			->setClass('KarelKolaska\NetteAssetic\AssetRenderer');

		// Filter Manager
		$builder->addDefinition($this->prefix('filterManager'))
			->setClass('Assetic\FilterManager');		
		
		// Filters		
		foreach ($config['filters'] as $name => $filter) {			
			$builder->getDefinition($this->prefix('filterManager'))
				->addSetup('set', [$name, new Statement($filter)]);
		}				
		
		// Workers		
		foreach ($config['workers'] as $name => $worker) {			
			$builder->getDefinition($this->prefix('assetFactory'))
				->addSetup('addWorker', [new Statement($worker)]);
		}		
		
		// Latte macro & filter
		$builder->addDefinition($this->prefix('latte.assetMacro'))
			->setClass('KarelKolaska\NetteAssetic\Latte\AssetMacro');

		$builder->addDefinition($this->prefix('latte.assetFilter'))
			->setClass('KarelKolaska\NetteAssetic\Latte\AssetFilter');

		$builder->getDefinition('nette.latteFactory')
			->addSetup('addMacro', ['assets', '@' . $this->prefix('latte.assetMacro')])
			->addSetup('addFilter', ['_assets', '@' . $this->prefix('latte.assetFilter')]);		
	}
	
	/**
	 * 
	 * @param ClassType $class
	 */
	public function afterCompile(ClassType $class)
	{
		$config = $this->getConfig(self::$configDefaults);
		$initialize = $class->methods['initialize'];
		
		foreach($config['assets'] as $name => $asset) {
			$initialize->addBody('$asset = $this->getService(?)->createAsset(?, ?, ?);', [
				$this->prefix('assetFactory'),
				$asset['files'],
				isset($asset['filters']) ? $asset['filters'] : [],
				['name' => $name]
			]);
			
			if (isset($asset['output'])) {
				$initialize->addBody('$asset->setTargetPath(?);', [$asset['output']]);
			}

			$initialize->addBody('$this->getService(?)->set(?, $asset);', [
				$this->prefix('assetManager'),
				$name
			]);			
		}
	}
}
