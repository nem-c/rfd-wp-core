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

class Micromodal {
	protected Loader $loader;

	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	public function init() {
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'register_assets' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_assets' );
		$this->loader->add_action( 'wp_footer', $this, 'modal_content' );
	}

	/**
	 * Show modal content in footer
	 */
	public function modal_content(): void {
		View::render_template( 'micromodal/modal.php', array(), null, RFD_CORE_VIEW_PATH );
	}

	public function register_assets() {
		wp_register_script( 'micromodal', RFD_CORE_ASSETS_URL . 'micromodal/micromodal.js', array(), '0.4.6', true );
		wp_register_style( 'micromodal-css', RFD_CORE_ASSETS_URL . '/micromodal/micromodal.css', array(), '0.4.6' );

	}

	public function enqueue_assets() {
		$blocks = apply_filters(
			'rfd_ext_micromodal_allowed_blocks',
			array(
				'gallery',
				'image',
				'rfd-live-auctions/auction-gallery',
			)
		);

		//TODO: $has_block_on_page has no effect at this moment
		$has_block_on_page = false;
		foreach ( $blocks as $block ) {
			if ( true === has_block( $block ) ) {
				$has_block_on_page = true;
			}
		}

		wp_enqueue_script( 'micromodal' );
		wp_enqueue_style( 'micromodal-css' );
	}
}
