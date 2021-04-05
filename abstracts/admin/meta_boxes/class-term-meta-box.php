<?php
/**
 * Term meta box abstract
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

/**
 * Class User_Meta_Box
 *
 * @package RFD\Core\Abstracts\Admin\Meta_Boxes
 */
abstract class Term_Meta_Box {
	/**
	 * Term meta box ID
	 *
	 * @var string
	 */
	protected $id = 'custom-term-meta-box';

	/**
	 * Term taxonomy
	 *
	 * @var string
	 */
	protected $taxonomy = 'category';

	/**
	 * Term meta box context definition
	 *
	 * @var string
	 */
	protected $context = 'normal';

	/**
	 * Term meta box priority
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
	protected $nonce_action = 'save';

	/**
	 * Term meta box title
	 *
	 * @var string
	 */
	protected $title = 'Woo Tables for Variable Products';

	/**
	 * Term meta box lang domain.
	 *
	 * @var string
	 */
	protected $lang_domain = 'rfd-woo-variable-table';

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$term_meta_box = new static(); // @phpstan-ignore-line.

		$loader->add_action( 'load-edit-tags.php', $term_meta_box, 'add_meta_boxes', 25 );
		$loader->add_action( $term_meta_box->taxonomy . '_edit_form', $term_meta_box, 'do_meta_boxes', 25 );
		$loader->add_action( $term_meta_box->taxonomy . '_add_form_fields', $term_meta_box, 'do_meta_boxes', 25 );

		$loader->add_action( 'add_meta_boxes', $term_meta_box, 'register' );
		$loader->add_action( 'saved_term', $term_meta_box, 'maybe_save', $priority, 4 );
	}

	/**
	 * Adds metabox on user page as it is not supported by default
	 */
	public function add_meta_boxes(): void {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tag_id = intval( sanitize_text_field( wp_unslash( $_GET['tag_ID'] ?? 0 ) ) );
		$tag    = null;

		/**
		 * Same meta box is displayed on Add new term, where tag_ID is not available.
		 * Because of this it is allowed to have null for tag/term.
		 */
		if ( 0 < $tag_id ) {
			$tag = get_term_by( 'id', $tag_id );
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$taxonomy = sanitize_text_field( wp_unslash( $_GET['taxonomy'] ?? '' ) );

		/**
		 * If taxonomy is not defined, or different than defined in meta box - skip.
		 */
		if ( $this->taxonomy !== $taxonomy ) {
			return;
		}
		do_action( 'add_meta_boxes', 'term', $tag, $this->taxonomy );
	}

	/**
	 * Display meta box holder.
	 *
	 * @param mixed $tag Tag object.
	 */
	public function do_meta_boxes( $tag ): void {
		echo '<style>#poststuff {min-width: auto; overflow: auto;} #' . $this->id . ' .handle-actions {display: none;} #' . $this->id . ' .postbox-header .hndle {cursor: default;} </style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div id="poststuff"><div id="postbox-container-2" class="postbox-container">';
		do_meta_boxes( 'term', 'normal', $tag );
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
			'term',
			$this->context,
			$this->priority
		);
	}

	/**
	 * Verifies nonce and user access
	 *
	 * @param int $term_id Term ID.
	 * @param string $nonce_value Nonce value.
	 *
	 * @return WP_Error|bool
	 */
	public function verify( int $term_id, string $nonce_value ) {
		// Check if a nonce is valid.
		if ( false === wp_verify_nonce( $nonce_value, $this->nonce_action ) ) {
			return new WP_Error( 'rfd-error', __( 'Nonce validation failed', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		// Check if the user has permissions to save data.
		if ( false === current_user_can( 'manage_categories', $term_id ) ) {
			return new WP_Error( 'rfd-error', __( 'User is not allowed to edit meta', $this->lang_domain ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
		}

		return true;
	}

	/**
	 * Determines should save be called.
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id Term-taxonomy ID.
	 * @param string $taxonomy Taxonomy.
	 * @param bool $update Update (true) or create (false).
	 *
	 * @return bool
	 */
	public function maybe_save( int $term_id, int $tt_id, string $taxonomy, bool $update ): bool {
		if ( true === empty( $this->nonce_name ) || true === empty( $this->nonce_action ) ) {
			/* translators: name of missing nonce name */
			$error_text = _x( 'Nonce validation not set for %s', 'name of missing nonce name', $this->lang_domain ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
			$error      = new WP_Error( 'rfd-error', sprintf( $error_text, $this->id ) );
			Logger::log( $error, true ); // @phpstan-ignore-line.
		}

		$verified = $this->verify( $term_id, sanitize_text_field( wp_unslash( $_POST[ $this->nonce_name ] ?? '' ) ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( is_wp_error( $verified ) ) {
			Logger::log( $verified, true ); // @phpstan-ignore-line.
		}

		return $this->save( $term_id, $tt_id, $taxonomy, $update );
	}

	/**
	 * Render user meta box.
	 *
	 * @param mixed $term Term object.
	 */
	abstract public function render( $term ): void;

	/**
	 * Save user meta data.
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id Term-taxonomy ID.
	 * @param string $taxonomy Taxonomy.
	 * @param bool $update Update (true) or create (false).
	 *
	 * @return bool
	 */
	abstract public function save( int $term_id, int $tt_id, string $taxonomy, bool $update ): bool;

	/**
	 * Renders nonce field
	 *
	 * @return string
	 */
	public function nonce_field(): string {
		return wp_nonce_field( $this->nonce_action, $this->nonce_name, true, false );
	}
}
