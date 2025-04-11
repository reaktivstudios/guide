<?php 
/**
 * Creates a numbered list.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

/**
 * Numbered_list block.
 */
class Numbered_list extends Text { // phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'ol';
}
