<?php
/**
 * Simple logger class
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;

/**
 * Class Logger
 *
 * @package RFD\Core
 */
class Logger {

	/**
	 * Log to file and maybe die.
	 *
	 * @param mixed $log Data to log.
	 * @param false $wp_die Run wp die after log.
	 */
	public static function log( $log, $wp_die = false ): void {
		self::write( $log );
		if ( true === $wp_die ) { // @phpstan-ignore-line.
			wp_die( esc_attr__( $log ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		}
	}

	/**
	 * Log data to error log if WP_DEBUG allows it.
	 *
	 * @param mixed $log Data to log.
	 */
	public static function write( $log ): void {
		if ( true !== WP_DEBUG ) {
			return;
		}
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
		} else {
			error_log( $log ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}

	}
}
