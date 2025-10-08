<?php
/**
 * Base post type class.
 *
 * @package rkv-guide
 */

namespace RKV\Guide\Post_Type;

/**
 * Define the base class and associated methods.
 */
class Guide extends Base {

	/**
	 * Post type name.
	 *
	 * @var string
	 */
	protected $post_type_name = 'site-guide';

	/**
	 * Initialize the post type.
	 */
	protected function initialize_post_type() {
		$labels = [
			'name'               => __( 'Docs', 'rkv-guide' ),
			'singular_name'      => __( 'Doc', 'rkv-guide' ),
			'edit_item'          => __( 'Edit Doc', 'rkv-guide' ),
			'new_item'           => __( 'New Doc', 'rkv-guide' ),
			'view_item'          => __( 'View Doc', 'rkv-guide' ),
			'search_items'       => __( 'Search Docs', 'rkv-guide' ),
			'not_found'          => __( 'No Docs found', 'rkv-guide' ),
			'not_found_in_trash' => __( 'No Docs found in Trash', 'rkv-guide' ),
			'menu_name'          => __( 'Site Guide', 'rkv-guide' ),
		];

		$this->post_type_args = [
			'labels'              => $labels,
			'description'         => __( 'The Guide', 'rkv-guide' ),
			'taxonomies'          => [ 'guide-type' ],
			'menu_position'       => 2,
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'public'              => is_user_logged_in() && current_user_can( 'edit_pages' ),
			'has_archive'         => is_user_logged_in() && current_user_can( 'edit_pages' ),
			'query_var'           => $this->post_type_name,
			'supports'            => [
				'title',
				'editor',
				'revisions',
			],
			'capabilities'        => [
				'create_posts' => is_multisite() ? 'do_not_allow' : false,
				'delete_posts' => is_multisite() ? 'do_not_allow' : false,
			],
		];

		if ( apply_filters( 'rkv_site_guide_allow_editors', false ) ) {
			unset( $this->post_type_args['capabilities'] );

			$this->post_type_args['capability_type'] = 'page';
		}

		add_action( 'pre_get_posts', [ $this, 'limit_post_access' ] );
		add_filter( 'post_type_link', [ $this, 'post_type_link' ], 10, 2 );
	}

	/**
	 * Limit post access based on user role.
	 *
	 * @param  \WP_Query $query The query.
	 * @return void
	 */
	public function limit_post_access( $query ) {
		if ( ! $query->is_main_query() || $this->post_type_name !== $query->get( 'post_type' ) ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$meta_query = $query->get( 'meta_query' );

		if ( ! is_array( $meta_query ) ) {
			$meta_query = [];
		}

		$role_query = [
			'relation' => 'AND',
		];

		$role_query[] = [
			'key'     => 'rkv_role_req',
			'compare' => '!=',
			'value'   => 'Admin',
		];

		if ( ! current_user_can( 'edit_pages' ) ) {
			$role_query[] = [
				'key'     => 'rkv_role_req',
				'compare' => '!=',
				'value'   => 'Editor',
			];
		}

		if ( ! current_user_can( 'edit_published_posts' ) ) {
			$role_query[] = [
				'key'     => 'rkv_role_req',
				'compare' => '!=',
				'value'   => 'Author',
			];
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$role_query[] = [
				'key'     => 'rkv_role_req',
				'compare' => '!=',
				'value'   => 'Contributor',
			];
		}

		$meta_query[] = $role_query;

		$query->set( 'meta_query', $meta_query );
	}

	/**
	 * Do not allow editing or deleting docs that have the notion_id post meta.
	 *
	 * @param  array $all_caps All capabilities.
	 * @param  array $caps     Capabilities.
	 * @param  array $args     Arguments.
	 * @return array
	 */
	public function user_has_cap( $all_caps, $caps, $args ) {
		$post_id = $args[2] ?? 0;

		if ( ! $post_id || 1 === (int) $post_id ) {
			return $all_caps;
		}

		$limited_cap = $args[0] ?? '';

		if ( 'delete_post' !== $limited_cap && 'edit_post' !== $limited_cap ) {
			return $all_caps;
		}

		if ( get_post_type( $post_id ) !== $this->post_type_name ) {
			return $all_caps;
		}

		$notion_id = get_post_meta( $post_id, 'notion_id', true );

		if ( $notion_id ) {
			$all_caps['edit_published_pages']   = false;
			$all_caps['edit_others_pages']      = false;
			$all_caps['edit_pages']             = false;
			$all_caps['delete_pages']           = false;
			$all_caps['delete_published_pages'] = false;
		}

		return $all_caps;
	}

	/**
	 * Fix the permalink for the post type.
	 *
	 * @param string  $post_link The post's permalink.
	 * @param WP_Post $post      The post object.
	 */
	public function post_type_link( $post_link, $post ) {
		if ( 'site-guide' === $post->post_type ) {
			$post_link = home_url( 'wp-admin/admin.php?page=site-guide&' ) . sprintf( 'article=%s', $post->post_name );
		}
		return $post_link;
	}
}
