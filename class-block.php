<?php

/**
 * Gutenberg block abstract
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 */

namespace RFD\Core;

abstract class Block {

	protected ?string $build_config = null;
	protected ?string $name = null;

	protected ?string $editor_handle = null;

	protected ?string $editor_script_src = null;
	protected ?string $editor_script_path = null;
	protected array $editor_script_dependencies = array(
		'wp-blocks',
		'wp-element',
		'wp-i18n',
		'wp-polyfill',
		'wp-editor',
	);

	protected ?string $editor_style_src = null;
	protected ?string $editor_style_path = null;
	protected array $editor_style_dependencies = array(
		'wp-edit-blocks',
	);

	protected ?string $frontend_style_handle = null;
	protected ?string $frontend_style_src = null;
	protected ?string $frontend_style_path = null;
	protected array $frontend_style_dependencies = array();

	protected string $lang_domain = 'WordPress';

	public function register() {

		$this->register_editor();
		$this->register_frontend();

		if ( true === file_exists( $this->build_config ) ) {
			register_block_type_from_metadata(
				$this->build_config,
				array(
					'render_callback' => array( $this, 'render_block' ),
				)
			);
		} else {
			$block_args = array(
				'editor_script' => $this->editor_handle,
			);
			if ( false === empty( $this->editor_style_path ) ) {
				$block_args['editor_style'] = $this->editor_handle;
			}
			if ( false === empty( $this->frontend_style_handle ) ) {
				$block_args['style'] = $this->frontend_style_handle;
			}

			register_block_type( $this->name, $block_args );
		}
	}

	public function register_editor() {
		wp_register_script(
			$this->editor_handle,
			$this->editor_script_src,
			$this->editor_script_dependencies,
			filemtime( $this->editor_script_path ),
			true
		);
		wp_set_script_translations( $this->editor_handle, $this->lang_domain );

		if ( false === empty( $this->editor_style_handle ) ) {
			wp_register_style(
				$this->editor_handle,
				$this->editor_style_src,
				$this->editor_style_dependencies,
				filemtime( $this->editor_style_path )
			);
		}
	}

	public function register_frontend() {
		if ( false === empty( $this->frontend_style_handle ) ) {
			wp_register_style(
				$this->frontend_style_handle,
				$this->frontend_style_src,
				$this->frontend_style_dependencies,
				filemtime( $this->frontend_style_path )
			);
		}
	}
}
