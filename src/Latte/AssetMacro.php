<?php

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
	 * 
	 * 
	 */
	public function initialize()
	{
		
	}

	/**
	 * 
	 * 
	 */
	public function finalize()
	{
		
	}

	/**
	 * 
	 * @param MacroNode $node
	 */
	public function nodeOpened(MacroNode $node)
	{
		$assets = $node->args;

		$node->isEmpty = TRUE;
		$node->openingCode = '<?php echo call_user_func($this->filters->_assets, "' . $assets . '"); ?>';
	}

	/**
	 * 
	 * @param MacroNode $node
	 */
	public function nodeClosed(MacroNode $node)
	{
		
	}
}