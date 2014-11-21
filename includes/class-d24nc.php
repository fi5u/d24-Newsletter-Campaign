<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 * @author     Your Name <email@example.com>
 */
class D24nc {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      D24nc_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'd24nc';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - D24nc_Loader. Orchestrates the hooks of the plugin.
	 * - D24nc_i18n. Defines internationalization functionality.
	 * - D24nc_Admin. Defines all hooks for the dashboard.
	 * - D24nc_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-d24nc-loader.php';

        /**
         * The class holding default values avoiding the need for global vars
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-d24nc-defaults.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-d24nc-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-d24nc-admin.php';

        /**
         * The class responsible for generating and saving metaboxes.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-d24nc-metaboxes.php';

        /**
         * The class responsible for holding data regarding HTML tags.
         * Used in shortcodes, shortcode buttons and kses sanitization
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-d24nc-html-tags.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-d24nc-public.php';

		$this->loader = new D24nc_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the D24nc_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new D24nc_i18n();
		$plugin_i18n->set_domain( $this->get_d24nc() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

        $defaults = new D24nc_Defaults();
        $html_tags = new D24nc_Html_Tags( $this->get_d24nc(), $defaults );
		$plugin_admin = new D24nc_Admin( $this->get_d24nc(), $this->get_version(), $html_tags );
        $meta_boxes = new D24nc_Metaboxes( $this->get_d24nc(), $this->get_version(), $html_tags );

        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu', 9 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_submenus_last', 10 );
        $this->loader->add_action( 'admin_menu', $meta_boxes, 'remove_meta_boxes' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'add_settings' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_head', $meta_boxes, 'remove_subscriber_taxonomy' );
        $this->loader->add_action( 'admin_head', $meta_boxes, 'add_subscriber_taxonomy' );
        $this->loader->add_action( 'add_meta_boxes', $meta_boxes, 'add_meta_boxes' );
        $this->loader->add_action( 'save_post', $meta_boxes, 'save_meta_boxes', 10, 2 );

        $this->loader->add_filter( 'hidden_meta_boxes', $meta_boxes, 'hide_meta_boxes', 10, 3 );
        $this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'set_messages' );
        //$this->loader->add_filter( 'get_sample_permalink_html', $plugin_admin, 'get_empty' );
        //$this->loader->add_filter( 'pre_get_shortlink', $plugin_admin, 'get_empty' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new D24nc_Public( $this->get_d24nc(), $this->get_version() );

        $this->loader->add_action( 'init', $plugin_public, 'register_post_types' );
        $this->loader->add_action( 'init', $plugin_public, 'register_taxonomies' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_d24nc() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    D24nc_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
