<?php
/**
 * Helper functions.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide;

/**
 * Gets the path to the docs directory.
 *
 * @return string
 */
function get_docs_path() {
	$docs_path = apply_filters( 'rkv_guide_docs_path', trailingslashit( WP_CONTENT_DIR ) . 'docs' );
	$docs_path = trailingslashit( $docs_path );

	return $docs_path;
}

/**
 * Gets the URL to the docs directory.
 * 
 * @return string
 */
function get_docs_url() {
	$docs_url = apply_filters( 'rkv_guide_docs_url', trailingslashit( WP_CONTENT_URL ) . 'docs' );
	$docs_url = trailingslashit( $docs_url );

	return $docs_url;
}


/**
 * Gets the image SRC from a relative path an directory.
 *
 * @param  string $rel_path    The relative path including the image.
 * @param  string $current_dir The current directory.
 * @return string
 */
function get_image_src( $rel_path, $current_dir ) {
	$rel_path = ltrim( $rel_path, '/.' );
	$rel_path = trailingslashit( $current_dir ) . $rel_path;
	$rel_path = str_replace( get_docs_path(), '', $rel_path );
	$url      = get_docs_url() . $rel_path;

	return $url;
}
