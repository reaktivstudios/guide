<?php
/**
 * Wrapper trait.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\API\Blocks;

trait Wrapper {
	/**
	 * Wraps a string in HTML tags.
	 *
	 * @param  string $content The string to wrap.
	 * @param  string $tag     The HTML tag.
	 * @param  string $classes The HTML class.
	 * @return string
	 */
	protected function wrap( $content, $tag, $classes = '' ) {
		if ( empty( $tag ) ) {
			return $content;
		}

		return sprintf( 
			'<%1$s%3$s>%2$s</%1$s>', 
			$tag, 
			$content,
			$classes ? ' class="' . esc_attr( $classes ) . '"' : ''
		);
	}
}
