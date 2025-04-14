<?php 
/**
 * Creates a bulleted list item
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

/**
 * Bulleted_list_item block.
 */
class Bulleted_list_item extends Text { // phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'li';
}
