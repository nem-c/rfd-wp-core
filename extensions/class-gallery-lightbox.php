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

class Gallery_Lightbox {
	protected $loader;

	public function __construct( Loader &$loader ) {
		$this->loader = $loader;
	}

	public function init() {
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'register_assets' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_assets' );
	}

	public function register_assets() {
		wp_register_script( 'baguette-box', RFD_CORE_ASSETS_URL . 'baguette-box/baguetteBox.min.js', [], '1.11.1', true );
		wp_add_inline_script( 'baguette-box', 'window.addEventListener("load", function() {var options={captions:function(t){var e=t.parentElement.getElementsByTagName("figcaption")[0];return!!e&&e.innerHTML}};baguetteBox.run(".wp-block-gallery",options);baguetteBox.run(".wp-block-image",options);});' );
		wp_register_style( 'baguette-box-css', RFD_CORE_ASSETS_URL . '/baguette-box/baguetteBox.min.css', [], '1.11.1' );
	}

	public function enqueue_assets() {

		$blocks            = apply_filters( 'rfd_ext_gallery_lightbox_allowed_blocks', [
			'gallery',
			'image',
			'rfd-live-auctions/auction-gallery',
		] );
		$has_block_on_page = false;
		foreach ( $blocks as $block ) {
			if ( true === has_block( $block ) ) {
				$has_block_on_page = true;
			}
		}

		wp_enqueue_script( 'baguette-box' );
		wp_enqueue_style( 'baguette-box-css' );
	}
}