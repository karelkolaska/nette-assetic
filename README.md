# KarelKolaska\NetteAssetic

Nette extension to manage assets via kriswallsmith/assetic. 

## Installation

Install KarelKolaska\NetteAssetic using [Composer](https://getcomposer.org/):

```
$ composer require karelkolaska/nette-assetic
```

## Configuration

Register extension in your NEON configuration:

```
extensions:
  	assetic:	KarelKolaska\NetteAssetic\DI\AsseticExtension
```

Set extension configuration:

```
assetic:
	assets:
		styles:
			output: temp/styles.css
			filters: [less, ?cssmin]
			files:
				- assets/css/style.css
				- assets/less/style.less

		scripts:
			output: temp/scripts.js
			filters: [jsmin]
			files:
				- assets/js/*
```

Other settings that are not required, and are set by default as below:

### Rebuild assets with every hit

```
parameters:
	rebuildAssets: true
```

### DebugMode

```
assetic:
	debug: %debugMode%
```

### Filters

```
assetic:
	filters:
		less: Assetic\Filter\LessphpFilter
		cssmin: Assetic\Filter\CssMinFilter
		jsmin: Assetic\Filter\JSMinFilter
```

### Workers

```
assetic:
	workers:
		# place to register your workers
```

## Usage

In template with macro assets:

```
{assets styles, scripts}
```

Macro save assets output to temp folder and generates HTML:

```
<link rel="stylesheet" href="/temp/styles.css?v=1525856452" />
<script type="text/javascript" src="/temp/scripts.js?v=1524029735" /></script>
```

## Example of generating assets on deploy

Symphony console command to generate assets:

```
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssetsBuildCommand extends Command
{	
	/** @var string */
	protected static $defaultName = 'assets:build';
	
	/** @var AssetWriter @inject */
	public $assetWriter;

	/** @var AssetManager @inject */
	public $assetManager;	

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$this->assetWriter->writeManagerAssets($this->assetManager);
	}
}
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/karelkolaska/nette-thumb/blob/master/LICENSE) file for details.
