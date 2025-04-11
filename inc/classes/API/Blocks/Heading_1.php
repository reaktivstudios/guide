<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

/**
 * H1
 */
class Heading_1 extends Text {
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'h1';
}
