<?php 
/**
 * Syncs the guide docs from Notion.
 * 
 * @package rkv-guide
 */

namespace RKV\Site_Guide\API;

/**
 * Sync class.
 */
class Sync {
	/**
	 * Number of attempts to try.
	 *
	 * @var integer
	 */
	private int $attempts = 5;

	/**
	 * The doc changes.
	 *
	 * @var array
	 */
	private $doc_changes = [
		'sync_time' => '',
		'added'     => [],
		'updated'   => [],
		'deleted'   => [],
	];

	/**
	 * Add the actions.
	 */
	public function __construct() {
		if ( empty( $_GET['rkv-guide-sync'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		add_action( 'init', [ $this, 'callback' ], 11 );
	}

	/**
	 * Initiate the sync.
	 *
	 * @return void
	 */
	public function callback() {
		$data = [
			'filter' => [
				'property' => 'Site Guide',
				'checkbox' => [
					'equals' => true,
				],
			],
		];
		$data = apply_filters( 'rkv_site_guide_sync_data', $data );

		$args = [
			'timeout' => 10, // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout
			'body'    => wp_json_encode( $data ),
			'headers' => [
				'Authorization'  => 'Bearer ' . RKV_SITE_GUIDE_API_KEY,
				'Notion-Version' => '2022-06-28',
				'Content-Type'   => 'application/json',
			],
		];

		$response = wp_remote_post( 'https://api.notion.com/v1/databases/' . RKV_SITE_GUIDE_DATABASE_ID . '/query', $args );

		if ( is_wp_error( $response ) && $this->attempts ) {
			$this->callback();
			--$this->attempts;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( is_string( $body ) ) {
			$body = json_decode( $body );
		}

		if ( isset( $body->results ) ) {
			foreach ( $body->results as $result ) {
				$updater = new Update( $result->id, $result->last_edited_time, $result->properties );
				$post_id = $updater->get_post_id();

				if ( $post_id ) {
					switch ( $updater->get_status() ) {
						case 'add':
							$this->doc_changes['added'][] = $post_id;
							break;
						case 'update':
							$this->doc_changes['updated'][] = $post_id;
							break;
					}
				}
			}

			$deleter = new Delete( $body->results );
			$deleted = $deleter->get_deleted();

			if ( $deleted ) {
				$this->doc_changes['deleted'] = $deleted;
			}

			$this->doc_changes['sync_time'] = time();

			update_option( 'rkv_site_guide_sync', $this->doc_changes );
		}
	}

	/**
	 * Gets the doc changes.
	 *
	 * @return array
	 */
	public function get_changes() {
		return $this->doc_changes;
	}
}
