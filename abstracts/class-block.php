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

/**
 * Class Block
 *
 * @package RFD\Core
 */
abstract class Block {

	/**
	 * Block build Config
	 *
	 * @var null $build_config
	 */
	protected $build_config = null;

	/**
	 * Block Name
	 *
	 * @var null $name
	 */
	protected $name = null;

	/**
	 * Block editor handle
	 *
	 * @var null $editor_handle
	 */
	protected $editor_handle = null;

	/**
	 * Block editor script file src
	 *
	 * @var null $editor_script_src
	 */
	protected $editor_script_src = null;

	/**
	 * Block editor script file path
	 *
	 * @var null $editor_script_path
	 */
	protected $editor_script_path = null;

	/**
	 * Block dependencies
	 *
	 * @var string[] $editor_script_dependencies
	 */
	protected $editor_script_dependencies = array(
		'wp-blocks',
		'wp-element',
		'wp-i18n',
		'wp-polyfill',
		'wp-editor',
	);

	/**
	 * Block editor style file src
	 *
	 * @var null $editor_style_src
	 */
	protected $editor_style_src = null;

	/**
	 * Block editor style file path
	 *
	 * @var null $editor_style_path
	 */
	protected $editor_style_path = null;

	/**
	 * Block editor style dependencies
	 *
	 * @var string[] $editor_style_dependencies
	 */
	protected $editor_style_dependencies = array(
		'wp-edit-blocks',
	);

	/**
	 * Frontend style handle
	 *
	 * @var null $frontend_style_handle
	 */
	protected $frontend_style_handle = null;

	/**
	 * Frontend style src
	 *
	 * @var null $frontend_style_src
	 */
	protected $frontend_style_src = null;

	/**
	 * Frontend style path
	 *
	 * @var null $frontend_style_path
	 */
	protected $frontend_style_path = null;

	/**
	 * Frontend style dependencies
	 *
	 * @var string[] $frontend_style_dependencies
	 */
	protected $frontend_style_dependencies = array();

	/**
	 * Lang domain to be used.
	 * While this is not by standard, using it in another way would require wrapper or series of wrappers.
	 *
	 * @var string $lang_domain
	 */
	protected $lang_domain = 'WordPress';

	/**
	 * Register block
	 */
	public function register() {

		$this->register_editor();
		$this->register_frontend();

		if ( true === file_exists( $this->build_config ) ) {
			$this->register_from_file();
		} else {
			$this->register_from_data();
		}
	}

	/**
	 * Register new block from metadata
	 *
	 * @see register_block_type_from_metadata()
	 */
	protected function register_from_file() {
		register_block_type_from_metadata(
			$this->build_config,
			array(
				'render_callback' => array( $this, 'render_block' ),
			)
		);
	}

	/**
	 * Registers new block with data set in object
	 *
	 * @see register_block_type()
	 */
	protected function register_from_data() {
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

	/**
	 * Register editor styles and scripts
	 */
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

	/**
	 * Register frontend styles and scripts
	 */
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
