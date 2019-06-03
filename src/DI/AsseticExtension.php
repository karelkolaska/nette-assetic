<?php

declare(strict_types=1);

namespace KarelKolaska\NetteAssetic\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use Nette\DI\Statement;
use Nette\PhpGenerator\ClassType;
use Nette\Schema\Context;

/**
 * Assetic Extension
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class AsseticExtension extends CompilerExtension
{
	/**
	 * @param ContainerBuilder $builder
	 * @return array
	 */
	public function getConfigDefaults(ContainerBuilder $builder) : array
	{
		return array_merge([
			'filters' => [
				'less' => 'Assetic\Filter\LessphpFilter',
				'cssmin' => 'Assetic\Filter\CssMinFilter',
				'jsmin' => 'Assetic\Filter\JSMinFilter'
			],
			'rebuildAssets' => false,
			'workers' => []
		], $builder->parameters);
	}

	/**
	 *
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfigSchema()->merge($this->config, $this->getConfigDefaults($builder));

		// Asset Manager
		$builder->addDefinition($this->prefix('assetManager'))
			->setFactory('Assetic\AssetManager');

		// Asset Factory
		$builder->addDefinition($this->prefix('assetFactory'))
			->setFactory('Assetic\Factory\AssetFactory', [$config['wwwDir']])
			->addSetup('setAssetManager')
			->addSetup('setFilterManager')
			->addSetup('setDefaultOutput', [$config['wwwDir']])
			->addSetup('setDebug', [$config['debugMode']]);

		// Asset Writer
		$builder->addDefinition($this->prefix('assetWriter'))
			->setFactory('Assetic\AssetWriter', [$config['wwwDir']]);

		// Asset Renderer
		$builder->addDefinition($this->prefix('assetRenderer'))
			->setFactory('KarelKolaska\NetteAssetic\AssetRenderer', [
				$config['wwwDir'],
				$config['rebuildAssets'],
			]);

		// Filter Manager
		$builder->addDefinition($this->prefix('filterManager'))
			->setFactory('Assetic\FilterManager');
		
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
			->setFactory('KarelKolaska\NetteAssetic\Latte\AssetMacro');

		$builder->addDefinition($this->prefix('latte.assetFilter'))
			->setFactory('KarelKolaska\NetteAssetic\Latte\AssetFilter');

		$builder->getDefinition('nette.latteFactory')
			->getResultDefinition()
				->addSetup('addMacro', ['assets', '@' . $this->prefix('latte.assetMacro')])
				->addSetup('addFilter', ['_assets', '@' . $this->prefix('latte.assetFilter')]);
	}
	
	/**
	 * 
	 * @param ClassType $class
	 */
	public function afterCompile(ClassType $class)
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfigSchema()->merge($this->config, $this->getConfigDefaults($builder));
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
