<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 * @author     Your Name <email@example.com>
 */
class D24nc_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin
	 */
	private $version;

    /**
     * An instance of html-tags class.
     *
     * @since    1.0.0
     * @access   private
     * @var      instance    $html_tags    The instance of html-tags class
     */
    private $html_tags;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string        $plugin_name        The name of this plugin
	 * @var      string        $version            The version of this plugin
     * @var      instance      $html_tags          Instance of class Html_Tags
	 */
	public function __construct( $plugin_name, $version, $html_tags ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->html_tags = $html_tags;

	}

    /**
     * Render the plugin settings page
     *
     * @since  1.0.0
     */
    public function output_settings_page() {
        include_once 'partials/d24nc-admin-display.php';
    }

    /**
     * Add the main plugin admin menu and any sub menus.
     *
     * @since    1.0.0
     */
    public function add_menu() {
        add_menu_page(
            __( 'Newsletter Campaign', $this->plugin_name ),// Page title
            __( 'Newsletter', $this->plugin_name ),         // Menu title
            'manage_options',                               // Capability
            $this->plugin_name,                             // Menu slug
            array( $this, 'output_settings_page' ),         // Function
            'dashicons-email-alt'                           // Icon url
        );

        add_submenu_page(
            $this->plugin_name, // Parent slug
            __( 'Newsletter Campaign Settings', $this->plugin_name ),   // Page title
            __( 'Settings', $this->plugin_name ),                       // Menu title
            'manage_options',                                           // Capability
            $this->plugin_name,                                         // Menu slug
            array( $this, 'output_settings_page' )                      // Function
        );

    }

    /**
     * Add sub menus to be output after custom post menu items have been output
     */
    public function add_submenus_last() {
        add_submenu_page(
            $this->plugin_name, // Parent slug
            __( 'Subscriber Lists', $this->plugin_name ), // Page title
            __( 'Subscriber Lists', $this->plugin_name ), // Menu title
            'manage_options', // Capability
            'edit-tags.php?taxonomy=d24nc_subscriber_list&post_type=d24nc_subscriber'
        );
    }

    /**
     * Output a text field to be used on a settings page
     *
     * @since   1.0.0
     * @var     array   $args   Array of called attributes
     */
    public function output_text_field( $args ) {
        $name = $args['name'];
        $options = get_option( 'd24nc_settings' );

        ?><input type="text" name="d24nc_settings[<?php echo $name; ?>]" id="<?php echo $name; ?>" value="<?php echo $options[$name]; ?>"><?php
    }

    /**
     * Outputs pages as dropdown menu
     *
     * @since   1.0.0
     * @var     array   $args   Array of called attributes
     */
    public function output_dropdown_pages( $args ) {
        $name = $args['name'];
        $options = get_option( 'd24nc_settings' );

        $wp_dropdown_args = array(
            'selected'          => $options[$name],
            'name'              => 'd24nc_settings[' . $name . ']',
            'id'                => $name,
            'show_option_none'  => __('Default text', $this->plugin_name),
            'show_option_value' => ''
        );
        wp_dropdown_pages($wp_dropdown_args);
    }

    /**
     * Display settings section
     *
     * @since   1.0.0
     * @var     array   $args   Array of called attributes
     */
    public function output_settings_section( $args ) {
        echo $args['title'];
    }

    /**
     * Add settings to populate the admin settings page
     *
     * @since   1.0.0
     */
    public function add_settings() {
        register_setting( 'd24nc_settings_page', 'd24nc_settings' );

        /**
         * Settings sections
         */
        add_settings_section(
            'd24nc_settings_section_unsubscribe',           // id
            __( 'Unsubscription', $this->plugin_name ),     // title
            array( $this, 'output_settings_section' ),      // callback
            'd24nc_settings_page'                           // page
        );

        add_settings_section(
            'd24nc_settings_section_shortcodes',            // id
            __( 'Shortcodes', $this->plugin_name ),         // title
            array( $this, 'output_settings_section' ),      // callback
            'd24nc_settings_page'                           // page
        );

        /**
         * Settings fields
         */
        add_settings_field(
            'd24nc_settings_field_unsubscribe',             // id
            __( 'Unsubscribe page', $this->plugin_name ),   // title
            array( $this, 'output_dropdown_pages' ),        // callback
            'd24nc_settings_page',                          // page
            'd24nc_settings_section_unsubscribe',           // section
            array(                                          // args
                'label_for' => 'd24nc_settings_field_unsubscribe',
                'name'      => 'd24nc_settings_field_unsubscribe'
            )
        );

        add_settings_field(
            'd24nc_settings_field_divider',                     // id
            __( 'Shortcode divider text', $this->plugin_name  ),// title
            array( $this, 'output_text_field' ),                // callback
            'd24nc_settings_page',                              // page
            'd24nc_settings_section_shortcodes',                // section
            array(                                              // args
                'label_for' => 'd24nc_settings_field_divider',
                'name'      => 'd24nc_settings_field_divider'
            )
        );
    }

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        $screen = get_current_screen();
        if ( $screen->base === 'post' && $screen->post_type === 'd24nc_template' ) {
            /**
             * Styles for template post type on post edit screen
             */
            wp_enqueue_style( $this->plugin_name . '-codemirror-style', plugin_dir_url( __FILE__ ) . 'css/codemirror.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name . '-button-bar-style', plugin_dir_url( __FILE__ ) . 'css/button-bar.css', array(), $this->version, 'all' );

        }

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/d24nc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        $screen = get_current_screen();
        if ( $screen->base === 'post' && $screen->post_type === 'd24nc_template' ) {
            /**
             * Scripts for template post type on post edit screen
             */

            $drag_drop_deps = array(
                'jquery',
                'jquery-ui-core',
                'jquery-ui-widget',
                'jquery-ui-mouse',
                'jquery-ui-draggable',
                'jquery-ui-droppable'
            );

            $codemirror_args = array(
                'lineNumbers'   => true,
                'mode'          => 'htmlmixed'
            );

            $template_translations = array(
                'optional'          => __( 'Optional', $this->plugin_name ),
                'insert'            => __( 'Insert', $this->plugin_name ),
                'cancel'            => __( 'Cancel', $this->plugin_name ),
                'selectAnOption'    => __( 'Select an option', $this->plugin_name )
            );

            wp_enqueue_script( $this->plugin_name . '-codemirror-script', plugin_dir_url( __FILE__ ) . 'js/codemirror/codemirror.js', array(), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-codemirror-xml', plugin_dir_url( __FILE__ ) . 'js/codemirror/xml.js', array(), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-codemirror-javascript', plugin_dir_url( __FILE__ ) . 'js/codemirror/javascript.js', array(), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-codemirror-css', plugin_dir_url( __FILE__ ) . 'js/codemirror/css.js', array(), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-codemirror-html', plugin_dir_url( __FILE__ ) . 'js/codemirror/htmlmixed.js', array(
                $this->plugin_name . '-codemirror-xml',
                $this->plugin_name . '-codemirror-javascript',
                $this->plugin_name . '-codemirror-css'
            ), $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-repeater-script', plugin_dir_url( __FILE__ ) . 'js/repeater.js', $drag_drop_deps, $this->version, true );
            wp_enqueue_script( $this->plugin_name . '-template-script', plugin_dir_url( __FILE__ ) . 'js/template.js', array(
                'jquery',
                $this->plugin_name . '-codemirror-script',
                $this->plugin_name . '-codemirror-html'
            ), $this->version, true );

            wp_localize_script( $this->plugin_name . '-template-script', 'codemirrorArgs', $codemirror_args );
            wp_localize_script( $this->plugin_name . '-template-script', 'translation', $template_translations );
            wp_localize_script( $this->plugin_name . '-template-script', 'buttons', $this->html_tags->get_html_tags() );
        }

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/d24nc-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Override default messages
     *
     * @since 1.0.0
     */
    public function set_messages($messages) {

        $screen = get_current_screen();
        switch ( $screen->post_type ) {
            case 'd24nc_campaign':
                $messages['post'][1] = __( 'Campaign saved.', $this->plugin_name );
                $messages['post'][4] = __( 'Campaign saved.', $this->plugin_name );
                $messages['post'][6] = __( 'Campaign saved.', $this->plugin_name );

                break;

            case 'd24nc_template':
                $messages['post'][1] = __( 'Template saved.', $this->plugin_name );
                $messages['post'][4] = __( 'Template saved.', $this->plugin_name );
                $messages['post'][6] = __( 'Template saved.', $this->plugin_name );

                break;

            case 'd24nc_subscriber':
                $messages['post'][1] = __( 'Subscriber saved.', $this->plugin_name );
                $messages['post'][4] = __( 'Subscriber saved.', $this->plugin_name );
                $messages['post'][6] = __( 'Subscriber saved.', $this->plugin_name );

                break;
        }
        return $messages;

    }

    /**
     * Return an empty string, used for filters to remove content completely
     * Only works for this plugin's screens
     *
     * @since   1.0.0
     * @var     mixed    $return     The current return value
     * @return  string               An empty string
     */
    public function get_empty( $return ) {

        $screen = get_current_screen();
        if ( strpos( $screen->post_type, $this->plugin_name ) !== false ) {
            $return = '';
        }
        return $return;

    }

}
