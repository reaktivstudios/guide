<?php 
/**
 * Creates standard text elements like paragraphs, headings, etc.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

use stdClass;

/**
 * Text block.
 */
class Text extends Base {
	/**
	 * The html tag.
	 *
	 * @var string
	 */
	protected string $tag = '';
}
