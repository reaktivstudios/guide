<?php
/**
 * Adds Cron job to trigger the sync.
 *
 * @package rkv-guide
 */

namespace RKV\Site_Guide\Cron;

/**
 * Class Cron Sync
 */
class Sync {
	/**
	 * The hook name.
	 *
	 * @var string
	 */
	protected string $cron_hook_name = 'rkv_site_guide_sync';

	/**
	 * Frequency string.
	 * from wp_get_schedules().
	 *
	 * @var string
	 */
	protected string $recurrence = 'hourly';

	/**
	 * Base constructor.
	 */
	public function __construct() {
		// Add our action that will be called when the cron job fires.
		add_action( $this->cron_hook_name, [ $this, 'callback' ] );
		// Setup the cron job, checking first to make sure it doesn't already exist.
		if ( ! wp_next_scheduled( $this->cron_hook_name ) ) {
			wp_schedule_event( time(), $this->recurrence, $this->cron_hook_name );
		}
	}

	/**
	 * Trigger the sync via non-blocking request to hand off.
	 *
	 * @return void
	 */
	public function callback() {
		$sync = new \RKV\Site_Guide\API\Sync();
		$sync->callback();
	}
}
