<?php

namespace RFD\Core\Abstracts\Admin\Meta_Boxes;

use RFD\Core\Loader;
use RFD\Core\Logger;
use \WP_Error;
use \WP_Comment;

abstract class Comment_Meta_Box {

	protected $id = 'custom-comment-meta-box';
	protected $context = 'normal';
	protected $priority = 'default';

	protected $nonce_name = '';
	protected $nonce_action = '';

	protected $title = 'Comment Meta Box';
	protected $lang_domain = 'rfd-core';

	final public static function init( Loader &$loader, $priority = 10 ) {
		$meta_box = new static();
		$loader->add_action( 'add_meta_boxes', $meta_box, 'register' );
		$loader->add_action( 'edit_comment', $meta_box, 'maybe_save', $priority, 2 );
	}

	public function register() {
		add_meta_box(
			$this->id,
			__( $this->title, $this->lang_domain ),
			[ $this, 'render' ],
			'comment',
			$this->context,
			$this->priority
		);
	}

	public function render( WP_Comment $comment ): void {

	}

	public function verify( $comment_id, $nonce_value ) {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce validation failed', $this->lang_domain ) );
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'edit_comment', $comment_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit comments', $this->lang_domain ) );
		}
	}

	public function maybe_save( $comment_id, $comment_data ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			$error = new WP_Error( 'rfd-error', __( sprintf( 'Nonce validation not set for %s', $this->id ), $this->lang_domain ) );
			Logger::log( $error, true );
		}

		$verified = $this->verify( $comment_id, sanitize_text_field( $_POST[ $this->nonce_name ] ) );
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true );
		}

		return $this->save( $comment_id, $comment_data );
	}

	public function save( $comment_id, $comment_data ): bool {
		return true;
	}

	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}