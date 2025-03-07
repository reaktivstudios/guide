<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * H2
 */
class Heading_2 extends Text {
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'h2';
}
