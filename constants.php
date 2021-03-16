<?php
/**
 * Constants file
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 * @subpackage RFD\Core\Views
 */

if ( false === defined( 'RFD_CORE_VIEW_PATH' ) ) {
	define( 'RFD_CORE_VIEW_PATH', dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR );
}

if ( false === defined( 'RFD_CORE_CONFIG_PATH' ) ) {
	define( 'RFD_CORE_CONFIG_PATH', dirname( __FILE__, 2 ) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR );
}

if ( false === defined( 'RFD_CORE_ASSETS_URL' ) ) {
	define( 'RFD_CORE_ASSETS_URL', plugin_dir_url( __FILE__ ) . '/assets/' );
}
