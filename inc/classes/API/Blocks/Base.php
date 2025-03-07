<?php 
/**
 * Sets up the processing for a block.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

use RKV\Site_Guide\API\Get_Blocks;

/**
 * Block base class.
 */
abstract class Base {
	use Wrapper;

	/**
	 * The block.
	 *
	 * @var array
	 */
	protected array $block = [];

	/**
	 * The HTML tag.
	 *
	 * @var string
	 */
	protected string $tag = 'div';

	/**
	 * HTML classes.
	 *
	 * @var string
	 */
	protected string $class = '';

	/**
	 * Sets up the block.
	 * 
	 * @param stdClass $block The block.
	 */
	public function __construct( $block ) {
		$type        = $block->type ?? '';
		$this->block = [
			'id'       => $block->id,
			'type'     => $type,
			'tag'      => $this->tag,
			'class'    => $this->class,
			'content'  => '',
			'data'     => $block->$type ?? [],
			'children' => empty( $block->has_children ) ? [] : $this->get_children( $block->id ),
		];

		$this->set_content();
	}

	
	/**
	 * Set the content.
	 *
	 * @return void
	 */
	protected function set_content() {
		$rich_text  = $this->block['data']->rich_text ?? [];
		$inner_text = '';

		foreach ( $rich_text as $text_node ) {
			$inner_text .= $this->process_text_node( $text_node );
		}

		$this->block['content'] = $inner_text;
	}

	/**
	 * Processes a text node to wrap with HTML.
	 *
	 * @param  stdClass $text_node The text node.
	 * @return string
	 */
	protected function process_text_node( $text_node ) {
		$plain_text = $text_node->plain_text ?? '';

		if ( empty( $plain_text ) ) {
			return '';
		}

		$annotations = $text_node->annotations ?? [];

		foreach ( $annotations as $type => $enabled ) {
			if ( ! $enabled ) {
				continue;
			}

			$markup = '';

			switch ( $type ) {
				case 'bold':
					$markup = 'strong';
					break;
				case 'italic':
					$markup = 'em';
					break;
				case 'strikethrough':
					$markup = 'strikethrough';
					break;
				case 'underline':
					$markup = 'u';
					break;
				case 'code':
					$markup = 'code';
					break;
			}

			if ( $markup ) {
				$plain_text = $this->wrap( $plain_text, $markup );
			}

			$type = $text_node->type ?? '';
			if ( $type && ! empty( $text_node->$type ) ) {
				$node_type = $text_node->$type;

				$link = $node_type->link ?? new \stdClass();
				$url  = $link->url ?? '';

				if ( $url ) {
					$plain_text = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( $url ),
						$plain_text
					);
				}
			}
		}

		return $plain_text;
	}

	/**
	 * Wraps a string in HTML tags.
	 *
	 * @param  string $content The string to wrap.
	 * @param  string $tag     The HTML tag.
	 * @return string
	 */
	protected function wrap( $content, $tag ) {
		if ( empty( $tag ) ) {
			return $content;
		}

		return sprintf( '<%1$s>%2$s</%1$s>', $tag, $content );
	}

	/**
	 * Gets the block children.
	 *
	 * @param  string $id The block ID.
	 * @return array
	 */
	private function get_children( $id ) {
		$block_parser = new Get_Blocks( $id );
		return $block_parser->get_the_blocks();
	}

	/**
	 * Gets the block object.
	 */
	public function get_block() {
		return $this->block;
	}
}
