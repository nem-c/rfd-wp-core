<?php
/**
 * Simple scheduler class
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

/**
 * Class Scheduler
 *
 * @package RFD\Core\Abstracts
 */
abstract class Scheduler {

	/**
	 * Loader Instance
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Scheduled name (used later as hook name for scheduling)
	 *
	 * @var string $schedule_name
	 */
	protected $schedule_name;

	/**
	 * How often process should be executed.
	 * Accepts 'hourly’, ‘daily’, or ‘twicedaily’. Default 'daily'.
	 * If custom recurrence is defined it can be used here.
	 *
	 * @var string $recurrence
	 */
	protected $recurrence = 'daily';

	/**
	 * Scheduler constructor.
	 *
	 * @param Loader $loader Loader object.
	 */
	public function __construct( Loader $loader ) {
		$this->loader = &$loader;
	}

	/**
	 * Scheduler.
	 * Registers given schedule to be executed every n times.
	 */
	public function schedule(): void {
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		if ( false === wp_next_scheduled( $this->schedule_name ) ) {
			wp_schedule_event( time(), $this->recurrence, $this->schedule_name );
		}
	}

	/**
	 * Run when deactivated.
	 */
	public function deactivate(): void {
		wp_clear_scheduled_hook( $this->schedule_name );
	}

	/**
	 * Get schedule name
	 *
	 * @return string
	 */
	public function get_schedule_name(): string {
		return $this->schedule_name;
	}

	/**
	 * Command to execute.
	 *
	 * @return mixed
	 */
	abstract public function execute();
}
