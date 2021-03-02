<?php

namespace RFD\Core\Admin;

use RFD\Core\Loader;

class Menu {
	protected $menus_pages = [];
	protected $submenus_pages = [];

	final public static function init( Loader &$loader ) {
		$menu = new static();
		$loader->add_action( 'admin_menu', $menu, 'register' );
	}

	public function register() {
		$this->load_settings_file();
		$this->register_menus();
		$this->register_submenus();
	}

	protected function register_menus() {
		foreach ( $this->menus_pages as $menu_page ) {

			$page_title    = $menu_page['page_title'];
			$menu_title    = $menu_page['menu_title'];
			$compatibility = $menu_page['capability'];
			$menu_slug     = $menu_page['menu_slug'];
			if ( true === isset( $menu_page['callback'] ) ) {
				$callback = $menu_page['callback'];
			} else {
				$callback = false;
			}
			$icon_url = $menu_page['icon_url'];
			$position = $menu_page['position'];

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

	protected function register_submenus() {
		foreach ( $this->submenus_pages as $submenu_page ) {

			$parent_slug = $submenu_page['parent_slug'];
			$page_title  = $submenu_page['page_title'];
			$menu_title  = $submenu_page['menu_title'];
			$capability  = $submenu_page['capability'];
			$menu_slug   = $submenu_page['menu_slug'];
			if ( true === isset( $submenu_page['callback'] ) ) {
				$callback = $submenu_page['callback'];
			} else {
				$callback = false;
			}
			$position = $submenu_page['position'] ?? null;

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

	private function load_settings_file() {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'admin/menu.php';
		$config           = [];
		// if file does not exist return false
		if ( true === file_exists( $config_file_path ) ) {
			$config = include $config_file_path;
		}

		foreach ( $config as $menu_id => $menu_config ) {

			$menu_page_title = $menu_config['page_title'];
			$menu_menu_title = $menu_config['menu_title'];
			$menu_slug       = $menu_config['slug'];
			$menu_capability = apply_filters( 'rfd_menu_' . $menu_id . '_capabilities', $menu_config['capability'] );
			$menu_callback   = $menu_config['callback'];
			$menu_icon       = $menu_config['icon'];
			$menu_position   = $menu_config['position'];

			if ( true === empty( $menu_slug ) ) {
				continue;
			}

			array_push( $this->menus_pages, [
				'page_title' => $menu_page_title,
				'menu_title' => $menu_menu_title,
				'capability' => $menu_capability,
				'menu_slug'  => $menu_slug,
				'function'   => $menu_callback,
				'icon_url'   => $menu_icon,
				'position'   => $menu_position,
			] );

			$submenus = [];
			if ( true === isset( $menu_config['submenus'] ) ) {
				$submenus = $menu_config['submenus'];
			}


			foreach ( $submenus as $submenu_id => $submenu_config ) {

				$submenu_page_title = $submenu_config['page_title'];
				$submenu_menu_title = $submenu_config['menu_title'];
				$submenu_capability = apply_filters( 'rfd_submenu_ ' . $submenu_id . '_capabilities', $submenu_config['capability'] );
				$submenu_callback   = $submenu_config['callback'];
				$submenu_position   = $submenu_config['position'] ?? null;

				if ( true === isset( $submenu_config['slug'] ) ) {
					$submenu_slug = $submenu_config['slug'];
				} else {
					$submenu_slug = null;
				}

				if ( true === empty( $submenu_slug ) ) {
					continue;
				}

				array_push( $this->submenus_pages,
					[
						'parent_slug' => $menu_slug,
						'page_title'  => $submenu_page_title,
						'menu_title'  => $submenu_menu_title,
						'menu_slug'   => $submenu_slug,
						'capability'  => $submenu_capability,
						'callback'    => $submenu_callback,
						'position'    => $submenu_position,
					]
				);
			}

		}
	}
}