<?php
/**
 * Admin menu updater.
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Admin;

use RFD\Core\Loader;

/**
 * Class Menu
 *
 * @package RFD\Core\Admin
 */
class Menu {

	/**
	 * Registered top menus.
	 *
	 * @var array
	 */
	protected $menus_pages = array();

	/**
	 * Registered submenus.
	 *
	 * @var array
	 */
	protected $submenus_pages = array();

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
	 * Register menus.
	 */
	public function register(): void {
		$this->preload_settings();
		$this->register_menus();
		$this->register_submenus();
	}

	/**
	 * Register top admin menu pages.
	 */
	protected function register_menus(): void {
		foreach ( $this->menus_pages as $menu_page ) {

			$page_title    = $menu_page['page_title'];
			$menu_title    = $menu_page['menu_title'];
			$compatibility = $menu_page['capability'];
			$menu_slug     = $menu_page['menu_slug'];
			$callback      = $this->fetch_callback( $menu_page );
			$icon_url      = $menu_page['icon_url'];
			$position      = $menu_page['position'];

			if ( true === is_callable( $callback, true ) ) {
				add_menu_page(
					$page_title,
					$menu_title,
					$compatibility,
					$menu_slug,
					$callback,
					$icon_url,
					$position
				);

				remove_submenu_page( $menu_slug, $menu_slug );
			}
		}
	}

	/**
	 * Register submenu items
	 */
	protected function register_submenus(): void {
		foreach ( $this->submenus_pages as $submenu_page ) {

			$parent_slug = $submenu_page['parent_slug'];
			$page_title  = $submenu_page['page_title'];
			$menu_title  = $submenu_page['menu_title'];
			$capability  = $submenu_page['capability'];
			$menu_slug   = $submenu_page['menu_slug'];
			$callback    = $this->fetch_callback( $submenu_page );
			$position    = $submenu_page['position'] ?? null;

			if ( true === is_callable( $callback, true ) ) {
				add_submenu_page(
					$parent_slug,
					$page_title,
					$menu_title,
					$capability,
					$menu_slug,
					$callback,
					$position
				);
			}
		}
	}

	/**
	 * Fetch callback from config array.
	 *
	 * @param array $menu_config Menu config array.
	 *
	 * @return array|string|bool
	 */
	private function fetch_callback( array $menu_config ) {
		if ( true === isset( $menu_config['callback'] ) ) {
			$callback = $menu_config['callback'];
		} else {
			$callback = false;
		}

		return $callback;
	}

	/**
	 * Preload settings
	 */
	public function preload_settings(): void {
		$config = $this->load_settings_file();

		$this->preload_menu_pages( $config );
	}

	/**
	 * Preload menu pages from config array.
	 *
	 * @param array $config Config array.
	 */
	private function preload_menu_pages( array $config ): void {
		foreach ( $config as $menu_id => $menu_config ) {

			$menu_slug = $this->store_menu_page( $menu_id, $menu_config );
			$this->preload_submenu_pages( $menu_slug, $menu_config['submenus'] ?? array() );
		}
	}

	/**
	 * Store menu page
	 *
	 * @param string $menu_id Menu ID.
	 * @param array $menu_config Menu config.
	 *
	 * @return string
	 */
	protected function store_menu_page( string $menu_id, array $menu_config ): string {
		$menu_page_title = $menu_config['page_title'] ?? '';
		$menu_menu_title = $menu_config['menu_title'] ?? '';
		$menu_slug       = $menu_config['slug'] ?? '';
		$menu_capability = apply_filters( 'rfd_menu_' . $menu_id . '_capabilities', $menu_config['capability'] ?? '' );
		$menu_callback   = $menu_config['callback'] ?? null;
		$menu_icon       = $menu_config['icon'] ?? '';
		$menu_position   = $menu_config['position'] ?? '';

		if ( false === empty( $menu_slug ) ) {
			array_push(
				$this->menus_pages,
				array(
					'page_title' => $menu_page_title,
					'menu_title' => $menu_menu_title,
					'capability' => $menu_capability,
					'menu_slug'  => $menu_slug,
					'function'   => $menu_callback,
					'icon_url'   => $menu_icon,
					'position'   => $menu_position,
				)
			);
		}

		return $menu_slug;
	}

	/**
	 * Preload submenu pages from config array
	 *
	 * @param string $menu_slug Parent menu slug.
	 * @param array $config Submenus config.
	 */
	protected function preload_submenu_pages( string $menu_slug, array $config ): void {
		foreach ( $config as $submenu_id => $submenu_config ) {

			$submenu_page_title = $submenu_config['page_title'] ?? '';
			$submenu_menu_title = $submenu_config['menu_title'] ?? '';
			$submenu_slug       = $submenu_config['slug'] ?? '';
			$submenu_capability = apply_filters( 'rfd_submenu_ ' . $submenu_id . '_capabilities', $submenu_config['capability'] ); //phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			$submenu_callback   = $submenu_config['callback'] ?? null;
			$submenu_position   = $submenu_config['position'] ?? 99;

			array_push(
				$this->submenus_pages,
				array(
					'parent_slug' => $menu_slug,
					'page_title'  => $submenu_page_title,
					'menu_title'  => $submenu_menu_title,
					'menu_slug'   => $submenu_slug,
					'capability'  => $submenu_capability,
					'callback'    => $submenu_callback,
					'position'    => $submenu_position,
				)
			);
		}
	}

	/**
	 * Load pages from config file.
	 */
	private function load_settings_file(): array {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'admin/menu.php';
		$config           = array();
		// if file does not exist return false.
		if ( true === file_exists( $config_file_path ) ) {
			$config = include $config_file_path;
		}

		return $config;
	}
}
