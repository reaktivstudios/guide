<?php
/**
 * Base taxonomy class.
 *
 * @package rkv-guide
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
			'name'              => _x( 'Types', 'taxonomy general name', 'rkv-guide' ),
			'singular_name'     => _x( 'Type', 'taxonomy singular name', 'rkv-guide' ),
			'search_items'      => __( 'Search Types', 'rkv-guide' ),
			'all_items'         => __( 'All Types', 'rkv-guide' ),
			'parent_item'       => __( 'Parent Type', 'rkv-guide' ),
			'parent_item_colon' => __( 'Parent Type:', 'rkv-guide' ),
			'edit_item'         => __( 'Edit Type', 'rkv-guide' ),
			'update_item'       => __( 'Update Type', 'rkv-guide' ),
			'add_new_item'      => __( 'Add New Type', 'rkv-guide' ),
			'new_item_name'     => __( 'New Type Name', 'rkv-guide' ),
			'menu_name'         => __( 'Types', 'rkv-guide' ),
			'not_found'         => __( 'No Types Found', 'rkv-guide' ),
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
