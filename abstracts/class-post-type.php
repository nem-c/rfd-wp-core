<?php
/**
 * Post Type generator
 *
 * @link       https://rfd.rs/
 * @since      2.0.0
 *
 * @package    RFD\Core
 * @subpackage RFD\Core\Abstracts
 */

namespace RFD\Core\Abstracts;

use RFD\Core\Loader;

/**
 * Class Post_Type
 *
 * @package RFD\Core\Abstracts
 */
abstract class Post_Type {

	/**
	 * Post type name
	 *
	 * @var string
	 */
	protected $name = 'post_type';

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	protected $slug = 'post-type';

	/**
	 * Post type menu item label
	 *
	 * @var string
	 */
	protected $menu_title = 'Posts';

	/**
	 * Post type admin bar label
	 *
	 * @var string
	 */
	protected $admin_bar_title = 'Posts';

	/**
	 * Post type singular label
	 *
	 * @var string
	 */
	protected $singular_label = 'Post';

	/**
	 * Post type plural label.
	 *
	 * @var string
	 */
	protected $plural_label = 'Posts';

	/**
	 * Post type description.
	 *
	 * @var string
	 */
	protected $description = 'Posts Custom Post Type';

	/**
	 * Post type lang domain (for i18n - usually matches lang of plugin).
	 *
	 * @var string
	 */
	protected $lang_domain = '';

	/**
	 * Post type show on frontend.
	 *
	 * @var bool
	 */
	protected $with_front = true;

	/**
	 * Post type pages.
	 *
	 * @var bool
	 */
	protected $pages = true;

	/**
	 * Show post type in feeds.
	 *
	 * @var bool
	 */
	protected $feeds = true;

	/**
	 * Is post type hierarchical.
	 *
	 * @var bool
	 */
	protected $hierarchical = true;

	/**
	 * Is post type public.
	 *
	 * @var bool
	 */
	protected $public = true;

	/**
	 * Show post type in UI
	 *
	 * @var bool
	 */
	protected $show_ui = true;

	/**
	 * Show post type in menu.
	 *
	 * @var bool
	 */
	protected $show_in_menu = true;

	/**
	 * Remove top menu item.
	 *
	 * @var bool
	 */
	protected $remove_menu_item = false;

	/**
	 * Post type admin menu position.
	 *
	 * @var int
	 */
	protected $menu_position = 50;

	/**
	 * Show post type in admin bar.
	 *
	 * @var bool
	 */
	protected $show_in_admin_bar = true;

	/**
	 * Show post type in nav menus.
	 *
	 * @var bool
	 */
	protected $show_in_nav_menus = true;

	/**
	 * Allow post type export.
	 *
	 * @var bool
	 */
	protected $can_export = true;

	/**
	 * Post type has archive.
	 *
	 * @var bool
	 */
	protected $has_archive = true;

	/**
	 * Exclude post type from search.
	 *
	 * @var bool
	 */
	protected $exclude_from_search = false;

	/**
	 * Post type is available on frontend.
	 *
	 * @var bool
	 */
	protected $publicly_queryable = true;

	/**
	 * Capability.
	 *
	 * @var string
	 */
	protected $capability_type = 'page';

	/**
	 * Post type support arguments.
	 *
	 * @var array
	 */
	protected $supports = array();

	/**
	 * Show post type in REST.
	 *
	 * @var bool
	 */
	protected $show_in_rest = true;

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 */
	final public static function init( Loader $loader ): void {
		$post_type = new static(); // @phpstan-ignore-line

		$loader->add_action( 'init', $post_type, 'register' );
		$loader->add_action( 'admin_menu', $post_type, 'remove_menu_item' );
	}

	/**
	 * Register post type
	 */
	public function register(): void {
		register_post_type( $this->name, $this->get_args() );
	}

	/**
	 * Remove top menu item
	 */
	public function remove_menu_item(): void {
		if ( true === $this->remove_menu_item ) {
			remove_menu_page( 'edit.php?post_type=' . $this->name );
		}
	}

	/**
	 * Prepare rewrite arguments.
	 *
	 * @return array
	 */
	private function rewrite(): array {
		return array(
			'slug'       => $this->slug,
			'with_front' => $this->with_front,
			'pages'      => $this->pages,
			'feeds'      => $this->feeds,
		);
	}

	/**
	 * Prepare labels array.
	 *
	 * @return array
	 */
	private function get_labels(): array {
		return array(
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'name'                  => _x( $this->plural_label, 'Post Type General Name', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'singular_name'         => _x( $this->singular_label, 'Post Type Singular Name', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'menu_name'             => __( $this->menu_title, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'name_admin_bar'        => __( $this->admin_bar_title, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'archives'              => __( 'Archives', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'attributes'            => __( 'Attributes', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'parent_item_colon'     => __( 'Parent Item:', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'all_items'             => __( 'All ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new_item'          => __( 'Add New ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'add_new'               => __( 'Add New', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'new_item'              => __( 'New ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'edit_item'             => __( 'Edit ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'update_item'           => __( 'Update ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'view_item'             => __( 'View ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'view_items'            => __( 'View ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'search_items'          => __( 'Search ' . ucfirst( $this->plural_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'not_found'             => __( 'Not found', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'not_found_in_trash'    => __( 'Not found in Trash', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'featured_image'        => __( 'Featured Image', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'set_featured_image'    => __( 'Set featured image', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'remove_featured_image' => __( 'Remove featured image', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'use_featured_image'    => __( 'Use as featured image', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'insert_into_item'      => __( 'Insert into ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'uploaded_to_this_item' => __( 'Uploaded to this ' . ucfirst( $this->singular_label ), $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'items_list'            => __( ucfirst( $this->plural_label ) . ' list', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'items_list_navigation' => __( ucfirst( $this->singular_label ) . ' list navigation', $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'filter_items_list'     => __( 'Filter ' . ucfirst( $this->plural_label ) . ' List', $this->lang_domain ),
		);
	}

	/**
	 * Prepare arguments for post type registration.
	 *
	 * @return array
	 */
	private function get_args(): array {
		return array(
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'label'               => __( $this->singular_label, $this->lang_domain ),
			//phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText, WordPress.WP.I18n.NonSingularStringLiteralDomain
			'description'         => __( $this->description, $this->lang_domain ),
			'labels'              => $this->get_labels(),
			'supports'            => $this->supports,
			'hierarchical'        => $this->hierarchical,
			'public'              => $this->public,
			'show_ui'             => $this->show_ui,
			'show_in_menu'        => $this->show_in_menu,
			'menu_position'       => $this->menu_position,
			'show_in_admin_bar'   => $this->show_in_admin_bar,
			'show_in_nav_menus'   => $this->show_in_nav_menus,
			'can_export'          => $this->can_export,
			'has_archive'         => $this->has_archive,
			'exclude_from_search' => $this->exclude_from_search,
			'publicly_queryable'  => $this->publicly_queryable,
			'rewrite'             => $this->rewrite(),
			'capability_type'     => $this->capability_type,
			'show_in_rest'        => $this->show_in_rest,
		);
	}
}
