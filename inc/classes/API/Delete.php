<?php 
/**
 * Deletes removed docs.
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API;

use stdClass;

/**
 * Delete class
 */
class Delete {
	/**
	 * Notion ID.
	 *
	 * @var array
	 */
	private array $docs = [];

	/**
	 * Posts to check.
	 * 
	 * @var array
	 */
	private array $posts = [];

	/**
	 * The deleted docs.
	 *
	 * @var array
	 */
	private array $deleted = [];

	/**
	 * Archived docs.
	 *
	 * @var array
	 */
	private array $archived = [];

	/**
	 * Set docs and triggers update.
	 *
	 * @param  stdClass $docs  The documents.
	 */
	public function __construct( $docs ) {
		$this->process_docs( $docs );
		$this->set_posts();
		$this->check();
	}

	/**
	 * Get the deleted docs.
	 *
	 * @return array
	 */
	public function get_deleted() {
		return $this->deleted;
	}

	/**
	 * Sets the docs to be an array of notion_ids.
	 *
	 * @param  array $docs The docs.
	 * @return void
	 */
	private function process_docs( $docs ) {
		foreach ( $docs as $doc ) {
			$this->docs[] = $doc->id;

			if ( ! empty( $doc->archived ) || ! empty( $doc->in_trash ) ) {
				$this->archived[] = $doc->id;
			}
		}
	}

	/**
	 * Set the posts to check.
	 *
	 * @return void
	 */
	private function set_posts() {
		$posts = get_posts( 
			[
				'post_type'      => 'rkv_guide',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			]
		);

		foreach ( $posts as $post ) {
			$notion_id = get_post_meta( $post->ID, 'notion_id', true );

			if ( $notion_id ) {
				$this->posts[ $post->ID ] = $notion_id;
			}
		}
	}

	/**
	 * Check for deleted docs.
	 *
	 * @return void
	 */
	private function check() {
		foreach ( $this->posts as $id => $notion_id ) {
			if ( ! in_array( $notion_id, $this->docs, true ) || in_array( $notion_id, $this->archived, true ) ) {
				$title = get_the_title( $id );

				wp_delete_post( $id );

				$this->deleted[] = $title;
			}
		}
	}
}
