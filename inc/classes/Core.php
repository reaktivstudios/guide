<?php
/**
 * The core site guide class.
 *
 * @package rkv-guide
 */

namespace RKV\Guide;

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
		'RKV\Guide\Admin\Admin',

		// Local Docs.
		'\RKV\Guide\Local_Docs\Sync',

		// Media.
		'\RKV\Guide\Media\Gallery',

		// Post Types.
		'\RKV\Guide\Post_Type\Guide',

		// Taxonomies.
		'\RKV\Guide\Taxonomy\Guide_Type',

		// Templates.
		'\RKV\Guide\Template\Enqueue',
		'\RKV\Guide\Template\Title',

		// WP 101.
		'\RKV\Guide\WP_101\Compat',
	];

	/**
	 * Files that should be loaded.
	 *
	 * @var array
	 */
	private $files = [
		'inc/helpers.php',
	];

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
