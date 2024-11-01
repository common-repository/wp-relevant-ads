<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package WP Relevant Ads/Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string $plugin The ID of this plugin.
	 */
	private $plugin;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin The name of the plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin, $version ) {
		$this->plugin = $plugin;
		$this->version = $version;

		$this->add_actions();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$ext = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css';

		wp_enqueue_style( $this->plugin, plugin_dir_url( __FILE__ ) . "css/wp-relevant-ads{$ext}", array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		global $wp_relevant_ads_options;

		$ext = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js';

		wp_enqueue_script( $this->plugin, plugin_dir_url( __FILE__ ) . "js/wp-relevant-ads{$ext}" , array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin . '-ajax', plugin_dir_url( __FILE__ ) . "js/wp-relevant-ads-ajax{$ext}" , array( 'jquery' ), $this->version, true );

		$terms_meta = get_option( 'wp_relevant_ads_terms_meta' );

		/* Script variables */
		$params = array(
			'ajax_url'			=> admin_url( 'admin-ajax.php' ),
			'nonce'				=> wp_create_nonce( $this->nonce ),
			'dom_ads'			=> wp_relevant_ads()->get_dom_ads(),
			'ads_wrapper'		=> $wp_relevant_ads_options->ads_wrapper,
			'ads_wrapper_title' => $wp_relevant_ads_options->ads_wrapper_title,
			'ads_terms'			=> get_terms( $this->taxonomy ),
			'terms_meta'		=> $terms_meta,
		);

		wp_localize_script( $this->plugin, 'wp_relevant_ads_params', $params );
		wp_localize_script( $this->plugin.'-ajax', 'wp_relevant_ads_params', $params );
	}

	/**
	 * Interceptor for retrieving properties from the main plugin object.
	 *
	 * @since	1.0.0
	 *
	 * @param	string $name The property to retrieve.
	 * @return	mixed The property value.
	 */
	public function __get( $name ) {
		return wp_relevant_ads()->$name;
	}

	/**
	 * Add actions.
	 */
	protected function add_actions() {
		add_action( 'the_content', array( $this, 'wp_relevant_ads_before_the_content_single' ), 10, 2 );
		add_action( 'the_content', array( $this, 'wp_relevant_ads_after_the_content_single' ), 10, 2 );
		add_action( 'the_content', array( $this, 'wp_relevant_ads_before_the_content' ), 10, 2 );
		add_action( 'the_content', array( $this, 'wp_relevant_ads_after_the_content' ), 10, 2 );
	}

	/**
	 * Hook '*_the_content_single' related custom filters into 'the_content' to correctly position the output Ads.
	 */
	public function wp_relevant_ads_before_the_content_single( $content, $is_wp_relevant_ads_content = false ) {
		global $post, $wp_relevant_ads;

		if ( $post->post_type === $wp_relevant_ads->post_type || $is_wp_relevant_ads_content || ( ! is_single() && ! is_singular() ) ) {
			return $content;
		}
		return $this->wp_relevant_ads_before_the_content( $content, $is_wp_relevant_ads_content, 'wp_relevant_ads_before_the_content_single' );
	}

	/**
	 * Hook '*_the_content_single' related custom filters into 'the_content' to correctly position the output Ads.
	 */
	public function wp_relevant_ads_after_the_content_single( $content, $is_wp_relevant_ads_content = false ) {
		global $post, $wp_relevant_ads;

		if ( $post->post_type === $wp_relevant_ads->post_type || $is_wp_relevant_ads_content || ( ! is_single() && ! is_singular() ) ) {
			return $content;
		}
		return $this->wp_relevant_ads_after_the_content( $content, $is_wp_relevant_ads_content, 'wp_relevant_ads_after_the_content_single' );
	}

	/**
	 * Hook '*_the_content' related custom filters into 'the_content' to correctly position the output Ads.
	 */
	public function wp_relevant_ads_before_the_content( $content, $is_wp_relevant_ads_content = false, $hook = 'wp_relevant_ads_before_the_content' ) {
		global $post, $wp_relevant_ads;

		if ( $post->post_type === $wp_relevant_ads->post_type || $is_wp_relevant_ads_content ) {
			return $content;
		}

		ob_start();
		do_action( $hook );
		$content = ob_get_clean() . $content;
		return $content;
	}

	/**
	 * Hook '*_the_content' related custom filters into 'the_content' to correctly position the output Ads.
	 */
	public function wp_relevant_ads_after_the_content( $content, $is_wp_relevant_ads_content = false, $hook = 'wp_relevant_ads_after_the_content' ) {
		global $post, $wp_relevant_ads;

		if ( $post->post_type === $wp_relevant_ads->post_type || $is_wp_relevant_ads_content ) {
			return $content;
		}

		ob_start();
		do_action( $hook );
		$content .= ob_get_clean();
		return $content;
	}

}
