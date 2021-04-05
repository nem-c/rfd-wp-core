<?php
/**
 * View generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;

/**
 * Class View
 *
 * @package RFD\Core
 */
class View {
	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name Template name.
	 * @param array $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 */
	public static function render_template( string $template_name, $args = array(), $template_path = '', $default_path = '' ): void {

		$template = self::locate_template( $template_name, $template_path, $default_path );

		if ( empty( $args ) === false && is_array( $args ) ) {
			unset( $args['action_args'] );
			extract( $args ); // @codingStandardsIgnoreLine
		}
		include $template;
	}

	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name Template name.
	 * @param array $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 *
	 * @return string
	 */
	public static function get_template_html( string $template_name, $args = array(), $template_path = '', $default_path = '' ): string {
		ob_start();
		self::render_template( $template_name, $args, $template_path, $default_path );

		$html = ob_get_clean();

		return (string) $html;
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/$template_name
	 * $default_path/$template_name
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 *
	 * @return string
	 */
	public static function locate_template( string $template_name, $template_path = '', $default_path = '' ): string {
		if ( true === empty( $template_path ) ) {
			$template_path = basename( plugin_dir_path( dirname( __FILE__ ) ) );
		}
		if ( true === empty( $default_path ) ) {
			$default_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);
		// Get default template/.
		if ( empty( $template ) === true ) {
			$template = $default_path . $template_name;
		}

		return $template;
	}
}
