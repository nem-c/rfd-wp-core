<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Core
 * @subpackage RFD\Core\Abstracts
 */

namespace RFD\Core;

/**
 * Class I18n
 */
final class I18n {

	/**
	 * Domain lang to be used
	 *
	 * @var string
	 */
	protected $domain = 'rfd-core';

	/**
	 * Plugin path to language files.
	 *
	 * @var string
	 */
	protected $plugin_rel_path = '';

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param array $props custom properties to be used.
	 */
	public static function init( Loader $loader, $props = array() ): void {
		$i18n = new I18n();
		foreach ( $props as $prop_name => $prop_value ) {
			$i18n->$prop_name = $prop_value;
		}
		$loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.9.0
	 */
	public function load_plugin_textdomain(): void {

		if ( true === empty( $this->domain ) || true === empty( $this->deprecated ) || true === empty( $this->plugin_rel_path ) ) {
			return;
		}

		load_plugin_textdomain( $this->domain, '', $this->plugin_rel_path ); //phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
	}
}
