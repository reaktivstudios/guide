<?php
/**
 * Builds the content from the blocks.
 *
 * @package rkv-guide
 */

namespace RKV\Guide\API;

use WP_CLI;

/**
 * WordPress CLI.
 */
class CLI {
	/**
	 * Constructor.
	 */
	public function __construct() {
		WP_CLI::add_command( 'rkv-guide-sync', [ $this, 'run_sync' ] );
	}

	/**
	 * Run the sync.
	 * 
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Force all content to update.
	 * 
	 * @param array $args       The arguments.
	 * @param array $assoc_args Associative array of args.
	 * 
	 * @return void
	 */
	public function run_sync( $args, $assoc_args ) {
		$forced = isset( $assoc_args['force'] );

		WP_CLI::log( 'Running sync... ' );

		if ( $forced ) {
			WP_CLI::log( 'Forcing update...' );
			$_GET['force'] = true; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$sync = new \RKV\Guide\API\Sync();
		$sync->callback();

		$changes = $sync->get_changes();

		if ( ! empty( $changes['added'] ) ) {
			WP_CLI::log( 'New docs: ' . count( $changes['added'] ) );
		}

		if ( ! empty( $changes['updated'] ) ) {
			WP_CLI::log( 'Updated docs: ' . count( $changes['updated'] ) );
		}

		if ( ! empty( $changes['deleted'] ) ) {
			WP_CLI::log( 'Deleted docs: ' . count( $changes['deleted'] ) );
		}

		if ( empty( $changes['added'] ) && empty( $changes['updated'] ) && empty( $changes['deleted'] ) ) {
			WP_CLI::log( 'No changes.' );
		}

		WP_CLI::success( 'Sync complete.' );
	}
}
