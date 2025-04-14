<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

/**
 * H3
 */
class Heading_3 extends Text {
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'h3';
}
