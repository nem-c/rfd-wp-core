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

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

/**
 * Class I18n
 */
class I18n {

	protected string $domain = 'rfd-core'; //phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected string $plugin_rel_path = '';

	final public static function init( Loader $loader, $props = array() ) {
		$i18n = new static();
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
