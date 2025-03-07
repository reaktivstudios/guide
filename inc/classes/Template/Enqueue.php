<?php
/**
 * Enqueues front end styles.
 * 
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\Template;

/**
 * Enqueue class.
 */
class Enqueue {
	/**
	 * Add the actions.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'callback' ] );
	}

	/**
	 * Enqueue the styles.
	 *
	 * @return void
	 */
	public function callback() {
		if ( ! is_singular( 'site-guide' ) ) {
			return;
		}

		wp_enqueue_style( 
			'rkv-site-guide-css',
			trailingslashit( RKV_SITE_GUIDE_URL ) . 'assets/main.css',
			[],
			filemtime( trailingslashit( RKV_SITE_GUIDE_PATH ) . 'assets/main.css' )
		);
	}
}
