<?php 
/**
 * Creates a numbered list item
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Numbered_list_item block.
 */
class Numbered_list_item extends Text { // phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
	/**
	 * The parent html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'li';
}
