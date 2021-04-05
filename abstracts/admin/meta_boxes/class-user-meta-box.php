<?php
/**
 * User meta box abstract
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Core
 */

namespace RFD\Core\Abstracts\Admin\Meta_Boxes;

use RFD\Core\Loader;
use RFD\Core\Logger;
use \WP_Error;
use \WP_User;

/**
 * Class User_Meta_Box
 *
 * @package RFD\Core\Abstracts\Admin\Meta_Boxes
 */
abstract class User_Meta_Box {

	/**
	 * User meta box ID
	 *
	 * @var string
	 */
	protected $id = 'custom-user-meta-box';

	/**
	 * User meta box context definition
	 *
	 * @var string
	 */
	protected $context = 'normal';

	/**
	 * User meta box priority
	 *
	 * @var string
	 */
	protected $priority = 'default';


	/**
	 * Nonce name to be used when running actions.
	 *
	 * @var string
	 */
	protected $nonce_name = '';

	/**
	 * Nonce save action name
	 *
	 * @var string
	 */
	protected $nonce_action = '';

	/**
	 * User meta box title
	 *
	 * @var string
	 */
	protected $title = 'User Meta Box';

	/**
	 * Comment meta box lang domain.
	 *
	 * @var string
	 */
	protected $lang_domain = 'rfd-core';

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$instance = new static(); // @phpstan-ignore-line.

		$loader->add_action( 'load-profile.php', $instance, 'add_meta_boxes', 25 );
		$loader->add_action( 'load-user-edit.php', $instance, 'trigger_meta_boxes', 25 );
		$loader->add_action( 'show_user_profile', $instance, 'do_meta_boxes', 25 );
		$loader->add_action( 'edit_user_profile', $instance, 'do_meta_boxes', 25 );

		$loader->add_action( 'add_meta_boxes', $instance, 'register' );
		$loader->add_action( 'edit_user_profile_update', $instance, 'maybe_save', $priority );
		$loader->add_action( 'personal_options_update', $instance, 'maybe_save', $priority );
	}

	/**
	 * Adds metabox on user page as it is not supported by default
	 */
	public function add_meta_boxes(): void {
		global $pagenow, $user_id;

		$user = null;

		if ( 'profile.php' === $pagenow ) {
			$user = get_current_user();
		} elseif ( 'user-edit.php' === $pagenow ) {
			$user = get_user_by( 'ID', $user_id );
		}
		do_action( 'add_meta_boxes', 'user', $user );
	}

	/**
	 * Display meta box holder.
	 *
	 * @param WP_User $user WP_User object.
	 */
	public function do_meta_boxes( WP_User $user ): void {
		echo '<div id="poststuff"><div id="postbox-container-2" class="postbox-container">';
		do_meta_boxes( 'user', 'normal', $user );
		echo '</div></div>';
	}

	/**
	 * Register meta box
	 */
	public function register(): void {
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

	/**
	 * Verifies nonce and user access
	 *
	 * @param int $user_id User ID.
	 * @param string $nonce_value Nonce value.
	 *
	 * @return WP_Error|bool
	 */
	public function verify( int $user_id, string $nonce_value ) {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce validation failed', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'edit_user', $user_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit users', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		return true;
	}

	/**
	 * Determines should save be called.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool
	 */
	public function maybe_save( int $user_id ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			/* translators: name of missing nonce name */
			$error_text = _x( 'Nonce validation not set for %s', 'name of missing nonce name', $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
			$error      = new WP_Error( 'rfd-error', sprintf( $error_text, $this->id ) );
			Logger::log( $error, true ); // @phpstan-ignore-line.
		}

		$verified = $this->verify( $user_id, sanitize_text_field( wp_unslash( $_POST[ $this->nonce_name ] ?? '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true ); // @phpstan-ignore-line.
		}

		return $this->save( $user_id );
	}

	/**
	 * Render user meta box.
	 *
	 * @param WP_User $user WP_User object.
	 */
	abstract public function render( WP_User $user ): void;

	/**
	 * Save user meta data.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return bool
	 */
	abstract public function save( int $user_id ): bool;

	/**
	 * Renders nonce field
	 *
	 * @return string
	 */
	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}
