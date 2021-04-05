<?php
/**
 * Comment meta box abstract
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
use \WP_Comment;

/**
 * Class Comment_Meta_Box
 *
 * @package RFD\Core\Abstracts\Admin\Meta_Boxes
 */
abstract class Comment_Meta_Box {

	/**
	 * Comment meta box ID
	 *
	 * @var string
	 */
	protected $id = 'custom-comment-meta-box';

	/**
	 * Comment meta box context
	 * Accepts 'normal' or 'side'. Default 'normal'.
	 *
	 * @var string
	 */
	protected $context = 'normal';

	/**
	 * Comment meta box priority
	 * Accepts 'high', 'core', 'default', or 'low'. Default 'default'.
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
	 * @var string Nonce action name
	 */
	protected $nonce_action = '';

	/**
	 * Comment meta box title
	 *
	 * @var string
	 */
	protected $title = 'Comment Meta Box';

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
		$meta_box = new static(); // @phpstan-ignore-line.
		$loader->add_action( 'add_meta_boxes', $meta_box, 'register' );
		$loader->add_action( 'edit_comment', $meta_box, 'maybe_save', $priority, 2 );
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
			'comment',
			$this->context,
			$this->priority
		);
	}

	/**
	 * Verifies nonce and user access
	 *
	 * @param int $comment_id Comment ID.
	 * @param string $nonce_value Nonce value.
	 *
	 * @return bool|WP_Error
	 */
	public function verify( int $comment_id, string $nonce_value ) {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce validation failed', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'edit_comment', $comment_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit comments', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		return true;
	}

	/**
	 * Determines should save be called.
	 *
	 * @param int $comment_id Comment ID.
	 * @param mixed $comment_data Comment meta data.
	 *
	 * @return bool
	 */
	public function maybe_save( int $comment_id, $comment_data ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			/* translators: name of missing nonce name */
			$error_text = _x( 'Nonce validation not set for %s', 'name of missing nonce name', $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
			$error      = new WP_Error( 'rfd-error', sprintf( $error_text, $this->id ) );
			Logger::log( $error, true ); // @phpstan-ignore-line.
		}

		$verified = $this->verify( $comment_id, sanitize_text_field( wp_unslash( $_POST[ $this->nonce_name ] ?? '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true ); // @phpstan-ignore-line.
		}

		return $this->save( $comment_id, $comment_data );
	}

	/**
	 * Renders html output for comments meta box
	 *
	 * @param WP_Comment $comment Actual WP_Comment object.
	 */
	abstract public function render( WP_Comment $comment ): void;

	/**
	 * Save function.
	 *
	 * @param int $comment_id Comment ID.
	 * @param mixed $comment_data Comment data.
	 *
	 * @return bool
	 */
	abstract public function save( int $comment_id, $comment_data ): bool;

	/**
	 * Renders nonce field
	 *
	 * @return string
	 */
	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}
