<?php
/**
 * The class that outputs the how to page.
 *
 * @package WP Relevant Ads/Admin/Instructions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The How-To related class.
 */
class WP_Relevant_Ads_HowTo_Page extends BC_Framework_Tabs_Page {

	function setup() {

		$this->save_button = false;

		$this->args = array(
			'page_title'	=> __( 'How To', 'wp-relevant-ads' ),
			'menu_title'	=> __( 'How To', 'wp-relevant-ads' ),
			'page_slug'		=> 'wp-relevant-ads-howto',
			'parent'		=> 'edit.php?post_type=' . $this->post_type,
			'screen_icon'	=> 'options-general',
			'admin_action_priority' => 11,
		);

	}

	public function before_form( $active_tab ) {

		switch ( $active_tab ) {
			case 'quick_start':
				echo html( 'h2', __( 'The Basics' , 'wp-relevant-ads' ) );

				echo html( 'p', sprintf( __( '<em>%1$s</em>, gives you the power to display any type of Ads: <em>Video, Images, Text, etc</em>, contextually, anywhere on your site. '
								. 'That\'s because it displays Ads based on your content taxonomy terms. '
								. 'If you\'re not familiar with taxonomies, please take a look at the <a href="%2$s" target="_blank">Taxonomies</a> related WordPress codex page. '
								. 'For example, a Classifieds site could use it to display Ads related with the category the users are browsing. A blog site could display Ads based on the tags assigned to posts. '
								. 'These are only some examples of what you can achieve with <em>%s</em> as long as a post or custom post type (*) contains taxonomy terms.'
								, 'wp-relevant-ads' ), 'WP Relevant Ads', 'https://codex.wordpress.org/Taxonomies', 'WP Relevant Ads' ) );

				// @todo: don't show when related add-on is active
				echo html( 'p', html( 'em', html( 'small', __( '(*) Support for custom post types is available through add-ons only', 'wp-relevant-ads' ) ) ) );

				echo html( 'p', __( 'To display relevant Ads you just need to specify the taxonomy terms they should relate to and how they should be displayed. '
								. 'Additional options like auto expiring Ads are also available, but these are optional (other options coming soon).', 'wp-relevant-ads' ) );

				echo html( 'p', sprintf( __( 'Note that, although <em>%s</em> is primarily targeted to display Ads you can use it to output '
				. 'virtually any type of content using his flexibility to display content anywhere.', 'wp-relevant-ads' ), 'WP Relevant Ads' ) );

				echo html( 'h2', __( 'Choose the Terms' , 'wp-relevant-ads' ) );

				echo html( 'p', sprintf( __( "By default, <em>WordPress</em> comes with the taxonomies, <a href='%s'>Category</a> and <a href='%s'>Tag</a>. Both can be applied to posts or other post types. "
								. "Some themes and plugins provide additional taxonomies, that are usually applied to new custom post types.", 'wp-relevant-ads' ),
								'edit-tags.php?taxonomy=category', 'edit-tags.php?taxonomy=post_tag' ) );

				echo html( 'p', sprintf( __( "<em>%s</em> can read all (*) available taxonomies allowing you to display Ads for any of the available taxonomies terms. "
						. "Just select the term(s) that relate to the Ad you're creating, from the dropdown list.", 'wp-relevant-ads' ), 'WP Relevant Ads' ) ) .
						html( 'p', html( 'small', '(*) Support for custom post types taxonomies is available through add-ons</em>' ) );

				echo html( 'p', __( 'Leave the terms list empty for Ads that should be displayed anywhere. Use it to display Ads on the front page, for example.', 'wp-relevant-ads' ) );

				echo html( 'img', array( 'src' => esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'images/howto-taxonomies.png' ) ) );

				echo html( 'h2', __( 'Choose the Display Method' , 'wp-relevant-ads' ) );

				echo html( 'p', __( "After you choose the terms that relate to the Ad you want to display, you need to specify how and where the Ad is displayed. "
								. "You might want to display your Ads on a single post/page/product view, on a particular listing, on a sidebar, when a action hook is fired, etc.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "Below are the available options that can be selected to display relevant Ads. You can click on each one to get more details on how to use them.", 'wp-relevant-ads' ) );

				echo sprintf( __( '- <a href="%s">Shortcode</a>', 'wp-relevant-ads' ), 'edit.php?post_type='.$this->post_type.'&page=wp-relevant-ads-howto&tab=shortcodes' );
				echo '<br/>' . sprintf( __( '- <a href="%s">Widget</a>', 'wp-relevant-ads' ), 'edit.php?post_type='.$this->post_type.'&page=wp-relevant-ads-howto&tab=widgets' );

				echo html( 'h2', __( 'Choose a Category' , 'wp-relevant-ads' ) );

				echo html( 'p', __( "Categories are used to group Ads by type and keep them organized. They are specially useful if you need to group Ads in blocks for the same type.", 'wp-relevant-ads' ) );

				echo html( 'img', array( 'src' => plugin_dir_url( dirname( __FILE__ ) ) . '/images/howto-categories.png' ) );
/*
				// @todo: display only if related add-on is installed
				echo html( 'p', __( "For example, if you need to group Ads by hook, or other trigger, you can create a specific category like, <em/>Hook Ad</em> and wrap all Ads in a block. "
							. "Simply edit the A category and check 'Wrap Ads':" , 'wp-relevant-ads' ) );

				echo html( 'img', array( 'src' => plugin_dir_url( dirname( __FILE__ ) ) . '/images/howto-edit-category.png' ) );
				echo html( 'img', array( 'src' => plugin_dir_url( dirname( __FILE__ ) ) . '/images/howto-wrap-ads-in-blocks.png' ) );
*/
				echo html( 'h2', __( 'Choose the Duration' , 'wp-relevant-ads' ) );

				echo html( 'p', __( "Ads can be set to expire on a specific date or can run forever.", 'wp-relevant-ads' ) );

				echo html( 'img', array( 'src' => plugin_dir_url( dirname( __FILE__ ) ) . '/images/howto-expiration.png' ) );

				echo html( 'h2', __( 'Call to Action' , 'wp-relevant-ads' ) );

				echo html( 'p', __( "<em>Call to Action</em> Ads are special Ads that you can create to redirect a user to a specific page, usually to sell Ad slots on the site. Check the <em>Call to Action</em> option to redirect users to your Ads sales page when the Ad is clicked.", 'wp-relevant-ads' ) );

				echo html( 'img', array( 'src' => plugin_dir_url( dirname( __FILE__ ) ) . '/images/howto-call-to-action.png' ) );

				echo html( 'h2', __( 'More' , 'wp-relevant-ads' ) );

				echo html( 'p', __( "Additionally, you can sort Ads, monitor Ad clicks, and also style any Ad using your own CSS classes.", 'wp-relevant-ads' ) );

				echo html( 'p', sprintf( __( "These options are all available on the <a href='%s'>edit Ad page</a>." , 'wp-relevant-ads' ), 'post-new.php?post_type=' . $this->post_type ) );

				break;

			case 'widgets':
				echo html( 'p', sprintf( __( '<em>%1$s</em> can be configured to display only in Widgets. Simply select <em>Widget</em> as the Ad trigger and assign it an Ad Category. You can then specify the Ad Categories that each widget should display.', 'wp-relevant-ads' ), 'WP Relevant Ads' ) );
				echo html( 'p', __( 'No additional file changes are needed to trigger Ads using Widgets.', 'wp-relevant-ads' ) );
				break;

			case 'shortcodes':
				echo html( 'p', sprintf( __( '<em>%1$s</em> can be displayed/placed on your website using <a href="%2$s" target="_blank">Shortcodes</a>. '
								. 'Either placed directly inside your posts using <em>WordPress</em> embedded Shortcodes tags <code>%3$s</code> (click the icon %4$s on the <em>TinyMCE</em> tools menu), or placed inside PHP pages with <code>%5$s</code> PHP function. '
								. 'Although PHP allows for greater flexibility, it is not recommended for users not familiar with PHP.', 'wp-relevant-ads' ),
								'WP Relevant Ads', 'https://codex.wordpress.org/Shortcode_API', '[wp_relevant_ads]', '<i class="icon-billboard" style="font-size: 16px;"></i>',"do_shortcode('[wp_relevant_ads]')" ) );

				echo html( 'p', __( 'To display Ads using shortcodes make sure you have published Ads with a <em>Shortcode</em> trigger.', 'wp-relevant-ads' ) );

				echo html( 'p', __( 'Without additional parameters, shortcodes will always output all your <em>Shortcode</em> triggered Ads based on the page taxonomy terms. You can fine tune which Ads are visible, using the additional parameters explained below.', 'wp-relevant-ads' ) );
				break;

			case 'hooks':
				echo html( 'p', sprintf( __( '<em>%1$s</em> can be triggered using any <a href="%2$s">Action Hook</a> provided by WordPress or by the active themes/plugins, as long as they are located on a page that outputs any content with taxonomy terms. '
								. '<em>Action Hooks</em> can be identified inside PHP files by the <code>%3$s</code> function call. Only use hooks that you\'re familiar with to avoid having Ads displayed incorrectly or multiple times.', 'wp-relevant-ads' ),
								'WP Relevant Ads', 'https://codex.wordpress.org/Plugin_API/Hooks', "do_action('action_name')" ) );
				echo html( 'p', __( 'No additional file changes are needed to trigger Ads using hooks. You just need to find the hook that should trigger your Ads and select it from the available hooks '
								. 'list, on the edit Ad page. If the hook you want to use is not listed you can add it to the \'Other Hooks\' field.', 'wp-relevant-ads' ) );
				break;

			case 'dom':
				echo html( 'p', sprintf( __( '<em>%1$s</em> can display Ads virtually anywhere on your site, using <a href="%2$s">jQuery Selectors</a>. Selectors act as placeholders to display the Ads (i.e: <code>%3$s</code>). Some CSS and/or jQuery knowledge is recommended.', 'wp-relevant-ads' ),
								'WP Relevant Ads', 'https://www.w3schools.com/cssref/css_selectors.asp', ".display-ad-here" ) );
				echo html( 'p', __( 'No additional file changes are needed to trigger Ads using jQuery Selectors but it may be easier to ad your own CSS classes to act as the placeholders. You just need to find the place where the Ad should be displayed and specify the appropriate CSS selector.', 'wp-relevant-ads' ) );
				break;

			case 'more':
				echo html( 'p', sprintf( __( 'Additional ways to display Ads coming soon. Keep a close eye on the <a href="%s">add-ons</a>.', 'wp-relevant-ads' ), esc_url( $this->addons_url ) ) );
				break;

			default:
				break;
		}

	}

	public function before_tab_section( $section ) {

			if ( 'quick_start' === $section ) {
				//
			} elseif ( 'hooks_examples' === $section ) {
				echo html( 'h2', __( 'Display Ads on Single pages', 'wp-relevant-ads' ) );

				echo html( 'em', sprintf( __( '%s: You have a Classifieds site and want to display relevant Ads when users are viewing classified ads with the terms: outdoor, sports.', 'wp-relevant-ads' ), html( 'strong', __( 'Use Case', 'wp-relevant-ads' ) ) ) );

				echo '<hr/>';

				echo html( 'p', sprintf( __( 'Supposing your theme provides a hook named <code>%s</code>, triggered by <code>%s</code> on single custom post type pages.', 'wp-relevant-ads' ), 'after_single_listing', "do_action('after_single_listing')" ) );
				echo html( 'p', __( "1. Create a new <em>Relevant Ad</em> named 'Outdoor And Sports (hook)' ", 'wp-relevant-ads' ) );
				echo html( 'p', __( "2. Choose 'Outdoor' and 'Sports' from the 'Terms' dropdown list.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "3. On the 'Display' metabox, choose 'Hook' and look for 'after_single_listing' on the dropdown list. If it's not listed add it to 'Other Hooks' field.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "4. Publish the Ad and you're done. Any classified ad containing the terms, 'outdoors' or 'sports', should now display your newly created Ad.", 'wp-relevant-ads' ) );

				echo '<br/>';

				echo html( 'h2', __( 'Display Ads on Category pages', 'wp-relevant-ads' ) );
				echo html( 'em', sprintf( __( "%s: You want to display 'Automotive' Ads when users are browsing 'Automotive' categories.", 'wp-relevant-ads' ), html( 'strong', __( 'Use Case', 'wp-relevant-ads' ) ) ) );

				echo '<hr/>';

				echo html( 'p', sprintf( __( 'Supposing your theme provides a hook named <code>%s</code>, triggered by <code>%s</code> on single post pages.', 'wp-relevant-ads' ), 'before_category_listing', "do_action('before_category_listing')" ) );

				echo html( 'p', __( "1. Create a new <em>Relevant Ad</em> named 'Automotive (hook)' ", 'wp-relevant-ads' ) );
				echo html( 'p', __( "2. Choose 'Automotive' related terms from the 'Terms' dropdown list.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "3. On 'Display', choose 'Hook' and look for 'before_category_listing' on the dropdown list. If it's not listed add it to 'Other Hooks' field.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "4. Publish the Ad and you're done. Automotive category pages should now display your newly created Ad.", 'wp-relevant-ads' ) );

			} elseif ( 'dom_examples' === $section ) {
				echo html( 'h2', __( 'Display Ads on Single pages', 'wp-relevant-ads' ) );

				echo html( 'em', sprintf( __( '%s: You have a Classifieds site and want to display relevant Ads on a specific <code>%s</code> tag when users are viewing classified ads with the terms: outdoor, sports.', 'wp-relevant-ads' ), html( 'strong', __( 'Use Case', 'wp-relevant-ads' ) ), '&lt;div&gt;' ) );

				echo '<hr/>';

				echo html( 'p', sprintf( __( 'Supposing your theme provides a <code>%s</code> tag with a class named <code>%s</code> on single custom post type pages.', 'wp-relevant-ads' ), '&lt;div&gt;', 'ad-content' ) );
				echo html( 'p', __( "1. Create a new jQuery Selector Ad named 'Outdoor And Sports (jQuery Selector)' ", 'wp-relevant-ads' ) );
				echo html( 'p', __( "2. Choose 'Outdoor' and 'Sports' from the 'Terms' dropdown list.", 'wp-relevant-ads' ) );
				echo html( 'p', sprintf( __( "3. On 'Display', choose 'jQuery Selector', select the positioning and add the CSS selector <code>div.ad-content</code> to the input field.", 'wp-relevant-ads' ), 'ad-content' ) );;
				echo html( 'p', __( "4. Publish the Ad and you're done. Any classified ad containing the terms, 'outdoors' or 'sports', should now display your newly created Ad positioned on the specified jQuery Selector.", 'wp-relevant-ads' ) );

				echo '<br/>';

				echo html( 'h2', __( 'Display Ads on other pages', 'wp-relevant-ads' ) );

				echo '<hr/>';

				echo html( 'p', __( 'Repeat the same steps as the previous example by locating an appropriate jQuery Selector to act as the placeholder for the Ad.', 'wp-relevant-ads' ) );

			} elseif ( 'widget_examples' === $section ) {
				echo html( 'h2', __( 'Display Ads on a Single page sidebar', 'wp-relevant-ads' ) );

				echo html( 'em', sprintf( __( '%s: You have a News blog site and want to display relevant Ads using a Widget, on an existing sidebar when users are viewing posts in the \'Sports\' category.', 'wp-relevant-ads' ), html( 'strong', __( 'Use Case', 'wp-relevant-ads' ) ) ) );

				echo '<hr/>';

				echo html( 'p', __( "1. Create a new Widget Ad named 'Sports (Widget)' ", 'wp-relevant-ads' ) );
				echo html( 'p', __( "2. Choose all 'Sports' terms from the 'Terms' dropdown list.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "3. On 'Display', choose 'Widget'.", 'wp-relevant-ads' ) );
				echo html( 'p', __( "4. Create/Select the Ad category 'Sports'.", 'wp-relevant-ads' ) );
				echo html( 'p', sprintf( __( "5. Goto <em>Appearance > Widgets</em>, add a new <em>%s</em> Widget to the single ad page sidebar and select 'Sports' from the 'Ad Category' dropdown.", 'wp-relevant-ads' ), 'WP Relevant Ads' ) );
				echo html( 'p', __( "6. Publish the Ad and you're done. All posts on the 'Sports' category will now display Sports Ads on the single page sidebar.", 'wp-relevant-ads' ) );

				echo '<br/>';

				echo html( 'h2', __( 'Display Ads on other sidebars', 'wp-relevant-ads' ) );

				echo '<hr/>';

				echo html( 'p', __( 'Repeat the same steps as the previous example and select the appropriate Ad category.', 'wp-relevant-ads' ) );

			}
	}

	protected function init_tabs() {
		$this->tabs->add( 'quick_start', __( 'Quick Start', 'wp-relevant-ads' ) );
		$this->tabs->add( 'widgets', __( 'Widgets', 'wp-relevant-ads' ) );
		$this->tabs->add( 'shortcodes', __( 'Shortcodes', 'wp-relevant-ads' ) );
		$this->tabs->add( 'more', __( 'More', 'wp-relevant-ads' ) );

		$this->tab_sections['more']['more'] = array(
			'title'  => '',
			'fields' => array(),
		);

		$this->tab_sections['quick_start']['quick_start'] = array(
			'title'  => '',
			'fields' => array(),
		);

		$this->tab_sections['dom']['dom_examples'] = array(
			'title' => '',
			'fields' => array(),
		);

		$this->tab_sections['shortcodes']['howto'] = array(
			'title'  => html ( 'h2', __( 'Shortcode Types', 'wp-relevant-ads' ) ),
			'fields' => array(
				array(
					'title'  => __( 'Embedded Shortcode', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'shortcode_types_embedded' ),
					'name'   => '_blank',
					'tip'    => __( 'Copy&Paste the shortcode to a page or post in which you want to display relevant Ads.', 'wp-relevant-ads' ),
				),
				array(
					'title'  => __( 'PHP Shortcode', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'shortcode_types_php' ),
					'name'   => '_blank',
					'tip' 	  => __( 'Copy&Paste the shortcode to a PHP file in which you want to display relevant Ads.', 'wp-relevant-ads' ),
				),
			),
		);

		$this->tab_sections['shortcodes']['parameters'] = array(
			'title' => html( 'h2', __( 'Shortcode Parameters', 'wp-relevant-ads' ) ),
			'fields' => array(
				array(
					'title' => __( 'Optional Parameters', 'wp-relevant-ads' ),
					'type'  => '',
					'name'  => '_blank',
					'extra' => array(
						'style' => 'display: none;'
					),

					'tip' =>
							html( 'p', __( 'Fine tune which Ads are visible in each page by adding any of the available parameters to the shortcodes. All parameters are optional.', 'wp-relevant-ads' ) ) .

							'<br/>' .

							html( 'strong',__( 'Embedded', 'wp-relevant-ads' ) ) .
							html( 'p', sprintf( "<code>[%s <strong>%s</strong>='YOUR_AD_ID' <strong>%s</strong>='YOUR_AD_CAT_SLUG' <strong>%s</strong>='YOUR_AD_CSS_CLASS' <strong>%s</strong>='YOUR_AD_CSS_STYLE']</code>", 'wp_relevant_ads', 'ad_id', 'ad_category', 'ad_class', 'ad_style' ) ) .

							'<br/>' .

							html( 'strong', __( 'PHP', 'wp-relevant-ads' ) ) . '<br/>' .
							sprintf( "<code>do_shortcode([%s <strong>%s</strong>='YOUR_AD_ID' <strong>%s</strong>='YOUR_AD_CAT_SLUG' <strong>%s</strong>='YOUR_AD_CSS_CLASS' <strong>%s</strong>='YOUR_AD_CSS_STYLE']);</code>", 'wp_relevant_ads', 'ad_id', 'ad_category', 'ad_class', 'ad_style' ) .

							'<br/>' . __( 'OR *', 'wp-relevant-ads' ) . '<br/>' .

							sprintf( "<code>&lt;?php do_shortcode([%s <strong>%s</strong>='YOUR_AD_ID' <strong>%s</strong>='YOUR_AD_CAT_SLUG' <strong>%s</strong>='YOUR_AD_CSS_CLASS' <strong>%s</strong>='YOUR_AD_CSS_STYLE']); ?&gt;</code>", 'wp_relevant_ads', 'ad_id', 'ad_category', 'ad_class', 'ad_style' ) .

							'<br/><br/>' .

							html( 'p', sprintf( __( '(*) The PHP tags <code>%s</code> are only needed if you\'re pasting the shortcode outside existing <code>%s</code> tags.', 'wp-relevant-ads' ), '&lt;?php ?&gt;','&lt;?php ?&gt;' ) ),

					'desc' =>
							sprintf( __( '%s the Ad ID', 'wp-relevant-ads' ), html( 'code', sprintf( "<strong>%s</strong>='YOUR_AD_ID'", 'id' ) ) ) .
							html( 'p', __( 'Only display the Ad with this specific Ad ID. Replace <em>\'YOUR_AD_ID\'</em> with a valid Ad Id.', 'wp-relevant-ads' ) ) .

							'<br/>' .

							sprintf( __( '%s the Ad Category Slug', 'wp-relevant-ads' ), html( 'code', sprintf( "<strong>%s</strong>='YOUR_AD_CAT_SLUG'", 'category' ) ) ) .
							html( 'p', __( 'Display Ads from this specific Ad Category. Replace <em>\'YOUR_AD_CAT_SLUG\'</em> with a valid Ad Category Slug.', 'wp-relevant-ads' ) ) .

							'<br/>' .

							sprintf( __( '%s the CSS Class style to be applied to the Ads', 'wp-relevant-ads' ), html( 'code', sprintf( "<strong>%s</strong>='YOUR_AD_CSS_CLASS'", 'class' ) ) ) .
							html( 'p', __( 'Style the Ads with this CSS class. Replace <em>\'YOUR_AD_CSS_CLASS\'</em> with a valid CSS class.', 'wp-relevant-ads' ) ) .

							'<br/>' .

							sprintf( __( '%s the CSS style to be applied to the Ads', 'wp-relevant-ads' ), html( 'code', sprintf( "<strong>%s</strong>='YOUR_AD_CSS_STYLE'", 'css' ) ) ) .
							html( 'p', __( 'Style the Ads with this CSS style. Replace <em>\'YOUR_AD_CSS_STYLE\'</em> with a valid CSS style.', 'wp-relevant-ads' ) ),

							/*
							'<br/>' .

							sprintf( __( '%s also display Call to Action Ads', 'wp-relevant-ads' ), html( 'code', sprintf( "<strong>%s</strong>='yes'", 'call_action' ) ) ) .
							html( 'p', __( 'Enqueues Call to Action Ads from the same Ad Category to the retrieved Ads list.', 'wp-relevant-ads' ) ),
							*/
				),
			),

		);

		$this->tab_sections['shortcodes']['examples'] = array(
			'title'  => html( 'h2', __( 'Examples', 'wp-relevant-ads' ) ),
			'fields' => array(
				array(
					'title'  => __( 'Display Single Ad', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'display3' ),
					'name'   => '_blank',
					'tip'    => __( 'Embed this shortcode on a page or post to display a single specific Ad. It expects a valid Ad Id.', 'wp-relevant-ads' ),
				),
				array(
					'title'  => __( 'Display Ads by Category', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'display4' ),
					'name'   => '_blank',
					'tip' 	  => __( 'Embed this shortcode on a page or post to display Ads from a specific Ad Category. It expects a valid Ad Category slug.', 'wp-relevant-ads' ),
				),
				array(
					'title'  => __( 'Display Styled Ads', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'display5' ),
					'name'   => '_blank',
					'tip'    => __( 'Embed this shortcode on a page or post to display all active Ads applying a red border to each one.', 'wp-relevant-ads' ),
				),
				array(
					'title'  => __( 'Multiple Parameters', 'wp-relevant-ads' ),
					'type'   => 'custom',
					'render' => array( $this, 'display6' ),
					'name'   => '_blank',
					'tip'    => __( 'Display and style Ads from a specific category and also display Call to Action Ads on the same category.', 'wp-relevant-ads' ),
				),
			),
		);

		$this->tab_sections['hooks']['hooks_examples'] = array(
			'title'  => '',
			'fields' => array(),
		);

		$this->tab_sections['widgets']['widget_examples'] = array(
			'title'  => '',
			'fields' => array(),
		);

	}

	function display_disabled_text( $text, $width = '65em' ) {
		return html( 'label', array(), html( 'input', array(
			'type'     => 'text',
			'class'    => 'code',
			'value'    => $text,
			'style'    => "width: $width;",
			'disabled' => 'disabled',
		)));

	}

	function shortcode_types_embedded() {
		$text = '[wp_relevant_ads]';
		return $this->display_disabled_text( $text );
	}

	function shortcode_types_php() {
		$text = '<?php echo do_shortcode(\'[wp_relevant_ads]\'); ?>';
		return $this->display_disabled_text( $text );
	}

	function display3(){
		$text = "[wp_relevant_ads id='999']";
		return $this->display_disabled_text( $text );
	}

	function display4(){
		$text = "[wp_relevant_ads category='my-banner-ads']";
		return $this->display_disabled_text( $text );
	}

	function display5(){
		$text = "[wp_relevant_ads css='border: 1px solid red;']";
		return $this->display_disabled_text( $text );
	}

	function display6(){
		$text = "[wp_relevant_ads category='my-banner-ads' css='border: 1px solid red;' class='my-ad-class']";
		return $this->display_disabled_text( $text );
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

}