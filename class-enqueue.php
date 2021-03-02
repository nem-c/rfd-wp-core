<?php

/**
 * Enqueue library
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 */

namespace RFD\Core;

class Enqueue {

	protected array $admin_assets = array(
		'css' => array(),
		'js'  => array(),
	);

	protected array $frontend_assets = array(
		'css' => array(),
		'js'  => array(),
	);

	final public static function init( Loader $loader ) {
		$enqueue = new static();
		if ( true === is_admin() ) {
			$enqueue->load_admin_configuration();
			$loader->add_action( 'admin_enqueue_scripts', $enqueue, 'enqueue_admin_assets' );
		}
		$enqueue->load_frontend_configuration();
		$loader->add_action( 'wp_enqueue_scripts', $enqueue, 'enqueue_frontend_assets' );
	}

	public function enqueue_admin_assets( $hook ) {
		$this->enqueue_assets( $this->admin_assets, $hook );
	}

	public function enqueue_frontend_assets( $hook ) {
		$this->enqueue_assets( $this->frontend_assets, $hook );
	}

	public function enqueue_assets( $assets, $hook ) {
		foreach ( $assets['js'] as $js_asset ) {
			if ( false === is_array( $js_asset ) ) {
				$handle = $js_asset;
				wp_enqueue_script( $handle );
			} else {
				wp_enqueue_script(
					$js_asset['handle'],
					$js_asset['src'],
					$js_asset['deps'],
					$js_asset['ver'],
					$js_asset['in_footer']
				);
			}
		}

		foreach ( $assets['css'] as $css_asset ) {
			wp_enqueue_style(
				$css_asset['handle'],
				$css_asset['src'],
				$css_asset['deps'],
				$css_asset['ver']
			);
		}
	}

	protected function load_frontend_configuration() {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'enqueue.php';
		$config_array     = $this->load_config_file( $config_file_path );
		$this->store_configuration( $config_array );
	}

	protected function load_admin_configuration() {
		$config_file_path = RFD_CORE_CONFIG_PATH . 'admin/enqueue.php';
		$config_array     = $this->load_config_file( $config_file_path );
		$this->store_configuration( $config_array, 'admin' );
	}

	protected function store_configuration( array $config_array, string $type = 'frontend' ) {
		$type_attribute = $type . '_assets';

		$js_config  = $config_array['js'] ?? array();
		$css_config = $config_array['css'] ?? array();

		foreach ( $js_config as $handle => $config_data ) {
			if ( true === $config_data ) {
				$this->{$type_attribute}['js'][] = $handle;
			} else {
				$src = $config_data['src'] ?? '';
				if ( true === empty( $src ) ) {
					continue;
				}
				$this->{$type_attribute}['js'][] = array(
					'handle'    => $handle,
					'src'       => $config_data['src'],
					'deps'      => $config_data['deps'] ?? array(),
					'ver'       => $config_data['version'] ?? 0,
					'in_footer' => $config_data['in_footer'] ?? true,
				);
			}
		}

		foreach ( $css_config as $handle => $config_data ) {
			$src = $config_data['src'] ?? '';
			if ( true === empty( $src ) ) {
				continue;
			}
			$this->{$type_attribute}['css'][] = array(
				'handle' => $handle,
				'src'    => $config_data['src'],
				'deps'   => $config_data['deps'] ?? array(),
				'ver'    => $config_data['version'] ?? 0,
			);
		}
	}

	protected function load_config_file( string $config_file_path ): array {
		$config_array = array();

		if ( true === file_exists( $config_file_path ) ) {
			$config_array = include $config_file_path;
		}

		return $config_array;
	}
}
