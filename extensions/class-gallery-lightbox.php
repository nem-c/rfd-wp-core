<?php
/**
 * Lightbox for Gutenberg Gallery
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 * @subpackage RFD\Core\Extensions
 */

namespace RFD\Core\Extensions;

use RFD\Core\Loader;

/**
 * Class Gallery_Lightbox
 *
 * @package RFD\Core\Extensions
 */
class Gallery_Lightbox {

	/**
	 * Loader object
	 *
	 * @var Loader
	 */
	protected $loader;

	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$gallery_lightbox_ext = new static(); // @phpstan-ignore-line

		$loader->add_action( 'wp_enqueue_scripts', $gallery_lightbox_ext, 'register_assets' );
		$loader->add_action( 'wp_enqueue_scripts', $gallery_lightbox_ext, 'enqueue_assets' );
	}

	/**
	 * Register assets
	 */
	public function register_assets(): void {
		wp_register_script( 'baguette-box', RFD_CORE_ASSETS_URL . 'baguette-box/baguetteBox.min.js', array(), '1.11.1', true );
		wp_add_inline_script( 'baguette-box', 'window.addEventListener("load", function() {var options={captions:function(t){var e=t.parentElement.getElementsByTagName("figcaption")[0];return!!e&&e.innerHTML}};baguetteBox.run(".wp-block-gallery",options);baguetteBox.run(".wp-block-image",options);});' );
		wp_register_style( 'baguette-box-css', RFD_CORE_ASSETS_URL . '/baguette-box/baguetteBox.min.css', array(), '1.11.1' );
	}

	/**
	 * Enqueue assets
	 */
	public function enqueue_assets(): void {
		wp_enqueue_script( 'baguette-box' );
		wp_enqueue_style( 'baguette-box-css' );
	}
}
