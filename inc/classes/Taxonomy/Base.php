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
abstract class Base {

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy_name;

	/**
	 * Taxonomy args.
	 *
	 * @var array
	 */
	protected $taxonomy_args = [];

	/**
	 * Post types supported by this taxonomy.
	 *
	 * @var array
	 */
	protected $taxonomy_post_types = [];

	/**
	 * Flag to determine if the taxonomy is opt-in or opt-out.
	 * Taxonomies are opt-in by default.
	 *
	 * @var bool
	 */
	protected $opt_in = true;

	/**
	 * Show in GraphQL.
	 *
	 * @var bool
	 */
	protected $show_in_graphql = false;

	/**
	 * GraphQL single name.
	 *
	 * @var string
	 */
	protected $graphql_single_name = '';

	/**
	 * GraphQL plural name.
	 *
	 * @var string
	 */
	protected $graphql_plural_name = '';

	/**
	 * Class constructor.
	 *
	 * Called during init hook.
	 *
	 * @return void
	 */
	public function __construct() {
		// Make sure a taxonomy name is defined.
		if ( empty( $this->taxonomy_name ) ) {
			return;
		}

		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * Init callback.
	 *
	 * @return void
	 */
	public function init() {
		$this->initialize_taxonomy();
		$this->create_taxonomy();
	}

	/**
	 * Initialize the taxonomy.
	 */
	abstract protected function initialize_taxonomy();

	/**
	 * Create the post type.
	 *
	 * @return void
	 */
	protected function create_taxonomy() {
		$default_args = $this->get_default_taxonomy_args();

		$this->taxonomy_args = wp_parse_args( $this->taxonomy_args, $default_args );

		if ( ! empty( $this->taxonomy_args ) && ! empty( $this->taxonomy_post_types ) ) {
			register_taxonomy(
				$this->taxonomy_name,
				$this->taxonomy_post_types,
				$this->taxonomy_args
			);
		}
	}

	/**
	 * Get the default taxonomy args.
	 *
	 * @return array Default args.
	 */
	protected function get_default_taxonomy_args() {
		$default_args = [
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		];

		if (
			$this->show_in_graphql &&
			! empty( $this->graphql_single_name ) &&
			! empty( $this->graphql_plural_name )
		) {
			$default_args['show_in_graphql']     = true;
			$default_args['graphql_single_name'] = $this->graphql_single_name;
			$default_args['graphql_plural_name'] = $this->graphql_plural_name;
		}

		return $default_args;
	}
}
