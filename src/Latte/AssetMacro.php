<?php

declare(strict_types=1);

namespace KarelKolaska\NetteAssetic\Latte;

use Latte\IMacro;
use Latte\MacroNode;

/**
 * Asset Macro
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class AssetMacro implements IMacro
{
	/**
	 * Initializes before template parsing.
	 * @return void
	 */
	public function initialize()
	{

	}

	/**
	 * Finishes template parsing.
	 * @return array|null [prolog, epilog]
	 */
	public function finalize()
	{

	}

	/**
	 * New node is found. Returns false to reject.
	 * @return bool|null
	 */
	public function nodeOpened(MacroNode $node)
	{
		$assets = $node->args;
		$node->openingCode = '<?php echo call_user_func($this->filters->_assets, "' . $assets . '"); ?>';
	}

	/**
	 * Node is closed.
	 * @return void
	 */
	public function nodeClosed(MacroNode $node)
	{

	}
}
