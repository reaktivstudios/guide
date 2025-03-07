<?php 
/**
 * Creates a bulleted list.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Bulleted_list block.
 */
class Bulleted_list extends Text { // phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'ul';
}
