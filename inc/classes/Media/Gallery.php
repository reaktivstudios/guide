<?php
/**
 * Hides guide images from the media library.
 *
 * @package rkv-site-guide
 */

namespace RKV\Site_Guide\Media;

/**
 * Class Media Gallery.
 */
class Gallery {
	/**
	 * Base constructor.
	 */
	public function __construct() {
		add_filter( 'ajax_query_attachments_args', [ $this, 'hide_images' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ] );
	}

	/**
	 * Hide images from the media library.
	 *
	 * @param array $query_args The query arguments.
	 *
	 * @return array
	 */
	public function hide_images( array $query_args ): array {
		if ( ! is_admin() ) {
			return $query_args;
		}

		if ( empty( $query_args['post_type'] ) || 'attachment' !== $query_args['post_type'] || 'attachment' !== $query_args['post_type'] ) {
			return $query_args;
		}

		if ( ! isset( $query_args['meta_query'] ) ) {
			$query_args['meta_query'] = []; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		$query_args['meta_query'][] = [
			'key'     => 'rkv_site_guide',
			'compare' => 'NOT EXISTS',
		];

		return $query_args;
	}

	/**
	 * Hide images from the media library.
	 *
	 * @param \WP_Query $query The query object.
	 *
	 * @return void
	 */
	public function pre_get_posts( \WP_Query $query ): void {
		global $pagenow;

		if ( ! is_admin() || ! $query->is_main_query() || 'upload.php' !== $pagenow ) {
			return;
		}

		if ( 'attachment' != $query->get( 'post_type' ) ) {
			return;
		}

		$meta_query = $query->get( 'meta_query' ) ?: [];

		$meta_query[] = [
			'key'     => 'rkv_site_guide',
			'compare' => 'NOT EXISTS',
		];

		$query->set( 'meta_query', $meta_query );
	}
}
