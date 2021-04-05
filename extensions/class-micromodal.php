<?php
/**
 * Micromodal for Gutenberg Gallery
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 * @subpackage RFD\Core\Extensions
 */

namespace RFD\Core\Extensions;

use RFD\Core\Loader;
use RFD\Core\View;

/**
 * Class Micromodal
 *
 * @package RFD\Core\Extensions
 */
class Micromodal {
	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$micormodal_ext = new static(); // @phpstan-ignore-line

		$loader->add_action( 'wp_enqueue_scripts', $micormodal_ext, 'register_assets' );
		$loader->add_action( 'wp_enqueue_scripts', $micormodal_ext, 'enqueue_assets' );
		$loader->add_action( 'wp_footer', $micormodal_ext, 'modal_content' );
	}

	/**
	 * Show modal content in footer
	 */
	public function modal_content(): void {
		View::render_template( 'micromodal/modal.php', array(), '', RFD_CORE_VIEW_PATH );
	}

	/**
	 * Register assets.
	 */
	public function register_assets(): void {
		wp_register_script( 'micromodal', RFD_CORE_ASSETS_URL . 'micromodal/micromodal.js', array(), '0.4.6', true );
		wp_register_style( 'micromodal-css', RFD_CORE_ASSETS_URL . '/micromodal/micromodal.css', array(), '0.4.6' );

	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets(): void {
		wp_enqueue_script( 'micromodal' );
		wp_enqueue_style( 'micromodal-css' );
	}
}
