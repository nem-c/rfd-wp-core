<?php

/**
 * Simple scheduler class
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;

abstract class Scheduler {
	/**
	 * @var Loader Loader Instance
	 */
	protected $loader;

	/**
	 * @var string $schedule_name scheduled name (used later as hook name for scheduling
	 */
	protected $schedule_name;

	/**
	 * @var string $recurrence how often process should be executed. Defaults to 'hourly’, ‘daily’, or ‘twicedaily’.
	 */
	protected $recurrence = 'daily';

	/**
	 * Scheduler constructor.
	 *
	 * @param Loader $loader
	 */
	public function __construct( Loader &$loader ) {
		$this->loader = &$loader;
	}

	/**
	 * Scheduler.
	 * Registers given schedule to be executed every n times.
	 */
	public function schedule() {
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
		if ( false === wp_next_scheduled( $this->schedule_name ) ) {
			wp_schedule_event( time(), $this->recurrence, $this->schedule_name );
		}
	}

	public function deactivate() {
		wp_clear_scheduled_hook( $this->schedule_name );
	}

	public function get_schedule_name() {
		return $this->schedule_name;
	}

	abstract function execute();
}