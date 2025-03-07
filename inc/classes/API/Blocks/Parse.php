<?php 
/**
 * Parses a block.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

/**
 * Processes block to return a standardized output.
 */
class Parse {
	/**
	 * The block.
	 *
	 * @var string
	 */
	private array $block = [];

	/**
	 * Sets up the block.
	 * 
	 * @param stdClass $block The block.
	 */
	public function __construct( $block ) {
		$type  = ucfirst( $block->type );
		$class = __NAMESPACE__ . '\\' . $type;

		if ( 'Video' === $type ) {
			$class = __NAMESPACE__ . '\\Image';
		}

		if ( class_exists( $class ) ) {
			$handler = new $class( $block );

			$this->block = $handler->get_block();
		}
	}

	/**
	 * Get the block.
	 *
	 * @return array
	 */
	public function get_block() {
		return $this->block;
	}
}
