<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package WP Relevant Ads/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin related class.
 */
class WP_Relevant_Ads_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @var string $plugin The ID of this plugin.
	 */
	private $plugin;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $wp_relevant_ads       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin, $version ) {
		$this->plugin = $plugin;
		$this->version = $version;

		$this->admin_hooks();
		$this->load_dependencies();
		$this->setup_admin_page();
		$this->setup_metaboxes();
	}

	/**
	 * Init admin hooks.
	 */
	protected function admin_hooks() {
		add_action( 'save_post', array( $this, 'delete_transients' ) );
		add_action( 'pre_get_posts', array( $this, 'orderby' ) );
		add_filter( 'fs_plugins_api', array( $this, 'fix_addons_title' ), 99 );
	}

	/**
	 * Load any file dependencies.
	 */
	protected function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-relevant-ads-howto.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-relevant-ads-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-relevant-ads-metaboxes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-relevant-ads-tutorial.php';
	}

	/**
	 * Instantiate all related admin classes.
	 */
	protected function setup_admin_page() {
		global $wp_relevant_ads_options;

		new WP_Relevant_Ads_Admin_Page( $wp_relevant_ads_options );
		new WP_Relevant_Ads_HowTo_Page( $wp_relevant_ads_options );

		new WP_Relevant_Ads_Guided_Tutorial_Listing;
		new WP_Relevant_Ads_Guided_Tutorial_Single;
	}

	/**
	 * Initialize all meta boxes.
	 */
	protected function setup_metaboxes() {
		$metaboxes = array(
			'WP_Relevant_Ads_Call_Action',
			'WP_Relevant_Ads_Clicks',
			'WP_Relevant_Ads_Rules',
			'WP_Relevant_Ads_Display',
			'WP_Relevant_Ads_Expiration',
			'WP_Relevant_Ads_CSS',
			'WP_Relevant_Ads_Author_Meta'
		);

		$metaboxes = apply_filters( 'wp_relevant_ads_metaboxes', $metaboxes );

		foreach ( $metaboxes as $metabox ) {
			new $metabox;
		}
	}

	/**
	 * Outputs formatted admin messages.
	 *
	 * @since 1.0.0
	 */
	public static function output_admin_msg( $msg, $type = 'error' ) {
?>
		<div class="<?php echo esc_attr( $type ); ?>">
			<p><?php echo wp_kses_post( $msg ); ?></p>
		</div>
<?php
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles( $hook ) {
		global $typenow;

		wp_enqueue_style( $this->plugin . '-fontello', plugin_dir_url( __FILE__ ) . 'css/fontello.css', array(), $this->version, 'all' );

		if ( 'widgets.php' == $hook ) {
			wp_enqueue_style( $this->plugin . '-widgets', plugin_dir_url( __FILE__ ) . 'css/wp-relevant-ads-widgets.css', array(), $this->version, 'all' );
		}

		$pages = array( 'post.php', 'post-new.php' );

		if ( $this->post_type === $typenow ) {

			$ext = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css';

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( $this->plugin, plugin_dir_url( __FILE__ ) . "css/wp-relevant-ads-admin{$ext}", array(), $this->version, 'all' );
		}

		if ( ! in_array( $hook, $pages ) || $this->post_type != $typenow ) {
			return;
		}

		wp_enqueue_style( 'jquery-ui-style', plugin_dir_url( __FILE__ ) . 'css/jquery-ui-1.10.3.custom.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2/select2.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		global $typenow;

		$pages = array( 'post.php', 'post-new.php' );

		if ( ! in_array( $hook, $pages ) || $this->post_type != $typenow ) {
			return;
		}

		$ext = ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.js';

		wp_enqueue_script( array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-datepicker' ) );

		wp_enqueue_script( $this->plugin.'-validate', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js' , array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin.'-single-edit', plugin_dir_url( __FILE__ ) . "js/wp-relevant-ads-single-edit{$ext}" , array( 'jquery', 'jquery-ui-datepicker' ), $this->version, true );
		wp_enqueue_script( $this->plugin.'-select2', plugin_dir_url( __FILE__ ) . 'js/select2/select2.min.js',	array('jquery'), $this->version, true );

        $ad_tax_terms = $ad_roles = $ad_hooks = '';

		if ( ! empty( $_GET['post'] ) ) {
            $ad_tax_terms = get_post_meta( intval( $_GET['post'] ), $this->field_prefix.'terms', true );
			$ad_hooks = get_post_meta( intval( $_GET['post'] ), $this->field_prefix.'hooks', true );
        }

		/* Script variables */
		$params = array(
			'ad_tax_terms'       => $ad_tax_terms,
			'ad_hooks'           => $ad_hooks,
			'select_option'      => __( '(All) Click to select...', 'wp-relevant-ads' ),
			'select_taxonomies'  => __( '(Ignore) Click to select terms...', 'wp-relevant-ads' ),
			'select_hooks'       => __( 'Click to select hooks...', 'wp-relevant-ads' ),
			'hook_required_text' => __( 'Please select or specify a hook', 'wp-relevant-ads' ),
			'dom_required_text'  => __( 'Please specify a selector', 'wp-relevant-ads' ),
			'date_format'        => WP_Relevant_Ads_Utils::ui_date_format(),
		);
		$params = apply_filters( 'wp_relevant_ads_enqueue_params', $params );

		wp_localize_script( $this->plugin.'-single-edit', 'wp_relevant_ads_params', $params );
	}

	/**
	 * Create the shortcode button
	 *
	 * @since 1.0.0
	 */
	public function shortcode_button() {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'sc_add_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'sc_register_button' ) );
		}
	}

	/**
	 * Register TinyMCE Plugin
	 */
	 public function sc_add_plugin( $plugin_array ) {
		global $post;

		if ( $this->post_type != $post->post_type ) {
			$plugin_array['wp_relevant_ads_shortcode'] = plugin_dir_url( __FILE__ ) . 'js/wp-relevant-ads-shortcodes.js';
		}
		return $plugin_array;
	}

	/**
	 * Register the custom 'Add Shortcode' Button
	 */
	public function sc_register_button( $buttons ) {
		array_push( $buttons, '|', 'wp_relevant_ads_shortcode' );
		return $buttons;
	}

	/**
	 * Custom columns for the plugin post type list.
	 *
	 * @since	1.0.0
	 */
	public function manage_columns( $column_name, $id ) {

		switch ( $column_name ) {

			case 'taxonomies' :
			case 'category':

				if ( 'category' == $column_name ) {
					$taxs = get_the_terms( $id, $this->taxonomy );
					if ( $taxs ) {
						$taxs = wp_list_pluck( $taxs, 'name' );
					}
				} else {
					$terms = wp_relevant_ads()->get_ad_terms_meta( $id );
					$taxs = array_map( 'get_taxonomy', $terms );
					$taxs = wp_list_pluck( $taxs, 'label' );
				}
				$meta = implode( (array) $taxs, ', ' );
				break;

			case 'clicks' :

				/* Fetch custom information saved in meta data for this ad */
				$meta = (int) get_post_meta( $id, $this->field_prefix . 'clicks', true );
				break;

			case 'trigger' :

				$meta = ucfirst( get_post_meta( $id, $this->field_prefix . 'trigger_rule', true ) );
				break;

			case 'call_action' :

				/* Fetch custom information saved in meta data for this ad */
				$meta = ucfirst( get_post_meta( $id, $this->field_prefix . 'call_action', true ) );
				if ( ! $meta ) {
					$meta = __( 'No', 'wp-relevant-ads' );
				} else {
					$meta = __( 'Yes', 'wp-relevant-ads' );
				}
				break;

			case 'expire_date':

				/* Fetch custom information saved in meta data for this ad */
				$meta = get_post_meta( $id, $this->field_prefix.$column_name, true );

				if ( ! empty( $meta ) ) {
					$meta = date_i18n( get_option( 'date_format' ), $meta );
				} else {
					$meta = '-';
				}

				if ( $this->status_expired == get_post_status( $id ) ) {
					$meta .= html( 'p class="wp_relevant_ads expired"', html( 'strong', __( 'Expired', 'wp-relevant-ads' ) ) );
				}

				break;

			default:
				/* Fetch custom information saved in meta data for this ad */
				$meta = get_post_meta( $id, $this->field_prefix . $column_name, true );
		} // end switch

		if ( ! $meta ) {
			$meta = '-';
		}

		echo $meta;
	}

	/**
	 * Custom columns
	 *
	 * @since	1.0.0
	 */
	public function add_new_columns ( $defaults ) {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Name', 'wp-relevant-ads' ),
			'category'    => __( 'Category', 'wp-relevant-ads' ),
			'taxonomies'  => __( 'Taxonomies', 'wp-relevant-ads' ),
			'trigger'     => __( 'Displayed By', 'wp-relevant-ads' ),
			'call_action' => __( 'Call to Action', 'wp-relevant-ads' ),
			'clicks'      => __( 'Clicks', 'wp-relevant-ads' ),
			'date'        => __('Start Date', 'wp-relevant-ads' ),
			'expire_date' => __( 'Expire Date', 'wp-relevant-ads' ),
		);

		return $columns;
	}

	/**
	 * Sortable custom columns.
	 *
	 * @since	1.0.0
	 */
	public function column_register_sortable( $columns ) {
		$columns['clicks'] = 'clicks';
		$columns['expire_date'] = 'expire_date';
		return $columns;
	}

	/**
	 *
	 * @since	1.0.0
	 */
	public function menu_icon_css() {
		echo "<style type='text/css' media='screen'>
				#adminmenu .menu-icon-wp-relevant-ad div.wp-menu-image:before {
					 font-family:  'fontello' !important;
					 content: '\\e802'; // this is where you enter the fontawesome font code
				 }
			</style>";
	 }

	public function remove_metaboxes() {
		$remove_boxes = array( 'authordiv', 'slugdiv' );

		foreach ( $remove_boxes as $id ) {
			remove_meta_box( $id, $this->post_type, 'normal' );
		}
	}

	/**
	 * Retrieve the appropriate trigger directories based on the active theme.
	 */
	public function trigger_directories_exclude( $directories ) {
		return $directories;
	}

	/**
	 * Provides additional options to the Ads categories terms page.
	 */
	public function term_meta() {

		$settings_url = add_query_arg( array( 'post_type' => $this->post_type, 'page' => 'wp-relevant-ads-settings' ), admin_url( 'edit.php' ) );

		if ( ! empty( $_GET['tag_ID'] ) ) {
			$tag_id = (int) $_GET['tag_ID'];
		} else {
			$tag_id = 0;
		}

		$meta = get_option( 'wp_relevant_ads_terms_meta' );
		?>
		<tr class="form-field">
			<th scope="row"><label for="parent"><?php echo __( 'Wrap Ads', 'wp-relevant-ads' ); ?></label></th>
			<td>
				<input type="checkbox" name="wrap_ads" <?php checked( ! empty( $meta[ $tag_id ]['wrap_ads'] ) ); ?> />
				<?php echo sprintf( __( 'If checked, Ads with this category will be grouped in blocks using the HTML markup you set under the <a href="%s">settings</a> page.', 'wp-relevant-ads' ), $settings_url ); ?>
				<p class="description"><?php echo __( 'Text added to the description field will be used as the block title.', 'wp-relevant-ads' ); ?></p>
			</td>
		</tr>
		<?php
		echo html( 'p', '&nbsp;' );
	}

	/**
	 * Handles posted data for the the terms meta.
	 */
	public function handle_term_meta( $term_id, $tt_id, $taxonomy ) {

		if ( $taxonomy != $this->taxonomy || empty( $_POST['action'] ) || ( ! empty( $_POST['action'] ) && 'add-tag' != $_POST['action'] && 'editedtag' != $_POST['action'] ) ) {
			return;
		}

		$posted_meta = compact( 'wrap_ads', extract( $_POST ) );

		$meta = get_option( 'wp_relevant_ads_terms_meta' );

		$meta[ $term_id ] = $posted_meta;

		update_option( 'wp_relevant_ads_terms_meta', $meta );

	}

	/**
	 * Checks if the current admin page belongs to the plugin.
	 * Helps selectively load plugin content.
	 *
	 * @return boolean True if the current page is from the plugin, False otherwise.
	 */
	public static function is_this_plugin_page() {
		global $pagenow;

		$pages = array( 'post.php' => '', 'post-new.php' => '' );

		if ( ! is_admin() || ! isset( $pages[ $pagenow ] ) ) {
			return false;
		}

		if ( ! empty( $_GET['post'] ) ) {
			$post = (int) $_REQUEST['post'];
			$post_type = get_post_type( $post );
		} else {
			$post_type = $_REQUEST['post_type'];
		}

		return wp_relevant_ads()->post_type == $post_type;
	}

	/**
	 * Delete transients for a given post ID.
	 */
	public function delete_transients( $post_id ) {

		if ( wp_is_post_revision( $post_id ) || wp_relevant_ads()->post_type !== get_post_type( $post_id ) ) {
			return;
		}

		$transient_list = get_transient( '_wp_relevant_ads_transient_list' );

		foreach ( (array) $transient_list as $key => $transient ) {
			delete_transient( $key );
		}
		delete_transient( '_wp_relevant_ads_transient_list' );
	}

	/**
	 * Order numeric columns.
	 */
	public function orderby( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby');

		if ( 'clicks' === $orderby ) {
			$query->set( 'meta_key',$this->field_prefix . 'clicks' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}

	/**
	 * Fixes Add-ons title.
	 */
	public function fix_addons_title( $api ) {

		if ( empty( $_REQUEST['plugin'] ) || false === strpos( $_REQUEST['plugin'], 'wp-relevant-ads' ) ) {
			return $api;
		}
	?>
		<style type="text/css">
			div#plugin-information-title,
			#plugin-information-title h2,
			#plugin-information-title,
			#plugin-information-title.with-banner h2 {
				text-overflow: inherit !important;
			}
		</style>
	<?php
		return $api;
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

