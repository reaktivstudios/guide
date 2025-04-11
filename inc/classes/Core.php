<?php
/**
 * The core site guide class.
 *
 * @package rkv-guide
 */

namespace RKV\Site_Guide;

/**
 * Initialize all other classes here.
 */
class Core {
	/**
	 * The classes or callables.
	 *
	 * Provide fully qualified classes or callbacks
	 * to instantiate the various objects for
	 * the utilities.
	 *
	 * @var array
	 */
	private $classes = [
		// Admin.
		'RKV\Site_Guide\Admin\Admin',

		// API.
		'\RKV\Site_Guide\API\Sync',

		// API - CLI.
		'\RKV\Site_Guide\API\CLI',

		// Cron.
		'\RKV\Site_Guide\Cron\Sync',

		// Media.
		'\RKV\Site_Guide\Media\Gallery',

		// Post Types.
		'\RKV\Site_Guide\Post_Type\Site_Guide',

		// Taxonomies.
		'\RKV\Site_Guide\Taxonomy\Guide_Type',

		// Templates.
		'\RKV\Site_Guide\Template\Enqueue',
		'\RKV\Site_Guide\Template\Title',
	];

	/**
	 * Files that should be loaded.
	 *
	 * @var array
	 */
	private $files = [];

	/**
	 * Calls the classes callbacks and initializes the objects.
	 */
	public function __construct() {
		$this->init_classes();
		$this->require_files();
	}

	/**
	 * Initialize the classes.
	 *
	 * @return void
	 */
	private function init_classes() {
		/**
		 * Allow filtering the classes.
		 *
		 * @param array $classes The classes or callables.
		 */
		$classes = apply_filters( 'rkv_site_guide_classes', $this->classes );

		foreach ( $classes as $class ) {
			if ( false !== strpos( $class, 'CLI' ) && ! class_exists( 'WP_CLI' ) ) {
				continue;
			}

			if ( is_callable( $class ) ) {
				call_user_func( $class );
			} elseif ( class_exists( $class ) ) {
				$obj = new $class();

				if ( method_exists( $obj, 'run' ) ) {
					$obj->run();
				}
			}
		}
	}

	/**
	 * Require the files.
	 *
	 * @return void
	 */
	private function require_files() {
		/**
		 * Allow filtering the files.
		 *
		 * @param array $files The files.
		 */
		$files = apply_filters( 'rkv_site_guide_files', $this->files );

		foreach ( $files as $file ) {
			$file_path = trailingslashit( RKV_SITE_GUIDE_PATH ) . $file;

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}
	}
}
