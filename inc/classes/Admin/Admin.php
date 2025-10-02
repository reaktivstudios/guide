<?php
/**
 * Admin interface
 *
 * @package rkv-guide
 */

namespace RKV\Guide\Admin;

/**
 * Admin UI.
 */
class Admin {

	/**
	 * Run.
	 */
	public function run() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Admin menu.
	 */
	public function admin_menu() {

		// Add our page.
		add_menu_page(
			__( 'Guide Articles', 'rkv-guide' ),
			__( 'The Guide', 'rkv-guide' ),
			'edit_posts',
			'site-guide',
			[ $this, 'submenu_page_callback' ],
			'dashicons-editor-help',
			3,
		);

		remove_menu_page( 'edit.php?post_type=site-guide' );
	}

	/**
	 * Submenu Page callback.
	 */
	public function submenu_page_callback() {
		printf(
			'<div class="wrap" id="rkv-site-guide-settings">%s</div>',
			esc_html__( 'Loading…', 'rkv-guide' )
		);
	}

	/**
	 * Admin enqueue scripts.
	 *
	 * @param string $admin_page The admin page slug.
	 */
	public function admin_enqueue_scripts( $admin_page ) {
		if ( 'toplevel_page_site-guide' !== $admin_page ) {
			return;
		}

		$asset_file = RKV_SITE_GUIDE_PATH . 'dist/admin.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;

		wp_enqueue_script(
			'rkv-guide-admin',
			RKV_SITE_GUIDE_URL . 'dist/admin.js',
			$asset['dependencies'],
			$asset['version'],
			array(
				'in_footer' => true,
			)
		);

		wp_enqueue_style(
			'rkv-guide-admin',
			RKV_SITE_GUIDE_URL . 'dist/admin.css',
			[ 'wp-edit-blocks' ],
			[],
		);
	}
}
