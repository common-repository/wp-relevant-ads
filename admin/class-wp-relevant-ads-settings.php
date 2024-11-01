<?php
/**
 * Admin settings.
 *
 * @package WP Relevant Ads/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Provides admin settings to the plugin.
 */
class WP_Relevant_Ads_Admin_Page extends BC_Framework_Tabs_Page {

	function setup() {

		$this->args = array(
			'page_title'            => __( 'General Settings', 'wp-relevant-ads' ),
			'menu_title'            => __( 'Settings', 'wp-relevant-ads' ),
			'page_slug'             => 'wp-relevant-ads-settings',
			'parent'                => 'edit.php?post_type=' . $this->post_type,
			'screen_icon'           => 'options-general',
			'admin_action_priority' => 11,
		);

		add_action( 'admin_init', array( $this, 'init_tooltips' ), 9999 );
	}

	/**
	 * Load tooltips for the current screen.
	 */
	public function init_tooltips() {
		BC_Framework_ToolTips::instance( array( 'toplevel_page_wp-relevant-ads-settings' ) );
	}

	protected function init_tabs() {

		$this->tabs->add( 'general', __( 'General', 'wp-relevant-ads' ) );
		$this->tabs->add( 'other', __( 'Extra', 'wp-relevant-ads' ) );

		$this->tab_sections['general']['reporting'] = array(
			'title'  => __( 'Clicks', 'wp-relevant-ads' ),
			'fields' => array(
				array(
					'title' => __( 'Count Clicks', 'wp-relevant-ads' ),
					'tip'   => __( 'Enable this option to monitor Ad clicks.', 'wp-relevant-ads' ),
					'type'  => 'checkbox',
					'name'  => 'count_clicks',
				),
			),
		);

		$this->tab_sections['general']['ads'] = array(
			'title' => __( 'Ads', 'wp-relevant-ads' ),
			'fields' => array(
				array(
					'title'    => __( 'Group Markup', 'wp-relevant-ads' ),
					'desc'     => '<br/>' . 'Example: <code>&lt;div class="my-ad-group"&gt;_AD_&lt;/div&gt;</code>' .
								  '<br/><br/>' . __( 'The <code>_AD_</code> placeholder is required.', 'wp-relevant-ads' ),
					'tip'      => __( 'The HTML markup for wrapping each category of Ads.', 'wp-relevant-ads' ),
					'type'     => 'textarea',
					'name'     => 'ads_wrapper',
					'sanitize' => array( $this, 'sanitize_html' ),
					'extra'    => array(
						'rows' => 3,
						'cols' => 100,
					),
				),
				array(
					'title'    => __( 'Group Title Markup', 'wp-relevant-ads' ),
					'desc'     => '<br/>' . 'Example: <code>&lt;h2 class="my-ad-group-title"&gt;_TITLE_&lt;/h2&gt;</code>' .
								  '<br/><br/>' . __( 'The <code>_TITLE_</code> placeholder is required.', 'wp-relevant-ads' ),
					'tip'      => __( 'The HTML markup for the title displayed inside each category of Ads.', 'wp-relevant-ads' ),
					'type'     => 'textarea',
					'name'     => 'ads_wrapper_title',
					'sanitize' => array( $this, 'sanitize_html' ),
					'extra'    => array(
						'rows' => 3,
						'cols' => 100,
					),
				),
			),
		);

		$this->tab_sections['other']['shortcode'] = array(
			'title' => __( 'Contact Form Shortcode', 'wp-relevant-ads' ),
			'fields' => array(
				array(
					'title'  => __( 'Shortcode', 'wp-relevant-ads' ),
					'tip'    => __( 'Use this shortcode to display a simple contact form on your pre-sales page.', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'shortcode' ),
					'name'   => '_blank',
				),
			),
		);

	}


	public function shortcode() {
		$output  = html( 'code', '[wp_relevant_ads_contact_form]' );
		$output .= '<br/>' . html( 'h3', __( 'Parameters', 'wp-relevant-ads' ) );
		$output .= '<br/>' . html( 'p', __( sprintf( '<code>subject</code> The email subject.', get_bloginfo( 'admin_email' ) ), 'wp-relevant-ads' ) );
		$output .= '<br/>' . __( 'i.e:', 'wp-relevant-ads' ) . html( 'code', sprintf( '[wp_relevant_ads_contact_form subject="New Ad Pre-Sale Request on \'%s\'"]', get_bloginfo( 'name' ) ) );
		$output .= '<br/><br/>' . html( 'p', __( sprintf( '<code>to</code> The email address that will receive the pre-sale emails. Default is the site admin email <small>(%s)</small>.', get_bloginfo( 'admin_email' ) ), 'wp-relevant-ads' ) );
		$output .= '<br/>' . __( 'i.e:', 'wp-relevant-ads' ) . html( 'code', sprintf( '[wp_relevant_ads_contact_form to="%s"]', get_bloginfo( 'admin_email' ) ) );
		return $output;
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
	 * Sanitize HTML fields.
	 *
	 * @since 1.0.0
	 */
	public function sanitize_html( $value ) {
		return wp_kses_post( $value );
	}

}
