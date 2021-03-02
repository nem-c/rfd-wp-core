<?php
/**
 * Network Site Tabs generator
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core\Network;

use RFD\Core\Loader;

abstract class Site_Tab {

	protected Loader $loader;
	protected int $blog_id;

	protected string $save_action = '';
	protected string $nonce_action = 'save';
	protected string $nonce_name = '';

	protected string $tab_name;
	protected string $tab_label;
	protected string $tab_url;
	protected string $tab_cap = 'manage_sites';
	protected string $tab_menu_slug;

	public function __construct( Loader $loader ) {
		$this->loader = $loader;
	}

	public function init() {
		$this->nonce_name  = sprintf( '%s-%s', $this->tab_name, $this->blog_id );
		$this->save_action = sprintf( '%s_save', $this->tab_menu_slug );

		$this->loader->add_filter( 'network_edit_site_nav_links', $this, 'add_tabs' );
		$this->loader->add_action( 'network_admin_menu', $this, 'register_menu' );
		$this->loader->add_action( 'network_admin_edit_' . $this->tab_menu_slug . '_save', $this, 'form_handler' );
	}

	public function add_tabs( $tabs ) {
		$tabs[ $this->tab_name ] = array(
			'label' => $this->tab_label,
			'url'   => $this->tab_url,
			'cap'   => $this->tab_cap,
		);

		return $tabs;
	}

	public function register_menu() {
		add_submenu_page(
			'sites.php',
			__( 'Edit website' ),
			'',
			$this->tab_cap,
			$this->tab_menu_slug,
			array(
				$this,
				'page_handler',
			)
		);
	}

	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}

	public function check_nonce() {
		check_admin_referer( $this->nonce_action, $this->nonce_name );
	}

	protected function get_blog_id() {
		if ( true === isset( $_POST['blog_id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->blog_id = intval( $_POST['blog_id'] ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		} elseif ( true === isset( $_GET['id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->blog_id = intval( $_GET['id'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		if ( $this->blog_id <= 0 ) {
			wp_die( esc_attr__( 'Sorry, this feature is not available from menu' ) );
		}
	}

	abstract public function page_handler();

	abstract public function form_handler();
}
