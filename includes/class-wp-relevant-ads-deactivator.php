<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @package WP Relevant Ads/Includes/Deactivator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Relevant_Ads_Deactivator {

	/**
	 * Code run exclusively on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'wp_relevant_ads_check_expired' );
	}

}
