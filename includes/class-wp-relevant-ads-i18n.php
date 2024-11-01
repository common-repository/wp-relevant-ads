<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package WP Relevant Ads/Includes/i18n
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * Give priority to the WP languages folder.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = self::get_domain();

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// load from WP '\languages' folder.
		load_textdomain( $domain, WP_LANG_DIR . "/{$domain}/{$domain}-" . $locale . '.mo' );

		// load from the plugin '\languages' folder.
		load_plugin_textdomain( $domain, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Get the plugin domain.
	 *
	 * @since 1.0.0
	 */
	public static function get_domain() {
		return 'wp-relevant-ads';
	}

}
