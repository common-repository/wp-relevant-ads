<?php
/**
 * Single Ad page metaboxes.
 *
 * @package WP Relevant Ads/Admin/Meta Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Extends 'scbPostMetabox' to add tips to meta boxes.
 */
class WP_Relevant_Ads_Metabox extends scbPostMetabox {

	public function __construct( $id, $title, $args = array() ) {
		parent::__construct( $id, $title, $args );

		add_action( 'admin_init', array( $this, 'init_tooltips' ), 9999 );
	}

	/**
	 * Load tooltips for the current screen.
	 */
	public function init_tooltips() {
		BC_Framework_ToolTips::instance( 'wp-relevant-ad' );
	}

	public function table_row( $row, $formdata, $errors = array() ) {
		return BC_Framework_ToolTips::table_row( $row, $formdata );
	}

	/**
	 *
	 *
	 * @since 1.0.0
	 *
	 * @param type $post_data
	 * @param type $post_id
	 * @param type $meta_key
	 * @return type
	 */
	public static function trim_post_data( $post_data, $post_id, $meta_key = '' ) {
		$valid_data = array();

		$meta = get_post_custom( $post_id );

		if ( ! $post_data && $meta && $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		}

		foreach ( $post_data as $key => $value ) {

			if ( ! isset( $meta[ $key ] ) || $value != $meta[ $key ] ) {
				if ( ! $value && isset( $meta[ $key ] ) ) {
					delete_post_meta( $post_id, $key );
				} elseif ( $value ) {
					$valid_data[ $key ] = $value;
				}
			}
		}
		return $valid_data;
	}

	public function __get( $name ) {
		return wp_relevant_ads()->$name;
	}

}

/**
 * Call to Action related meta boxes.
 */
class WP_Relevant_Ads_Call_Action extends WP_Relevant_Ads_Metabox {

	public function __construct() {

		parent::__construct( 'wp-relevant-ads-call-action', __( 'Call to Action Ad?', 'wp-relevant-ads' ), array(
      'post_type' => $this->post_type,
      'context'   => 'side',
      'priority'  => 'high',
		) );

	}

	public function before_display( $form_data, $post ) {
		return $form_data;
	}

	public function form_fields() {

		$sales_page = get_page_by_path( 'pre-sales-page-wp-relevant-ads' );

		$sales_page_id = 0;

		if ( ! empty( $sales_page->ID ) ) {
			$sales_page_id = $sales_page->ID;
		}

		return array(
			array(
				'title' => __( 'Yes', 'wp-relevant-ads' ),
				'type'  => 'checkbox',
				'name'  => $this->field_prefix . 'call_action',
				'extra' => array(
					'id' => $this->field_prefix . 'call_action',
				),
				'tip' => __( 'Check the option to set this Ad as a \'Call to Action\' Ad. \'Call to Action\' Ads act as placeholders to sell Ad slots on your site.', 'wp-relevant-ads' ),
			),
			array(
				'title'    => __( 'Sales Page', 'wp-relevant-ads' ),
				'type'     => 'select',
				'name'     => $this->field_prefix . 'pre_sale_page',
				'choices'  => $this->get_pages(),
				'selected' => $sales_page_id,
				'extra'    => array(
					'id'    => $this->field_prefix . 'pre_sale_page',
					'class' => 'wp_relevant_ads_select',
					'style' => 'width: 140px',
				),
				'tip' => __( 'Select a page to redirect advertisers wanting to buy Ad slots. If you link this Ad to an existing page all other links on the Ad will be ignored.', 'wp-relevant-ads' ),
			),
		);
	}

	function get_pages() {
		$pages[''] = __( 'None', 'wp-relevant-ads' );
		foreach ( (array) get_pages() as $page ) {
			$pages[ $page->ID ] = $page->post_title;
		}
		return $pages;
	}

	protected function before_save( $post_data, $post_id ) {
		$post_data = $this->trim_post_data( $post_data, $post_id );
		return $post_data;
	}
}

/**
 * Rules related meta boxes.
 */
class WP_Relevant_Ads_Rules extends WP_Relevant_Ads_Metabox {

	// @todo: make sure we can add more rules through filters.

	public function __construct() {
		parent::__construct( 'wp-relevant-ads-rules', __( 'Rules', 'wp-relevant-ads' ), array(
			'post_type' => $this->post_type,
		) );
	}

	public function before_display( $form_data, $post ) {
		echo html( 'p', __( 'Select the rules that makes this Ad relevant.', 'wp-relevant-ads' ) );
		return $form_data;
	}

	public function form_fields() {
		return apply_filters( 'wp_relevant_ads_metabox_rules', $this->get_tax_terms_fields() );
	}

	public function get_tax_terms_fields() {

		// Get taxonomies.
		$args = array(
			'public'  => true,
			'show_ui' => true,
		);
		$taxonomies = get_taxonomies( $args, 'objects' );

		$post_types = WP_Relevant_Ads_Register_Content::get_content( 'post_type', false );
		$post_types = WP_Relevant_Ads_Utils::reduce_multi_array( $post_types );

		$tax_list = $valid_taxonomies = array();

		// Make sure to include only taxonomies for the parent theme.
		foreach ( $taxonomies as $taxonomy ) {

			if ( in_array( $taxonomy->object_type[0], $post_types ) ) {
				$valid_taxonomies[] = $taxonomy;
			}
		}

		foreach ( $valid_taxonomies as $taxonomy ) {
			if ( $this->taxonomy === $taxonomy->name ) { continue;
			}
			$tax_list[ $taxonomy->name ] = $taxonomy->labels->name;
		}

		$options = array();

		foreach ( $tax_list as $tax_name => $tax_label ) {

			$terms = $this->get_terms( $tax_name );
			if ( $terms ) {

				$options = array();

				foreach ( $terms as $term ) {
					$options[ $tax_name . '|' . $term->term_id ] = $term->name;
				}

				$fields[] = array(
					'title' => $tax_label,
					'type'  => 'select',
					'name'  => $tax_name,
					'extra' => array(
						'class'    => 'wp_relevant_ads_select wp_relevant_ads_taxonomies',
						'label'    => $tax_label,
						'multiple' => 'multiple',
					),
					'choices' => $options,
				);

			}
		}

		$fields[] = array(
			'title' => __( 'Terms', 'wp-relevant-ads' ),
			'type'  => 'select',
			'name'  => $this->field_prefix . 'terms[]',
			'desc'  => __( 'Leave blank to ignore taxonomy terms and display this Ad without any criteria.', 'wp-relevant-ads' ),
			'tip'   => __( 'Check at least one term to give a context to your Ads. If you leave it empty the Ad will be displayed wherever you choose to display it.', 'wp-relevant-ads' )
					. '<br/><br/>'
					. sprintf( '<strong>%s</strong>', __( 'Use Case', 'wp-relevant-ads' ) )
					. '<br/><br/>'
					. html( 'em', html( 'strong', __( 'You have a category named \'Sports\', and you want to display this Ad on all \'Sports\' related posts.', 'wp-relevant-ads' ) ) )
					. '<br/>'
					. html( 'em', __( 'Select any or all the terms from the \'Sports\' category to display this Ad on all \'Sports\' related posts.', 'wp-relevant-ads' ) )
					// @todo: SHOW IF OUTSIDE ADD-ON
					. '<br/><br/>' . html( 'em', sprintf( __( '(*) The <em>Free</em> version is limited to \'Post\' terms. If you need to display Ads based on terms from a custom taxonomy checkout the <a href="%s">Post-Types Add-on</a>', 'wp-relevant-ads' ), $this->addons_url ) ),
			'extra' => array(
				'class'    => 'wp_relevant_ads_select wp_relevant_ads_terms wp_relevant_ads_multiple collapse',
				'multiple' => 'multiple',
				'size'     => 5,
			),
			'choices' => array(),
		);
		return $fields;
	}

	function optgroup( $value, $field ) {
		return html( 'optgroup', array( 'label' => $field ) );
	}

	function empty_title( $value, $field ) {
		return '';
	}

	function get_terms( $taxonomy = '' ) {
		$args = array(
			'hide_empty' => false,
		);
		$terms = get_terms( $taxonomy, $args );
		return $terms;
	}

	protected function before_save( $post_data, $post_id ) {
		$post_data = $this->trim_post_data( $post_data, $post_id, $this->field_prefix . 'terms' );

		$terms = array();

		$data = stripslashes_deep( $_POST );
 		if ( ! empty( $data[ $this->field_prefix . 'terms' ] ) ) {
			$terms = $data[ $this->field_prefix . 'terms' ];
		}

		$tax_terms = '';

		foreach ( $terms as $term ) {
			$tax = explode( '|', $term );
			$tax_terms[ $tax[0] ][] = $tax[1];
		}

		update_post_meta( $post_id, $this->field_prefix . 'terms', $tax_terms );

		return $post_data;
	}

}

/**
 * Ads display related meta boxes.
 */
class WP_Relevant_Ads_Display extends WP_Relevant_Ads_Metabox {

	public function __construct() {

		parent::__construct( 'wp-relevant-ads-display', __( 'Display', 'wp-relevant-ads' ), array(
      'post_type' => $this->post_type,
		) );

	}

	public function before_display( $form_data, $post ) {
		echo html( 'p', __( 'Select how this Ad should be displayed.', 'wp-relevant-ads' ) );
		return $form_data;
	}

	public function form_fields() {

		$fields['trigger_type'] = array(
			array(
				'title'   => __( 'Method', 'wp-relevant-ads' ),
				'type'    => 'select',
				'name'    => $this->field_prefix . 'trigger_rule',
				'desc'    => __( 'Select how this Ad should be displayed.', 'wp-relevant-ads' ),
				'tip'     => __( 'Choose how to display the Ad. Through a <em>Widget</em> or using the <em>Shortcodes</em>.', 'wp-relevant-ads' )
						. '<br/><br/>'
						. sprintf( '<strong>%s</strong>', __( 'Widget', 'wp-relevant-ads' ) )
						. '<br/><br/>'
						. sprintf( __( '<a href="%1$s" title="%2$s" rel="nofollow">Widgets</a> are added through the Widgets page. Just assign a category to the Ad and choose it on the \'%3$s\' widget.', 'wp-relevant-ads' ), 'edit.php?post_type=' . $this->post_type . '&page=wp-relevant-ads-howto&tab=widgets', esc_attr( __( 'Click to read more about using Widgets to display Ads.', 'wp-relevant-ads' ) ), 'WP Relevant Ads' )
						. '<br/><br/>'
						. sprintf( '<strong>%s</strong>', __( 'Shortcode', 'wp-relevant-ads' ) )
						. '<br/><br/>'
						. sprintf( __( '<a href="%1$s" title="%2$s" rel="nofollow">Shortcodes</a> allow greater flexibility because you can place them anywhere on your website. You may need to edit some <em>.php</em> files to place them exactly where you want.', 'wp-relevant-ads' ), 'edit.php?post_type=' . $this->post_type . '&page=wp-relevant-ads-howto&tab=shortcodes', esc_attr( __( 'Click to read more about using Shortcodes to display Ads.', 'wp-relevant-ads' ) ) )
						. '<br/><br/>',
						// @todo: SHOW IF OUTSIDE ADD-ON
						//. sprintf( __( '(*) More triggers including CSS selectors, and extended list of native and custom hooks are also available through <a href="%s">Add-ons</a>', 'wp-relevant-ads' ), $this->addons_url ),
				'choices' => $this->trigger_choices(),
				'extra'   => array(
					'class' => 'wp_relevant_ads_select wp_relevant_ads_trigger_type collapse',
					'style' => 'width: 250px;',
				),
			),
		);

		$fields['trigger_type'] = apply_filters( 'wp_relevant_ads_metabox_display', $fields['trigger_type'], 'trigger_type' );

		$fields['shortcode'] = array(
			array(
				'title' => __( 'Shortcode', 'wp-relevant-ads' ),
				'type'  => 'text',
				'name'  => '_blank',
				'desc'  => __( 'Paste this shortcode on a static page to display the Ad.', 'wp-relevant-ads' ),
				'tip'   => sprintf( __( 'Paste this shortcode on a static page where the Ad should be displayed or embed it directly on a WordPress page using the dedicated <em>Relevant Ads</em> icon (%1$s), available on the editing menu.<br/><br/>More details on how to use shortcodes are available <a href="%2$s">here</a>.', 'wp-relevant-ads' ), '<i class="icon-billboard" style="font-size: 23px;"></i>', 'edit.php?post_type=' . $this->post_type . '&page=wp-relevant-ads-howto&tab=shortcodes' ),
				'extra' => array(
					'style'    => 'width: 450px; background-color: #EEE;',
					'disabled' => 'disabled',
					'class'    => 'code wp_relevant_ads_trigger wp_relevant_ads_trigger_shortcode collapse',
				),
				'value' => '[wp_relevant_ads]',
			),
		);

		$fields['shortcode'] = apply_filters( 'wp_relevant_ads_metabox_display', $fields['shortcode'], 'shortcode' );

		$fields['php_shortcode'] = array(
			array(
				'title' => __( 'PHP Shortcode', 'wp-relevant-ads' ),
				'type'  => 'text',
				'name'  => '_blank',
				'desc'  => __( 'Paste this shortcode on a PHP page to display the Ad.', 'wp-relevant-ads' ),
				'tip'   => sprintf( __( 'Paste this shortcode on the PHP file where this Ad should be displayed or embed it directly on a WordPress page using the dedicated <em>Relevant Ads</em> icon (%1$s), available on the editing menu.<br/><br/> More details on how to use shortcodes are available <a href="%2$s">here</a>.', 'wp-relevant-ads' ), '<i class="icon-billboard" style="font-size: 23px;"></i>', 'edit.php?post_type=' . $this->post_type . '&page=wp-relevant-ads-howto&tab=shortcodes' ),
				'extra' => array(
					'style'    => 'width: 450px; background-color: #EEE;',
					'disabled' => 'disabled',
					'class'    => 'code wp_relevant_ads_trigger wp_relevant_ads_trigger_shortcode collapse',
				),
				'value' => "<?php echo do_shortcode('[wp_relevant_ads]'); ?>",
			),
		);

		$fields['php_shortcode'] = apply_filters( 'wp_relevant_ads_metabox_display', $fields['php_shortcode'], 'php_shortcode' );

		//$fields['hook'] = $this->get_hooks();// apply_filters( 'wp_relevant_ads_metabox_trigger', $this->get_hooks(), 'hook' );

		$fields = apply_filters( 'wp_relevant_ads_metabox_display_fields', array_merge( $fields['trigger_type'], $fields['shortcode'], $fields['php_shortcode'] ) );

		return $fields;
	}

	public function get_hooks() {
		global $post;

		$options = array();

		$hook_content = WP_Relevant_Ads_Register_Content::get_content( 'hook',false );

		if ( ! empty( $hook_content ) ) {

			krsort( $hook_content );

			foreach ( $hook_content as $theme => $hooks ) {
				$options[ $theme ] = '_PARENT_' . strtoupper( $theme );

				ksort( $hooks );

				foreach ( $hooks as $key => $description ) {
					$options[ "$theme|$key" ] = "$theme|" . sprintf( '<span class="hook-name">%1$s</span> <span class="hook-description">%2$s</span>', $key, ( $description ? "({$description})" : '' ) );
				}
			}
		}

		$options = array_unique( $options );

		$hooks = get_post_meta( $post->ID, $this->field_prefix . 'hooks', true );

		$fields = array(
			array(
				'title' => __( 'Hooks', 'wp-relevant-ads' ),
				'type'  => 'select',
				'name'  => $this->field_prefix . 'hooks[]',
				'tip'   => __( 'The Ad will be displayed when the specified hooks are called.', 'wp-relevant-ads' )
						. '<br/><br/>'
						. sprintf( '<strong>%s</strong>', __( 'Use Case', 'wp-relevant-ads' ) )
						. '<br/><br/>'
						. html( 'em', html( 'strong', __( 'You want to display the Ad each time a hook called <code>after_single_post</code> is called.', 'wp-relevant-ads' ) ) )
						. '<br/>'
						 . html( 'em', __( 'Choose the <code>after_single_post</code> hook from the hooks list if present, or manually add it to the the \'Other Hook(s)\' field.', 'wp-relevant-ads' ) )
						// @todo: SHOW IF OUTSIDE ADD-ON
						. '<br/><br/>'
						. sprintf( __( '(*) Additional <a href="%s">Add-ons</a> provide the ability to load pre-set lists of hooks from XML files and more positioning options', 'wp-relevant-ads' ), $this->addons_url ),
				'extra' => array(
					'class'          => 'wp_relevant_ads_select wp_relevant_ads_multiple wp_relevant_ads_trigger wp_relevant_ads_trigger_hook large-text',
					'multiple'       => 'multiple',
					'selected_hooks' => ( ! empty( $hooks ) ? implode( ',', $hooks ) : '' ),
					'style'          => 'width: 100%;',
				),
				'choices' => $options,
				'desc'    => __( 'Select one or more known hooks or specify other(s) below.', 'wp-relevant-ads' ),
			),
			array(
				'title' => __( 'Other', 'wp-relevant-ads' ),
				'type'  => 'textarea',
				'tip'   => __( 'Use this field to manually add hooks not listed on the hooks dropdown list. Use commas to separate multiple hooks.', 'wp-relevant-ads' ),
				'name'  => $this->field_prefix . 'hooks_custom',
				'extra' => array(
					'placeholder' => __( 'e.g.: after_single_post, before_single_post', 'wp-relevant-ads' ),
					'class'       => 'wp_relevant_ads_trigger wp_relevant_ads_trigger_hook large-text',
					'cols'        => '58',
					'rows'        => '2',
				),
				'desc' => __( 'Comma separated list of hooks.', 'wp-relevant-ads' ),
			)
		);

		return $fields;
	}

	protected function before_save( $post_data, $post_id ) {
		$post_data = $this->trim_post_data( $post_data, $post_id );

		$hooks = $hooks_custom = '';

		$data = stripslashes_deep( $_POST );
 		if ( ! empty( $data[ $this->field_prefix . 'hooks' ] ) ) {
			$hooks = $data[ $this->field_prefix . 'hooks' ];
		}

 		if ( ! empty( $data[ $this->field_prefix . 'hooks_custom' ] ) ) {
			$hooks_custom = $data[ $this->field_prefix . 'hooks_custom' ];
		}

		update_post_meta( $post_id, $this->field_prefix . 'hooks', $hooks );
		update_post_meta( $post_id, $this->field_prefix . 'hooks_custom', $hooks_custom );

		return $post_data;
	}

	protected function trigger_choices() {
		$triggers = array(
			'widget'    => 'Widget',
			'shortcode' => 'Shortcode',
		);
		return apply_filters( 'wp_relevant_ads_display_choices', $triggers );
	}

}

/**
 * CSS related meta boxes.
 */
class WP_Relevant_Ads_CSS extends WP_Relevant_Ads_Metabox {

	public function __construct() {

		parent::__construct( 'wp-relevant-ads-css', __( 'Styling', 'wp-relevant-ads' ), array(
      'post_type' => $this->post_type,
      'context' => 'side',
		) );

	}

	public function before_form( $post ) {
		echo html( 'p', __( 'Style this Ad using CSS.', 'wp-relevant-ads' ) );
	}

	public function form_fields() {
		return array(
			array(
				'title' => __( 'Class', 'wp-relevant-ads' ),
				'type'  => 'text',
				'name'  => $this->field_prefix . 'class',
				'extra' => array( 'size' => 15, 'class' => 'regular-text', 'style' => 'width: 9.5em;' ),
				'tip'   => __( 'You can style each Ad individually by adding your own CSS classes here.', 'wp-relevant-ads' ),
			),
			array(
				'title' => __( 'CSS', 'wp-relevant-ads' ),
				'type'  => 'textarea',
				'name'  => $this->field_prefix . 'css',
				'extra' => array( 'cols' => 15, 'rows' => 5 ),
				'tip'   => __( 'You can style each Ad individually by adding your own CSS styles here. Normal CSS styles apply, separated by semi-colons (;).', 'wp-relevant-ads' ),
			),
			array(
				'title'   => __( 'Display', 'wp-relevant-ads' ),
				'type'    => 'select',
				'name'    => $this->field_prefix . 'display',
				'tip'     => __( 'Choose whether to display this Ad over/under other Ads (stacked), floating with other Ads (inline) or as set by your CSS style/classes.', 'wp-relevant-ads' ),
				'choices' => array(
					'wp_relevant_ads_clear'  => __( 'Stacked', 'wp-relevant-ads' ),
					'wp_relevant_ads_inline' => __( 'Inline', 'wp-relevant-ads' ),
					''                       => __( 'CSS', 'wp-relevant-ads' ),
				),
				'value' => 'wp_relevant_ads_clear',
			),
		);
	}

}

/**
 * Ads expiration related meta boxes.
 */
class WP_Relevant_Ads_Expiration extends WP_Relevant_Ads_Metabox {

	public function __construct() {
		parent::__construct( 'wp-relevant-ads-duration', __( 'Duration', 'wp-relevant-ads' ), array(
			'post_type' => $this->post_type,
		) );
	}

	public function form_fields() {
		return array(
			array(
				'title'  => __( 'Ad Expiry Date', 'wp-relevant-ads' ),
				'type'   => 'custom',
				'render' => array( $this, 'display_expire_date' ),
				'name'   => $this->field_prefix . 'expire_date',
				'tip'    => __( 'The date the Ad expires. Leave blank to display the Ad indefinitely.', 'wp-relevant-ads' ),
				'extra'  => array(
					'disabled' => 'disabled',
					'style'    => 'display: none;',
				),
			),
		);
	}

	function display_expire_date() {

		if ( ! empty( $_GET['post'] ) ) {
			$timestamp = get_post_meta( intval( $_GET['post'] ), $this->field_prefix . 'expire_date', true );
		}

		if ( ! empty( $timestamp ) ) {
			$expire_date = date_i18n( WP_Relevant_ads_Utils::ui_date_format( 'm/d/Y', 'wp' ), $timestamp );
		} else {
			$expire_date = '';
		}

		$input = html( 'input',
			array(
				'id'          => 'datepicker-field',
				'type'        => 'text',
				'name'        => $this->field_prefix . 'expire_date',
				'class'       => 'regular-text',
				'style'       => 'width: 45em; background-color: #EEE; width: 100px; cursor: pointer;',
				'clear_input' => $this->field_prefix . 'expire_date',
				'value'       => $expire_date,
				'placeholder' => __( 'Pick Date...', 'wp-relevant-ads' ),
		) );

		$a = html( 'a', array( 'class' => 'button-secondary clear_expire_date' ), __( 'Clear', 'wp-relevant-ads' ) );

		$p = html( 'p class="description"', __( 'Leave blank for unlimited.', 'wp-relevant-ads' ) );

		return $input . $a . $p;
	}

	protected function before_save( $post_data, $post_id ) {

		$date = $post_data[ $this->field_prefix . 'expire_date' ];
		$date = strtotime( $date );

		$post_data[ $this->field_prefix . 'expire_date' ] = $date;

		return $post_data;
	}

}

/**
 * Clicks stats related meta boxes.
 */
class WP_Relevant_Ads_Clicks extends WP_Relevant_Ads_Metabox {

	public function __construct() {

		parent::__construct( 'wp-relevant-ads-clicks', __( 'Clicks', 'wp-relevant-ads' ), array(
      'post_type' => $this->post_type,
      'context' => 'side',
		) );

	}

	public function before_form( $post ) {
		echo html( 'p', __( 'The current Ad clicks.', 'wp-relevant-ads' ) );
	}

	public function form_fields() {
		return array(
			array(
				'title' => __( 'Clicks', 'wp-relevant-ads' ),
				'type'  => 'text',
				'name'  => $this->field_prefix . 'clicks',
				'extra' => array( 'size' => 5, 'class' => 'small-text' ),
				'tip'   => __( 'This field will always show the current clicks for this Ad. Leave blank to reset the value to 0.',  'wp-relevant-ads' ),
			),
		);
	}

}

/**
 * Ads author related meta boxes.
 */
class WP_Relevant_Ads_Author_Meta extends WP_Relevant_Ads_Metabox {

	public function __construct() {
		parent::__construct( 'wp-relevant-ads-author', __( 'Ad Owner', 'wp-relevant-ads' ), array(
			'post_type' => $this->post_type,
			'context'   => 'side',
			'priority'  => 'low',
		) );
	}

	public function display( $post ) {
		global $user_ID;
		?>
		<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author' ); ?></label>
		<?php
		wp_dropdown_users( array(
			'name'             => 'post_author_override',
			'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
			'include_selected' => true,
		) );
	}
}
