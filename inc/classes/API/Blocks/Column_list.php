<?php 
/**
 * Adds columns
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API\Blocks;

use RKV\Guide\API\Get_Blocks;

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
