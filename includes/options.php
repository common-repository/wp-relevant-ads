<?php
/**
 * Contains the default values for the options global.
 *
 * @package WP Relevant Ads/Options
 */

$GLOBALS['wp_relevant_ads_options'] = new scbOptions( 'wp_relevant_ads_options', WPRA_PLUGIN_PATH, array(
    'count_clicks'               => 'yes',

    'owner_notify_expired_ads'   => 'yes',

    'admin_notify_expired_ads'   => 'yes',
    'admin_notify_expiring_ads'  => 'yes',

    'expiring_ads_footer_text'   => sprintf( __( 'To extend your Ad duration, please <a href="%s">contact us</a>.', 'wp-relevant-ads' ), site_url( 'contact' ) ),
    'expired_ads_footer_text'    => sprintf( __( 'To place a new Ad on our site, please <a href="%s">contact us</a>.', 'wp-relevant-ads' ), site_url( 'contact' ) ),

    'ads_wrapper'                => '',
    'ads_wrapper_title'          => '',
) );

