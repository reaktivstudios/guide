<?php 
/**
 * Builds the content from the blocks.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\API;

/**
 * Build the content
 */
class Content_Parser {
	use Blocks\Wrapper;

	/**
	 * The blocks.
	 * 
	 * @var array
	 */
	private array $blocks = [];

	/**
	 * The content.
	 * 
	 * @var string
	 */
	private string $content = '';

	/**
	 * Constructor.
	 * 
	 * @param array $blocks The blocks.
	 */
	public function __construct( $blocks ) {
		$this->blocks = $blocks;

		$this->parse();
	}

	/**
	 * Parse the blocks.
	 */
	private function parse() {
		foreach ( $this->blocks as $block ) {
			$tag   = $block['tag'] ?? '';
			$class = $block['class'] ?? '';
			$inner = $block['content'] ?? '';

			if ( ! empty( $block['children'] ) ) {
				$content_parser = new self( $block['children'] );
				
				$inner .= $content_parser->get_content();
			}

			$this->content .= $this->wrap( $inner, $tag, $class );
		}
	}

	/**
	 * Get the content.
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}
}
