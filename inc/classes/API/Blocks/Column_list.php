<?php 
/**
 * Adds columns
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

use RKV\Site_Guide\API\Get_Blocks;

/**
 * Column List.
 */
class Column_list extends Base { // phpcs:ignore PEAR.NamingConventions.ValidClassName.Invalid, Generic.Classes.OpeningBraceSameLine.ContentAfterBrace
	/**
	 * The block class.
	 *
	 * @var string
	 */
	protected string $class = 'rkv-column-list';
}
