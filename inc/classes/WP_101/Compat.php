<?php
/**
 * Adds compatibility for WP101 plugin.
 * 
 * @package rkv-guide
 */

namespace RKV\Guide\WP_101;

/**
 * WP 101 Compat class.
 */
class Compat {
	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( did_action( 'init' ) || doing_action( 'init' ) ) {
			$this->init();
		}

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Initialize compatibility features.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WP101_INC' ) ) {
			return;
		}

		// Remove WP101's admin menu.
		remove_action( 'admin_menu', 'WP101\Admin\register_menu_pages' );
		add_action( 'admin_menu', [ $this, 'register_menu_pages' ] );

		// Enqueue WP101 scripts and styles.
		remove_action( 'admin_enqueue_scripts', 'WP101\Admin\enqueue_scripts' );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Register the WP101 settings page.
	 *
	 * @return void
	 */
	public function register_menu_pages() {
		add_submenu_page(
			'site-guide',
			_x( 'WP101', 'page title', 'rkv-guide' ),
			_x( 'Video Tutorials', 'menu title', 'rkv-guide' ),
			'read',
			'wp101',
			'WP101\Admin\render_listings_page',
			'dashicons-video-alt3'
		);

		add_submenu_page(
			'site-guide',
			_x( 'WP101 Settings', 'page title', 'rkv-guide' ),
			_x( 'WP101 Settings', 'menu title', 'rkv-guide' ),
			'manage_options',
			'wp101-settings',
			'WP101\Admin\render_settings_page'
		);
	}

	/**
	 * Register scripts and styles to be used in WP admin.
	 *
	 * @param string $hook The page being loaded.
	 */
	public function enqueue_scripts( $hook ) {
		// Only enqueue on WP101 pages.
		if ( false !== strpos( $hook, 'guide_page_wp101' ) ) {
			wp_register_style(
				'wp101-admin',
				WP101_URL . '/assets/css/wp101-admin.css',
				null,
				WP101_VERSION,
				'all'
			);

			wp_register_script(
				'wp101-admin',
				WP101_URL . '/assets/js/wp101-admin.min.js',
				array( 'jquery-ui-accordion' ),
				WP101_VERSION,
				true
			);

			wp_enqueue_style( 'wp101-admin' );
			wp_enqueue_script( 'wp101-admin' );

			add_action( 'admin_notices', 'WP101\Admin\display_api_errors' );
		}
	}
}
