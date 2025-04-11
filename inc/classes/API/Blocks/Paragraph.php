<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Paragraph block.
 */
class Paragraph extends Text {
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = 'p';
}
