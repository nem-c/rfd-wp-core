<?php

namespace RFD\Core\Abstracts\Admin\Meta_Boxes;

use RFD\Core\Loader;
use RFD\Core\Logger;
use \WP_Error;
use \WP_User;

abstract class User_Meta_Box {

	protected string $id = 'custom-user-meta-box'; //phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected string $context = 'normal';

	protected string $priority = 'default';

	protected string $nonce_name = '';

	protected string $nonce_action = '';

	protected string $title = 'User Meta Box';

	protected string $lang_domain = 'rfd-core';

	final public static function init( Loader $loader, $priority = 10 ) {
		$instance = new static();

		$loader->add_action( 'load-profile.php', $instance, 'add_meta_boxes', 25 );
		$loader->add_action( 'load-user-edit.php', $instance, 'trigger_meta_boxes', 25 );
		$loader->add_action( 'show_user_profile', $instance, 'do_meta_boxes', 25 );
		$loader->add_action( 'edit_user_profile', $instance, 'do_meta_boxes', 25 );

		$loader->add_action( 'add_meta_boxes', $instance, 'register' );
		$loader->add_action( 'edit_user_profile_update', $instance, 'maybe_save', $priority );
		$loader->add_action( 'personal_options_update', $instance, 'maybe_save', $priority );
	}

	public function add_meta_boxes() {
		global $pagenow, $user_id;

		$user = null;

		if ( 'profile.php' === $pagenow ) {
			$user = get_current_user();
		} elseif ( 'user-edit.php' === $pagenow ) {
			$user = get_user_by( 'ID', $user_id );
		}
		do_action( 'add_meta_boxes', 'user', $user );
	}

	public function do_meta_boxes( WP_User $user ): void {
		echo '<div id="poststuff"><div id="postbox-container-2" class="postbox-container">';
		do_meta_boxes( 'user', 'normal', $user );
		echo '</div></div>';
	}

	public function register() {
		$title = __( $this->title, $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain

		add_meta_box(
			$this->id,
			$title,
			array( $this, 'render' ),
			'user',
			$this->context,
			$this->priority
		);
	}

	public function render( WP_User $user ): void {

	}

	public function verify( $user_id, $nonce_value ): ?WP_Error {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce validation failed', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'edit_user', $user_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit users', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		return null;
	}

	public function maybe_save( $user_id ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			/* translators: name of missing nonce name */
			$error_text = _x( 'Nonce validation not set for %s', 'name of missing nonce name', $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
			$error      = new WP_Error( 'rfd-error', sprintf( $error_text, $this->id ) );
			Logger::log( $error, true );
		}

		$verified = $this->verify( $user_id, sanitize_text_field( $_POST[ $this->nonce_name ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true );
		}

		return $this->save( $user_id );
	}

	public function save( $user_id ): bool {
		return true;
	}

	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}
