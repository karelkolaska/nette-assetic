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
parameters:
	wwwTempDir: /temp

assetic:
	assets:
		styles:
			output: %wwwTempDir%/styles.css
			filters: [less, ?cssmin]
			files:
				- assets/css/style.css
				- assets/less/style.less

		scripts:
			output: %wwwTempDir%/scripts.js
			filters: [jsmin]
			files:
				- assets/js/*
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

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/karelkolaska/nette-thumb/blob/master/LICENSE) file for details.
