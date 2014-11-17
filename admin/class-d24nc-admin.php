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
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
        ?><input type="text" name="nc_settings[<?php echo $name; ?>]" id="<?php echo $name; ?>" value="<?php echo $options[$name]; ?>"><?php
    }

    /**
     * Outputs pages as dropdown menu
     *
     * @since   1.0.0
     * @var     array   $args   Array of called attributes
     */
    public function output_dropdown_pages( $args ) {
        $options = get_option( 'd24nc_settings' );
        $wp_dropdown_args = array(
            'selected'          => $options[$name],
            'name'              => 'd24nc_settings[' . $args['name'] . ']',
            'id'                => $args['name'],
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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D24nc_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D24nc_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/d24nc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D24nc_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D24nc_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/d24nc-admin.js', array( 'jquery' ), $this->version, false );

	}

}
