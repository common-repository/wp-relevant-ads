<?php
/**
 * WP Relevant Ads.
 *
 * Created using WordPress Plugin Boilerplate.
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate
 *
 * @package           WP Relevant Ads
 *
 * @wordpress-plugin
 * Plugin Name:       WP Relevant Ads
 * Plugin URI:        https://www.wprelevantads.com
 * Description:       Display relevant Ads or any other content based on their terms (categories, tags, etc..)
 * Version:           1.0.0
 * Author:            SebeT
 * Author URI:        https://www.bruno-carreco.com
 * Text Domain:       wp-relevant-ads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Begin Freemius.
if ( ! function_exists( 'wra_fs' ) ) {
/**
 * Helper function for easy SDK access.
 */
function wra_fs() {
	global $wra_fs;

	if ( ! isset( $wra_fs ) ) {
		// Include Freemius SDK.
		require_once dirname( __FILE__ ) . '/includes/freemius/start.php';

		$wra_fs = fs_dynamic_init( array(
			'id'             => '995',
			'slug'           => 'wp-relevant-ads',
			'type'           => 'plugin',
			'public_key'     => 'pk_3ebce8077aa8cf4d4fad8b2e34de6',
			'is_premium'     => false,
			'has_addons'     => true,
			'has_paid_plans' => false,
			'menu'           => array(
			'slug'           => 'edit.php?post_type=wp-relevant-ad',
			'support'        => false,
			),
		) );
	}
	return $wra_fs;
}

// Init Freemius.
wra_fs();

// Signal that SDK was initiated.
do_action( 'wra_fs_loaded' );

define( 'WPRA_PLUGIN_PATH', __FILE__ );

/**
 * Init hooks.
 */
add_action( 'after_setup_theme', 'run_wp_relevant_ads', 9999 );


/**
 * Activation/Deactivation hooks.
 */
register_activation_hook( __FILE__, 'activate_wp_relevant_ads' );
register_deactivation_hook( __FILE__, 'deactivate_wp_relevant_ads' );

/**
 * Runs during plugin activation.
 *
 * @since 1.0.0
 */
function activate_wp_relevant_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-activator.php';
	run_wp_relevant_ads( $firstime = true );
}

/**
 * Runs during plugin deactivation.
 *
 * @since 1.0.0
 */
function deactivate_wp_relevant_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-deactivator.php';
	WP_Relevant_Ads_Deactivator::deactivate();
}

/**
 * Begins execution of the plugin.
 *
 * It must be triggered from 'plugins_loaded' to ensure 'scbFramework' works correctly.
 *
 * @since 1.0.0
 */
function run_wp_relevant_ads( $firstime = false ) {
	global $wp_relevant_ads;

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-activator.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-i18n.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-core.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-relevant-ads-register.php';

	if ( $firstime ) {
		WP_Relevant_Ads_Activator::activate( __FILE__ );
	}

	// Gets an instance of the plugin.
	$wp_relevant_ads = wp_relevant_ads( __FILE__ );
	$wp_relevant_ads->run();

	do_action( 'wp_relevant_ads_init' );
}
}
