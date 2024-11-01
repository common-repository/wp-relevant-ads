<?php
/**
 * The core plugin class.
 *
 * @package WP Relevant Ads/Includes/Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Core {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var WP_Relevant_Ads_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var	string $wp_relevant_ads The string used to uniquely identify this plugin.
	 */
	protected $plugin;

	/**
	 * The current version of the plugin core.
	 *
	 * @since 1.0.0
	 *
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin base file name.
	 *
	 * @since 1.0.0
	 *
	 * @var string $filename The plugin base file name.
	 */
	protected $file;

	/**
	 * Additional properties.
	 *
	 * @since 1.0.0
	 *
	 * @var array $properties The list of properties.
	 */
	protected $properties;

	/**
	 * Setup the core properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file ) {
		$this->setup( $file );
	}

	/**
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and define the locale.
	 *
	 * @since 1.0.0
	 */
	public function setup( $file ) {
		$this->plugin  = 'wp-relevant-ads';
		$this->version = '1.0.0';
		$this->file    = $file;

		$this->properties = array(
			'domain'         => WP_Relevant_Ads_i18n::get_domain(),
			'nonce'          => $this->plugin,
			'post_type'      => 'wp-relevant-ad',
			'taxonomy'       => 'wp-relevant-ad-cat',
			'field_prefix'   => '_wp_relevant_ads_',
			'slug'           => 'wp-relevant-ad',
			'status_expired' => 'expired',
			'addons_url'     => '#',
		);

		$this->init_scb_framework();

		$this->load_dependencies();
		$this->set_locale();
	}

	/**
	 * Initializes all the core functionality of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init( $file ) {
		$this->setup( $file );

		$this->define_core_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_custom_widgets();

		$this->setup_cron();

		// Init ads statuses monitoring.
		new WP_Relevant_Ads_Status_Monitor;

		// Init contact shortcode.
		new WP_Relevant_Ads_Contact;

		// Register the base post type.
		WP_Relevant_Ads_Register_Content::register( 'post', 'post_type', 'post', false );
		WP_Relevant_Ads_Register_Content::register( 'page', 'post_type', 'page', false );

		// Register the base hooks.
		$hooks = array(
			'wp_relevant_ads_before_the_content_single' => __( 'Display Ad before the content - Single Pages Only', 'wp-relevant-ads' ),
			'wp_relevant_ads_after_the_content_single'  => __( 'Display the Ad after the content - Single Pages Only', 'wp-relevant-ads' ),
			'wp_relevant_ads_before_the_content'        => __( 'Display Ad before the content - All Pages', 'wp-relevant-ads' ),
			'wp_relevant_ads_after_the_content'         => __( 'Display the Ad after the content - All Pages', 'wp-relevant-ads' ),
		);
		WP_Relevant_Ads_Register_Content::register_base_hooks( $hooks );
	}

	/**
	 * Init scb framework.
	 */
	private function init_scb_framework() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/bc-framework/load.php';
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {

		/**
		 * Load the options file.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/options.php';

		/**
		 * The class responsible for orchestrating the actions and filters.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads-loader.php';

		/**
		 * Other required classes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads-widgets.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads-status.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads-utils.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads-contact.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-relevant-ads.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-relevant-ads-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-relevant-ads-public.php';

		$this->loader = new WP_Relevant_Ads_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Relevant_Ads_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since 1.0.0
	 */
	private function set_locale() {
		$plugin_i18n = new WP_Relevant_Ads_i18n();
		$plugin_i18n->load_plugin_textdomain();
	}

	private function define_core_hooks() {
		$this->loader->add_action( 'init', $this, 'register_post_types', 10 );
		$this->loader->add_action( 'init', $this, 'register_taxonomies', 11 );
	}

	/**
	 * Register all of the hooks related to the dashboard functionality of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function define_admin_hooks() {

		if ( ! is_admin() ) {
			return;
		}

		$plugin_admin = new WP_Relevant_Ads_Admin( $this->get_plugin(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'shortcode_button' );
		$this->loader->add_action( 'manage_' . $this->post_type . '_posts_custom_column', $plugin_admin, 'manage_columns', 10, 2 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'menu_icon_css' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'remove_metaboxes' );

		$this->loader->add_filter( 'manage_edit-' . $this->post_type . '_columns', $plugin_admin, 'add_new_columns' );
		$this->loader->add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', $plugin_admin, 'column_register_sortable' );
		$this->loader->add_filter( 'wp_relevant_ads_content_directories', $plugin_admin, 'trigger_directories_exclude' );
		$this->loader->add_action( "{$this->taxonomy}_add_form_fields", $plugin_admin, 'term_meta' );
		$this->loader->add_action( "{$this->taxonomy}_edit_form_fields", $plugin_admin, 'term_meta' );
		$this->loader->add_action( 'created_term', $plugin_admin, 'handle_term_meta', 10, 3 );
		$this->loader->add_action( 'edited_term', $plugin_admin, 'handle_term_meta', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since 1.0.0
	 */
	private function define_public_hooks() {

		$plugin_core = new WP_Relevant_Ads_Public( $this->get_plugin(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_core, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_core, 'enqueue_scripts' );
	}

	/**
	 * Register additional widgets.
	 *
	 * @since 1.0.0
	 */
	private function define_custom_widgets() {
		WP_Relevant_Ads_Single_Ad::init();
	}

	/**
	 * Setup cron jobs.
	 *
	 * @since 1.0.0
	 */
	private function setup_cron() {
		if ( ! wp_next_scheduled( 'wp_relevant_ads_check_expired' ) ) {
			wp_schedule_event( time(), 'hourly', 'wp_relevant_ads_check_expired' );
		}
	}

	/**
	 * Register post types.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types() {

		$labels = array(
			'name'               => __( 'Relevant Ads', 'wp-relevant-ads' ),
			'singular_name'      => __( 'Ad', 'wp-relevant-ads' ),
			'add_new'            => __( 'Add New Ad', 'wp-relevant-ads' ),
			'add_new_item'       => __( 'Add New Ad', 'wp-relevant-ads' ),
			'edit_item'          => __( 'Edit Ad', 'wp-relevant-ads' ),
			'new_item'           => __( 'New Ad', 'wp-relevant-ads' ),
			'view_item'          => __( 'View Ad', 'wp-relevant-ads' ),
			'search_items'       => __( 'Search Ads', 'wp-relevant-ads' ),
			'not_found'          => __( 'No Ads found', 'wp-relevant-ads' ),
			'not_found_in_trash' => __( 'No Ads found in Trash', 'wp-relevant-ads' ),
			'parent_item_colon'	 => __( 'Parent Ad:', 'wp-relevant-ads' ),
			'menu_name'          => 'Relevant Ads',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_icon'          => '',
			'supports'           => array( 'title', 'editor', 'author', 'page-attributes' ),
		);

		register_post_type( $this->post_type, apply_filters( 'wp_relevants_ads_ptype_args', $args ) );

		$args = array(
			'public'                    => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'wp-relevant-ads' ),
		);
		register_post_status( $this->status_expired, $args );
	}

	/**
	 * Register taxonomies.
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomies() {

		$labels = array(
			'name'          => _x( 'Ad Categories', 'taxonomy singular name' ),
			'singular_name' => _x( 'Ad Category', 'taxonomy singular name' ),
			'search_items'  => __( 'Search Ad Categories', 'wp-relevant-ads' ),
			'all_items'     => __( 'All Ad Categories', 'wp-relevant-ads' ),
			'edit_item'     => __( 'Edit Ad Category', 'wp-relevant-ads' ),
			'update_item'   => __( 'Update Ad Category', 'wp-relevant-ads' ),
			'add_new_item'  => __( 'Add New Ad Category', 'wp-relevant-ads' ),
			'new_item_name' => __( 'New Ad Category Name', 'wp-relevant-ads' ),
		);

		$args = array(
			'hierarchical' => true,
			'labels'       => $labels,
			'query_var'    => true,
			'show_ui'      => true,
		);

		register_taxonomy( $this->taxonomy, $this->post_type, $args );
		register_taxonomy_for_object_type( $this->taxonomy, $this->post_type );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}


	// Setters & Getters
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin() {
		return $this->plugin;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Relevant_Ads_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the plugin file name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The plugin file name.
	 */
	public function get_file() {
		return $this->file;
	}

	/**
	 * Wrapper for 'get_post_meta()' that automatically prefixs the plugin meta keys.
	 */
	public function get_meta( $id, $name, $single = true ) {
		return get_post_meta( $id, $this->field_prefix . $name, $single );
	}

	/**
	 * Magic method for retrieving any property stored on the properties array.
	 *
	 * @since	1.0.0
	 *
	 * @param	string $name The property to retrieve.
	 * @return	mixed The property value.
	 */
	public function __get( $name ) {
		if ( array_key_exists( $name, $this->properties ) ) {
			return $this->properties[ $name ];
		}
	}

	/**
	 * Magic method for executing specific methods.
	 */
	public function __call( $method, $args ) {
		if ( ! in_array( $method, array( 'get_meta' ) ) ) {
			return;
		}
		return call_user_func_array( array( $this, $method ), $args );
	}
}
