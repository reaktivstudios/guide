<?php
/**
 * Base taxonomy class.
 *
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\Taxonomy;

/**
 * Define the base class and associated methods.
 */
class Guide_Type extends Base {

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy_name = 'guide-type';

	/**
	 * Default terms.
	 *
	 * @var array
	 */
	private $default_terms = [
		'Explanation',
		'How To',
		'Tutorial',
		'Reference',
		'Local',
	];
	
	/**
	 * Initialize the taxonomy.
	 */
	protected function initialize_taxonomy() {
		$labels = [
			'name'              => _x( 'Types', 'taxonomy general name', 'rkv-site-guide' ),
			'singular_name'     => _x( 'Type', 'taxonomy singular name', 'rkv-site-guide' ),
			'search_items'      => __( 'Search Types', 'rkv-site-guide' ),
			'all_items'         => __( 'All Types', 'rkv-site-guide' ),
			'parent_item'       => __( 'Parent Type', 'rkv-site-guide' ),
			'parent_item_colon' => __( 'Parent Type:', 'rkv-site-guide' ),
			'edit_item'         => __( 'Edit Type', 'rkv-site-guide' ),
			'update_item'       => __( 'Update Type', 'rkv-site-guide' ),
			'add_new_item'      => __( 'Add New Type', 'rkv-site-guide' ),
			'new_item_name'     => __( 'New Type Name', 'rkv-site-guide' ),
			'menu_name'         => __( 'Types', 'rkv-site-guide' ),
			'not_found'         => __( 'No Types Found', 'rkv-site-guide' ),
		];

		$this->taxonomy_args = [
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => false,
			'show_in_rest' => true,
			'capabilities' => [
				'create_terms' => is_multisite() ? 'do_not_allow' : false,
				'delete_terms' => is_multisite() ? 'do_not_allow' : false,
			],
		];

		$this->taxonomy_post_types = [ 'site-guide' ];

		add_action( 'admin_init', [ $this, 'add_default_terms' ] );
	}

	/**
	 * Adds the default terms.
	 *
	 * @return void
	 */
	public function add_default_terms() {
		foreach ( $this->default_terms as $term ) {
			if ( ! term_exists( $term, $this->taxonomy_name ) ) { // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.term_exists_term_exists
				wp_insert_term( $term, $this->taxonomy_name );
			}
		}
	}
}
