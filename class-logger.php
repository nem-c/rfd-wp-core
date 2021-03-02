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

class Logger {
	public static function log( $log, $wp_die = false ) {
		self::write( $log );
		if ( true === $wp_die ) {
			wp_die( esc_attr__( $log ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		}
	}

	public static function write( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
			} else {
				error_log( $log ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
	}
}
