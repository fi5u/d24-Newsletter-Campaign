<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/public
 * @author     Your Name <email@example.com>
 */
class D24nc_Public {

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
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Register post types for campaign, templates and subscribers.
     *
     * @since    1.0.0
     */
    public function register_post_types() {

        if ( !post_type_exists( 'd24nc_campaign' ) ) {
            register_post_type( 'd24nc_campaign',
                array(  'labels'  => array(
                            'name'                  => __( 'Campaigns', $this->plugin_name ),
                            'singular_name'         => __( 'Campaign', $this->plugin_name ),
                            'menu_name'             => _x( 'Campaigns', 'Admin menu name', $this->plugin_name ),
                            'add_new'               => __( 'Start a Campaign', $this->plugin_name ),
                            'add_new_item'          => __( 'Start a New Campaign', $this->plugin_name ),
                            'edit'                  => __( 'Edit', $this->plugin_name ),
                            'edit_item'             => __( 'Edit Campaign', $this->plugin_name ),
                            'new_item'              => __( 'New Campaign', $this->plugin_name ),
                            'view'                  => __( 'View Campaign', $this->plugin_name ),
                            'view_item'             => __( 'View Campaign', $this->plugin_name ),
                            'search_items'          => __( 'Search Campaigns', $this->plugin_name ),
                            'not_found'             => __( 'No campaigns found', $this->plugin_name ),
                            'not_found_in_trash'    => __( 'No campaigns found in trash', $this->plugin_name ),
                            'parent'                => __( 'Parent Campaign', $this->plugin_name )
                        ),
                        'description'           => __( 'Stores campaign details.', $this->plugin_name ),
                        'public'                => true,
                        'show_ui'               => true,
                        'show_in_menu'          => true, //'newsletter-campaign'
                        'map_meta_cap'          => true,
                        'publicly_queryable'    => true,
                        'exclude_from_search'   => false,
                        'hierarchical'          => false,
                        'query_var'             => true,
                        'supports'              => array( 'title' ),
                        'has_archive'           => true,
                        'show_in_nav_menus'     => true
                )
            );
        }

        if ( !post_type_exists( 'd24nc_template' ) ) {
            register_post_type( 'd24nc_template',
                array(  'labels' => array(
                            'name'                  => __( 'Templates', $this->plugin_name ),
                            'singular_name'         => __( 'Template', $this->plugin_name ),
                            'menu_name'             => _x( 'Templates', 'Admin menu name', $this->plugin_name ),
                            'add_new'               => __( 'Create a Template', $this->plugin_name ),
                            'add_new_item'          => __( 'Create a New Template', $this->plugin_name ),
                            'edit'                  => __( 'Edit', $this->plugin_name ),
                            'edit_item'             => __( 'Edit Template', $this->plugin_name ),
                            'new_item'              => __( 'New Template', $this->plugin_name ),
                            'view'                  => __( 'View Template', $this->plugin_name ),
                            'view_item'             => __( 'View Template', $this->plugin_name ),
                            'search_items'          => __( 'Search Templates', $this->plugin_name ),
                            'not_found'             => __( 'No templates found', $this->plugin_name ),
                            'not_found_in_trash'    => __( 'No templates found in trash', $this->plugin_name ),
                            'parent'                => __( 'Parent Template', $this->plugin_name )
                        ),
                        'description'           => __( 'Stores template details.', $this->plugin_name ),
                        'public'                => true,
                        'show_ui'               => true,
                        'show_in_menu'          => true, //'newsletter-campaign'
                        'map_meta_cap'          => true,
                        'publicly_queryable'    => true,
                        'exclude_from_search'   => false,
                        'hierarchical'          => false,
                        'query_var'             => true,
                        'supports'              => array( 'title' ),
                        'has_archive'           => true,
                        'show_in_nav_menus'     => true
                )
            );
        }

        if ( !post_type_exists( 'd24nc_subscriber' ) ) {
            register_post_type( 'd24nc_subscriber',
                array(  'labels' => array(
                            'name'                  => __( 'Subscribers', $this->plugin_name ),
                            'singular_name'         => __( 'Subscriber', $this->plugin_name ),
                            'menu_name'             => _x( 'Subscribers', 'Admin menu name', $this->plugin_name ),
                            'add_new'               => __( 'Add a Subscriber', $this->plugin_name ),
                            'add_new_item'          => __( 'Add a New Subscriber', $this->plugin_name ),
                            'edit'                  => __( 'Edit', $this->plugin_name ),
                            'edit_item'             => __( 'Edit Subscriber', $this->plugin_name ),
                            'new_item'              => __( 'New Subscriber', $this->plugin_name ),
                            'view'                  => __( 'View Subscriber', $this->plugin_name ),
                            'view_item'             => __( 'View Subscriber', $this->plugin_name ),
                            'search_items'          => __( 'Search Subscribers', $this->plugin_name ),
                            'not_found'             => __( 'No subscribers found', $this->plugin_name ),
                            'not_found_in_trash'    => __( 'No subscribers found in trash', $this->plugin_name ),
                            'parent'                => __( 'Parent Subscriber', $this->plugin_name )
                        ),
                        'description'           => __( 'Stores subscriber details.', $this->plugin_name ),
                        'public'                => true,
                        'show_ui'               => true,
                        'show_in_menu'          => true, //'newsletter-campaign'
                        'map_meta_cap'          => true,
                        'publicly_queryable'    => true,
                        'exclude_from_search'   => false,
                        'hierarchical'          => false,
                        'query_var'             => true,
                        'supports'              => array( 'title' ),
                        'has_archive'           => true,
                        'show_in_nav_menus'     => true
                )
            );
        }

    }

    /**
     * Register taxonomies for subscriber list.
     *
     * @since    1.0.0
     */
    public function register_taxonomies() {

        if ( !taxonomy_exists( 'd24nc_subscriber_list' ) ) {
            register_taxonomy( 'd24nc_subscriber_list', 'd24nc_subscriber',
                array(  'hierarchical'  => true,
                        'label'         => __( 'Subscriber Lists', $this->plugin_name ),
                        'labels'        => array(
                            'name'                          => __( 'Subscriber Lists', $this->plugin_name ),
                            'singular_name'                 => __( 'Subscriber List', $this->plugin_name ),
                            'search_items'                  => __( 'Search Subscriber Lists', $this->plugin_name ),
                            'all_items'                     => __( 'All Subscriber Lists', $this->plugin_name ),
                            'edit_item'                     => __( 'Edit Subscriber List', $this->plugin_name ),
                            'update_item'                   => __( 'Update Subscriber List', $this->plugin_name ),
                            'add_new_item'                  => __( 'Add New Subscriber List', $this->plugin_name ),
                            'new_item_name'                 => __( 'New Subscriber List Name', $this->plugin_name ),
                            'popular_items'                 => __( 'Popular Subscriber Lists', $this->plugin_name ),
                            'separate_items_with_commas'    => __( 'Separate subscriber lists with commas', $this->plugin_name ),
                            'choose_from_most_used'         => __( 'Choose from the most used subscriber lists', $this->plugin_name ),
                            'not_found'                     => __( 'No subscriber lists found', $this->plugin_name )
                        ),
                        'show_ui'           => true,
                        'query_var'         => true,
                        'show_in_nav_menus' => true
            ));
        }

    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D24nc_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D24nc_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/d24nc-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in D24nc_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The D24nc_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/d24nc-public.js', array( 'jquery' ), $this->version, false );

	}

}
