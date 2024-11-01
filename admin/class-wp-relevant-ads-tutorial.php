<?php
/**
 * Provides the guided tour functionality.
 *
 * @package WP Relevant Ads/Admin/Tutorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Provides the info to use on the guided tour and help pages.
 */
class WP_Relevant_Ads_Guided_Tutorial_Listing extends BC_Framework_Pointers_Tour {

	var $plugin_name;

	public function __construct() {

		$this->plugin_name = 'WP Relevant Ads';

		// Tip: hook into 'current_screen' and call 'get_current_screen()' to get the screen ID.
		parent::__construct( 'edit-wp-relevant-ad', array(
			'version'     => '1.0',
			'prefix'      => 'wp-relevant-ads-tour',
			'text_domain' => 'wp-relevant-ads',
			'help'        => true,
		) );
	}

	/**
	 * Retrieves the 'apply' button for the custom settings.
	 */
	public function screen_settings_apply( $settings, $args ) {
		return $settings;
	}

	/**
	 * Adds the help page.
	 */
	public function init_help_page() {
		add_action( 'load-edit.php', array( $this, 'help_page' ) );
	}

	/**
	 * Setup the help page.
	 */
	public function help_page() {
		global $wp_relevant_ads;

		if ( empty( $_GET['post_type'] ) || $wp_relevant_ads->post_type !== $_GET['post_type'] ) {
			return;
		}
		parent::help_page();
	}

	/**
	 * The guided tour steps.
	 */
	protected function pointers() {
		$pointers['step1'] = array(
			'title'     => html( 'h3', sprintf( __( 'Welcome to <em>%s</em>!','wp-relevant-ads'), $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This is the page where you manage all relevant Ads or other relevant content you create.','wp-relevant-ads') ) .
						   html( 'p', __( 'On activation, the plugin auto-generates some Ads to help you get started creating your own.','wp-relevant-ads') ),
			'anchor_id' => '.wp-heading-inline',
			'edge'      => 'top',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step2'] = array(
			'title'     => html( 'h3', __( 'Name', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This column shows the Ad names.','wp-relevant-ads') ) .
						   html( 'p', __( 'You can give Ads any names you like. Names are not displayed anywhere else. They are only used for your reference.', 'wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #title',
			'edge'      => 'left',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step3'] = array(
			'title'     => html( 'h3', __( 'Categories', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Just like normal Posts, you can categorize each Ad as you like. ','wp-relevant-ads') ) .
						   html( 'p', __( 'For example, you could categorize your Ads by type: Shortcodes, Widgets; by client: Nike, Adidas, Apple, etc... Use it as you like.','wp-relevant-ads' ) ),
			'anchor_id' => '.wp-list-table #category',
			'edge'      => 'left',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step4'] = array(
			'title'     => html( 'h3', __( 'Taxonomies', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This column displays the parent category names that make each Ad relevant (<em>Categories</em>, <em>Tags</em>, etc ...).','wp-relevant-ads') ) .
						   html( 'p', __( 'To see exactly which terms make each Ad relevant you\'ll need to open the editor.','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #taxonomies',
			'edge'      => 'left',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step5'] = array(
			'title'     => html( 'h3', __( 'Displayed By', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This column shows the method you choose for displaying each Ad: Shortcode, Widget (others coming soon).','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #trigger',
			'edge'      => 'left',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step6'] = array(
			'title'     => html( 'h3', __( 'Call to Action', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'On this column you can see which Ads are meant to sell Ad space (<em>Call to Action</em> Ads).','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #call_action',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step7'] = array(
			'title'     => html( 'h3', __( 'Clicks', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This column shows the total number of clicks for each Ad.','wp-relevant-ads') ) .
						   html( 'p', __( 'You can sort Ads by their click count to find which Ads are generating more clicks.','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #clicks',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step8'] = array(
			'title'     => html( 'h3', __( 'Start Date', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This column shows each Ad start date.','wp-relevant-ads') ) .
						   html( 'p', __( 'If an Ad is scheduled to start at a later date you\'ll also see that information here.','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #date',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step9'] = array(
			'title'     => html( 'h3', __( 'Expire Date', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'The last column shows the expire date for each Ad.','wp-relevant-ads') ) .
						   html( 'p', __( 'No expire date means the Ad will run indefinitely.','wp-relevant-ads') ),
			'anchor_id' => '.wp-list-table #expire_date',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['help'] = array(
			'title'     => html( 'h3', sprintf( __( 'Thanks for using <em>%s</em>!','wp-relevant-ads'), $this->plugin_name ) ),
			'content'   => html( 'p', __( 'If you need to revisit this guided tour later or need specific help on an option use the - <em>Screen Options</em> - or - <em>Help</em> - tabs.','wp-relevant-ads') ),
			'anchor_id' => '#screen-options-link-wrap',
			'edge'      => 'top',
			'align'     => 'right',
			'where'     => array( $this->screen_id ),
		);
		return $pointers;
	}

    /**
     * Custom CSS styles to be added on the page header.
     */
	public function css_styles() {
?>
	<style type="text/css">
		.contextual-help-tabs-wrap .hide-in-help-tabs {
			display: none;
		}
		.wp-relevant-ads-tour1_0_help .wp-pointer-arrow {
			left: 250px;
		}
	</style>
<?php
	}

	/**
	 * Helper for outputting premium plan only notes.
	 */
	protected function premium_only( $part = 'refer', $plan = '' ) {


	}

}

/**
 * Provides the info to use on the guided tour and help pages.
 */
class WP_Relevant_Ads_Guided_Tutorial_Single extends BC_Framework_Pointers_Tour {

	var $plugin_name;

	public function __construct() {

		$this->plugin_name = 'WP Relevant Ads';

		// Tip: hook into 'current_screen' and call 'get_current_screen()' to get the screen ID.
		parent::__construct( 'wp-relevant-ad', array(
			'version'     => '1.0',
			'prefix'      => 'wp-relevant-ads-single-tour',
			'text_domain' => 'wp-relevant-ads',
			'help'        => true,
		) );
	}

	/**
	 * Adds the help page.
	 */
	public function init_help_page() {
		add_action( 'load-post.php', array( $this, 'help_page' ) );
		add_action( 'load-post-new.php', array( $this, 'help_page' ) );
	}

	/**
	 * Setup the help page.
	 */
	public function help_page() {
		global $wp_relevant_ads;

		if ( ! empty( $_GET['post'] ) ) {
			if ( get_post_type( (int) $_GET['post'] ) !== $wp_relevant_ads->post_type ) {
				return;
			}
		} else {

			if ( empty( $_GET['post_type'] ) || $wp_relevant_ads->post_type !== $_GET['post_type'] ) {
				return;
			}

		}
		parent::help_page();
	}

	/**
	 * The guided tour steps.
	 */
	protected function pointers() {
		$pointers['step1'] = array(
			'title'     => html( 'h3', sprintf( __( '<em>%s</em> - Ad editor','wp-relevant-ads'), $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This is the main screen for creating your relevant Ads.','wp-relevant-ads' ) ) .
						   html( 'p', __( 'You should be familiar with it since its very similar to the regular Posts edit screen.','wp-relevant-ads') ) .
						   html( 'p', __( 'Follow the tutorial to get started. Besides this guided tutorial you can also get more info about each option by hovering over their help icon ( <span class="dashicons dashicons-editor-help"></span> ) and on the \'How To\' page.','wp-relevant-ads') ),
			'anchor_id' => '.wp-heading-inline',
			'edge'      => 'top',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step2'] = array(
			'title'     => html( 'h3', __( 'Ad Content', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This is your Ad canvas. Your Ads can be anything: images, video, text, etc... ','wp-relevant-ads' ) ) .
						   html( 'p', __( 'Note that you are not limited to use if for Ads, you can add any content you like and make it relevant!' ,'wp-relevant-ads' ) ) .
						   html( 'p', __( 'As an example, you could use it to have a relevant image that changes every time you updated it here, instead of editing each Post individually when you need to change it.' ,'wp-relevant-ads' ) ),
			'anchor_id' => '#wp-content-editor-tools',
			'edge'      => 'top',
			'align'     => 'right',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step3'] = array(
			'title'     => html( 'h3', __( 'Criteria - Terms', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'This is the main criteria field to make Ads relevant.', 'wp-relevant-ads' ) ) .
						   html( 'p', __( 'Choose the terms that should trigger the Ad and it will be displayed on any posts that contain those terms, using the method you chose below: Shortcode, Widget, etc... (*)','wp-relevant-ads') ) .
						   html( 'p', __( 'If you don\'t choose any terms, the Ad will always be displayed regardless of its terms.' ,'wp-relevant-ads') ) .
						   html( 'p', __( 'Leaving terms empty is a good choice if you want to display Ads on your front page or any other pages that usually don\'t have any taxonomies (categories, tags, etc..).' ,'wp-relevant-ads') ) .
						   // @todo: hide if add-on is installed
						   html( 'p', html( 'em', __( '(*) The <em>Free</em> version only provides support for displaying Ads in <em>Post</em> related <em>Categories</em> and <em>Tags</em>.','wp-relevant-ads' ) ) ) .
						   html( 'p', html( 'em', __( 'Additional add-ons allow you to choose any terms from any custom types you have, like <em>WooCommerce</em> products, classified listings, etc..','wp-relevant-ads' ) ) ),
			'anchor_id' => '.wp_relevant_ads_terms',
			'edge'      => 'bottom',
			'align'     => 'center',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step4'] = array(
			'title'     => html( 'h3', __( 'Criteria - Keywords', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Similar to how <em>Terms</em> work, you can choose to make an Ad relevant only if it contains a pre-set list of keywords.','wp-relevant-ads') ) .
						   html( 'p', __( 'Use the logic dropdown above to choose if the Ad should match both <em>Terms</em> and <em>Keywords</em> or at least one of them.','wp-relevant-ads') ),
			'anchor_id' => '#_wp_relevant_ads_keywords',
			'edge'      => 'bottom',
			'align'     => 'center',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step5'] = array(
			'title'     => html( 'h3', __( 'Display - Method', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'The Ad can be displayed to your visitors in different ways: <em>Shortcode, Widget</em> or others(*).', 'wp-relevant-ads' ) ) .
						   html( 'p', __( '<strong>Shortcode</strong>
						   <br/>The simplest way to display Ads. Just place the shortcode where you want it to be displayed.','wp-relevant-ads' ) ) .
						   html( 'p', sprintf( __( 'Paste the shortcode %1$s or click the special shortcode (%2$s) icon available in the tools menu where you want to display the Ad.', 'wp-relevant-ads' ), '<code>&#91;wp_relevant_ads&#93;</code>', '<i class="icon-billboard" style="font-size: 14px;"></i>' ) ) .

						   html( 'p', sprintf( __( '<strong>Widget</strong>
						   <br/>Ads displayed through Widgets need to be assigned on the Widgets page under <em>Appearance > Widgets</em>. There you\'ll find a dedicated Widget called %s.', 'wp-relevant-ads' ), '<code>Relevant Ads</code>' ) ) .
						   html( 'p', __( 'Just add that Widget to one of your sidebars and from the categories dropdown choose the same category you assign to the Ad.', 'wp-relevant-ads' ) ) .
						   html( 'p', html( 'em', __( '(*) Additional add-ons allow you to display Ads in more ways (coming soon...)','wp-relevant-ads' ) ) ),
			'anchor_id' => '.wp_relevant_ads_trigger_type',
			'edge'      => 'left',
			'align'     => 'right',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step6'] = array(
			'title'     => html( 'h3', __( 'Duration', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'If this is a limited time Ad, you can specify an expiry date.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'Leave it empty to keep the Ad running.', 'wp-relevant-ads' ) ) .
						   html( 'p', __( 'Notifications for expired or expiring soon Ads will be available through add-ons at a later time.','wp-relevant-ads' ) ),
			'anchor_id' => '.clear_expire_date',
			'edge'      => 'left',
			'align'     => 'right',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step7'] = array(
			'title'     => html( 'h3', __( 'Ad Owner', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Like regular Posts, Ads can be assigned to specific users.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'As a suggestion, you can create user profiles for each of your Ad sponsors and assign them as Ad owners on their respective Ads.', 'wp-relevant-ads' ) ),
			'anchor_id' => '#wp-relevant-ads-author .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step8'] = array(
			'title'     => html( 'h3', __( 'Styling', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'The Styling metabox allows you to further tweak the Ad to your liking.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'Set CSS classes, stylings and a pre-set position for the Ad.', 'wp-relevant-ads' ) ) .
						   html( 'p', __( 'If you are displaying several different Ads for the same terms you can choose to display them: <em>Stacked</em> (vertically), <em>Inline</em> (horizontally) or based on your own <em>CSS</em> stylings.', 'wp-relevant-ads' ) ),
			'anchor_id' => '#wp-relevant-ads-css .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step9'] = array(
			'title'     => html( 'h3', __( 'Clicks', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Ad clicks will show here.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'You can reset clicks at any time by leaving this field empty and saving the Ad.', 'wp-relevant-ads' ) ),
			'anchor_id' => '#wp-relevant-ads-clicks .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step10'] = array(
			'title'     => html( 'h3', __( 'Post Attributes', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Use this field to set the order of the Ad.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'Ordering is useful if you are displaying several Ads for the same terms.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'Ads will be sorted in ascending order.', 'wp-relevant-ads') ),
			'anchor_id' => '#pageparentdiv .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step11'] = array(
			'title'     => html( 'h3', __( 'Categories', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'As with regular Posts, <em>Categories</em> give context to your Ads.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'Assign meaningful categories to identify and keep them organized.', 'wp-relevant-ads' ) ) .
						   html( 'p', __( 'As an example, you can categorize your Ads by their type: Banners, Widgets, etc..., by Client: <em>Amazon, Apple, Google</em>, etc...','wp-relevant-ads' ) ),
			'anchor_id' => '#wp-relevant-ad-catdiv .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step12'] = array(
			'title'     => html( 'h3', __( 'Call to Action', $this->plugin_name ) ),
			'content'   => //html( 'p', __( 'Check this option to have this Ad act as \'Call to Action\' Ad, used to sell Ad space on your site.', 'wp-relevant-ads') ) .
						   html( 'p', __( '\'Call to Action\' Ads are a great way to drive Ad sales to all your different content categories since they will be displayed while you don\'t have regular Ads for the selected terms.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'You can choose an Ad sales page from the dropdown or create one and use the special shortcode provided by the plugin.','wp-relevant-ads' ) ) .
						   html( 'p', __( 'The shortcode displays a simple contact form that advertisers can use to submit any Ad sale queries to you. Find more info about it on the plugin settings page.','wp-relevant-ads' ) ),
			'anchor_id' => '#wp-relevant-ads-call-action .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['step13'] = array(
			'title'     => html( 'h3', __( 'Publish', $this->plugin_name ) ),
			'content'   => html( 'p', __( 'Finally, when you\'re ready to show the Ad to your audience you can publish the Ad here.', 'wp-relevant-ads') ) .
						   html( 'p', __( 'As with regular Posts, you can also schedule Ads to start at specific dates.', 'wp-relevant-ads' ) ),
			'anchor_id' => '#submitdiv .inside',
			'edge'      => 'right',
			'align'     => 'left',
			'where'     => array( $this->screen_id ),
		);
		$pointers['help'] = array(
			'title'     => html( 'h3', sprintf( __( 'Thanks for using <em>%s</em>!','wp-relevant-ads'), $this->plugin_name ) ),
			'content'   => html( 'p', __( 'If you need to revisit this guided tour later or need specific help on an option use the - <em>Screen Options</em> - or - <em>Help</em> - tabs.','wp-relevant-ads') ),
			'anchor_id' => '#screen-options-link-wrap',
			'edge'      => 'top',
			'align'     => 'right',
			'where'     => array( $this->screen_id ),
		);
		return $pointers;
	}

    /**
     * Custom CSS styles to be added on the page header.
     */
	public function css_styles() {
?>
	<style type="text/css">
		.contextual-help-tabs-wrap .hide-in-help-tabs {
			display: none;
		}
		.wp-relevant-ads-single-tour1_0_step2.wp-pointer-top {
			padding-left: 13px;
		}
		.wp-relevant-ads-single-tour1_0_step2.wp-pointer-top .wp-pointer-arrow {
			left: 0;
			border-width: 13px 13px 14px 0;
			border-right-color: #ccc;
			top: 50%;
			margin-top: -15px;
			border-bottom-color: #fff;
		}
		.wp-relevant-ads-single-tour1_0_step2.wp-pointer-top .wp-pointer-arrow-inner {
			left: 1px;
			margin-left: -13px;
			margin-top: -13px;
			border: 13px solid transparent;
			border-right-color: #fff;
			display: block;
			content: " ";
		}
		.wp-relevant-ads-single-tour1_0_help .wp-pointer-arrow {
			left: 250px;
		}
	</style>
<?php
	}

	/**
	 * Helper for outputting premium plan only notes.
	 */
	protected function premium_only( $part = 'refer', $plan = '' ) {


	}

}
