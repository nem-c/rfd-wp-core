<?php
/**
 * Network Site Tabs generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Abstracts\Network;

use RFD\Core\Loader;

/**
 * Class Site_Tab
 *
 * @package RFD\Core\Abstracts\Network
 */
abstract class Site_Tab {

	/**
	 * Blog ID
	 *
	 * @var int
	 */
	protected $blog_id;

	/**
	 * Save action name
	 *
	 * @var string
	 */
	protected $save_action = '';

	/**
	 * Nonce action name
	 *
	 * @var string
	 */
	protected $nonce_action = 'save';

	/**
	 * Nonce name
	 *
	 * @var string
	 */
	protected $nonce_name = '';

	/**
	 * Tab name
	 *
	 * @var string
	 */
	protected $tab_name;

	/**
	 * Tab label
	 *
	 * @var string
	 */
	protected $tab_label;

	/**
	 * Tab URL
	 *
	 * @var string
	 */
	protected $tab_url;

	/**
	 * Tab capabilities
	 *
	 * @var string
	 */
	protected $tab_cap = 'manage_sites';

	/**
	 * Tab menu slug
	 *
	 * @var string
	 */
	protected $tab_menu_slug;

	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$site_tab = new static(); // @phpstan-ignore-line.

		$site_tab->nonce_name  = sprintf( '%s-%s', $site_tab->tab_name, $site_tab->blog_id );
		$site_tab->save_action = sprintf( '%s_save', $site_tab->tab_menu_slug );

		$loader->add_filter( 'network_edit_site_nav_links', $site_tab, 'add_tabs' );
		$loader->add_action( 'network_admin_menu', $site_tab, 'register_menu' );
		$loader->add_action( 'network_admin_edit_' . $site_tab->tab_menu_slug . '_save', $site_tab, 'form_handler' );
	}

	/**
	 * Add network site tabs.
	 *
	 * @param array $tabs Current tabs.
	 *
	 * @return array
	 */
	public function add_tabs( array $tabs ): array {
		$tabs[ $this->tab_name ] = array(
			'label' => $this->tab_label,
			'url'   => $this->tab_url,
			'cap'   => $this->tab_cap,
		);

		return $tabs;
	}

	/**
	 * Register Menu
	 */
	public function register_menu(): void {
		add_submenu_page(
			'sites.php',
			__( 'Edit website' ),
			'',
			$this->tab_cap,
			$this->tab_menu_slug,
			array(
				$this,
				'render',
			)
		);
	}

	/**
	 * Generate nonce fields
	 *
	 * @return string
	 */
	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}

	/**
	 * Check nonce before save
	 */
	public function check_nonce(): void {
		check_admin_referer( $this->nonce_action, $this->nonce_name );
	}

	/**
	 * Get blog ID from various sources.
	 */
	protected function get_blog_id(): void {
		if ( true === isset( $_POST['blog_id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->blog_id = intval( $_POST['blog_id'] ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		} elseif ( true === isset( $_GET['id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->blog_id = intval( $_GET['id'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( $this->blog_id <= 0 ) {
			wp_die( esc_attr__( 'Sorry, this feature is not available from menu' ) );
		}
	}

	/**
	 * Tab render content
	 *
	 * @return mixed
	 */
	abstract public function render();

	/**
	 * Tab form/save handler
	 *
	 * @return mixed
	 */
	abstract public function form_handler();
}
