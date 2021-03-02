<?php

namespace RFD\Core\Abstracts\Admin\Meta_Boxes;

use RFD\Core\Loader;
use RFD\Core\Logger;
use \WP_Error;

abstract class Post_Meta_Box {

	protected string $id = 'custom-post-meta-box'; //phpcs:ignore Generic.PHP.Syntax.PHPSyntax

	protected string $screen = 'post-type';

	protected string $context = 'side';

	protected string $priority = 'default';

	protected string $nonce_name = '';

	protected string $nonce_action = '';

	protected string $title = 'Post Meta Box';

	protected string $lang_domain = '';

	final public static function init( Loader $loader, $priority = 10 ) {
		$meta_box = new static();
		$loader->add_action( 'add_meta_boxes', $meta_box, 'register' );
		$loader->add_action( 'save_post', $meta_box, 'maybe_save', $priority, 2 );
	}

	public function register() {
		$title = __( $this->title, $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain

		add_meta_box(
			$this->id,
			$title,
			array( $this, 'render' ),
			$this->screen,
			$this->context,
			$this->priority
		);
	}

	public function render( $post ): void {

	}

	public function verify( $post_id, $nonce_value ) {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce does not match', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit this post', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		return true;
	}

	public function maybe_save( $post_id, $post ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			/* translators: name of missing nonce name */
			$error_text = _x( 'Nonce validation not set for %s', 'name of missing nonce name', $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
			$error      = new WP_Error( 'rfd-error', sprintf( $error_text, $this->id ) );
			Logger::log( $error, true );
		}

		// if nonce is not set or nonce is different than define or is is auto-save: skip
		if ( false === isset( $_POST[ $this->nonce_name ] ) || true === wp_is_post_autosave( $post_id ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			return false;
		}

		$verified = $this->verify( $post_id, sanitize_text_field( $_POST[ $this->nonce_name ] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true );
		}

		return $this->save( $post_id, $post );
	}

	public function save( $post_id, $post ): bool {
		return true;
	}

	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}
