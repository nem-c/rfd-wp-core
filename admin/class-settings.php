<?php
/**
 * Settings page generator.
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Admin;

use RFD\Core\Input;
use RFD\Core\Loader;

/**
 * Class Settings
 *
 * @package RFD\Core\Admin
 */
class Settings {

	/**
	 * Settings pages.
	 *
	 * @var array
	 */
	protected $pages = array();

	/**
	 * Settings sections.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Settings fields.
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$menu = new static(); // @phpstan-ignore-line
		$loader->add_action( 'admin_menu', $menu, 'register' );
	}

	/**
	 * Register
	 */
	public function register(): void {
		$this->preload_settings();
		$this->register_menu();
		$this->register_sections();
		$this->register_fields();
	}

	/**
	 * Register menu
	 */
	public function register_menu(): void {
		foreach ( $this->pages as $page ) {
			register_setting( $page['id'], $page['id'] );
			add_options_page(
				$page['page_title'],
				$page['menu_title'],
				$page['capabilities'],
				$page['id'],
				$page['callback']
			);
		}
	}

	/**
	 * Register sections
	 */
	public function register_sections(): void {
		foreach ( $this->sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				$section['callback'],
				$section['page']
			);
		}
	}

	/**
	 * Register fields
	 */
	public function register_fields(): void {
		foreach ( $this->fields as $field ) {
			add_settings_field(
				$field['id'],
				$field['title'],
				$field['callback'],
				$field['page'],
				$field['section'],
				$field['args']
			);
		}
	}

	/**
	 * Preload settings
	 */
	private function preload_settings(): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh,Generic.Metrics.NestingLevel.MaxExceeded
		$config = $this->load_settings_file();

		foreach ( $config as $page_block ) {
			$page           = $page_block['id'];
			$page_options   = get_option( $page );
			$capabilities   = apply_filters( 'rfd_settings_' . $page . '_capabilities', 'manage_options' );
			$sections_block = $page_block['sections'];

			array_push(
				$this->pages,
				array(
					'id'           => $page,
					'page_title'   => $page_block['page_title'],
					'menu_title'   => $page_block['menu_title'],
					'capabilities' => $capabilities,
					'callback'     => $page_block['callback'],
				)
			);

			foreach ( $sections_block as $section_block ) {
				if ( false === isset( $section_block['id'] ) ) {
					continue;
				}
				$section      = $section_block['id'];
				$fields_block = $section_block['fields'];

				array_push(
					$this->sections,
					array(
						'id'       => $section,
						'title'    => $section_block['title'],
						'callback' => $section_block['callback'],
						'page'     => $page,
					)
				);

				foreach ( $fields_block as $field_block ) {
					$field = $field_block['id'];
					array_push(
						$this->fields,
						array(
							'id'       => $field,
							'title'    => $field_block['title'],
							'callback' => function () use ( $page, $page_options, $field, $field_block ) {
								echo Input::render( //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									array(
										'id'          => $field,
										'field_name'  => $page . '[' . $field . ']',
										'field_value' => $page_options[ $field ] ?? null,
										'type'        => $field_block['type'],
										'title'       => $field_block['title'],
										'args'        => $field_block['args'],
									)
								);
							},
							'page'     => $page,
							'section'  => $section,
							'args'     => array(
								'label_for' => $field_block['label_for'],
								'class'     => 'rfd_input_' . $field,
							),
						)
					);
				}
			}
		}
	}

	/**
	 * Load settings file
	 *
	 * @return array
	 */
	private function load_settings_file(): array {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'admin/settings.php';
		$config           = array();
		if ( true === file_exists( $config_file_path ) ) {
			$config = include $config_file_path;
		}

		return $config;
	}
}
