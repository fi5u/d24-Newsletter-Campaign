<?php

/**
 * Handles the adding and doing of shortcodes.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 */

/**
 * Handles the adding and doing of shortcodes.
 *
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 * @author     Your Name <email@example.com>
 */
class D24nc_Shortcodes {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin
     */
    private $plugin_name;

    /**
     * Default values for plugin functionality.
     *
     * @since    1.0.0
     * @access   private
     * @var      D24nc_Defaults $defaults       Instance of D24nc_default class
     */
    private $defaults;

    /**
     * An instance of Html_Tags class.
     *
     * @since    1.0.0
     * @access   private
     * @var      instance       $html_tags      The instance of Html_Tags class
     */
    private $html_tags;

    /**
     * Stores output for custom post types within the email.
     *
     * @since    1.0.0
     * @access   private
     * @var      string         $custom_output  Output for custom post types
     */
    private $custom_output;

    /**
     * Stores the content of posts.
     *
     * @since    1.0.0
     * @access   private
     * @var      string         $posts_content  Content of posts
     */
    private $posts_content;

    /**
     * Stores the current post object.
     *
     * @since   1.0.0
     * @access  private
     * @var     object          $post_object    Current post object
     */
    private $post_object;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param   string      $plugin_name        The name of this plugin
     * @param   string      $version            The version of this plugin
     * @param   Html_Tags   $html_tags          The instance of class Html_Tags
     */
    public function __construct( $plugin_name, $defaults, $html_tags ) {

        $this->plugin_name = $plugin_name;
        $this->defaults = $defaults;
        $this->html_tags = $html_tags;

    }

    /**
     * In the passed string, replace occurrences of shortcodes with their respective outputs
     *
     * @since   1.0.0
     * @param   string   $template      The template to do shortcodes on
     * @return  string                  Template with shortcodes converted
     */
    public function do_shortcodes( $template ) {
        return do_shortcode( $template );
    }

    /**
     * Adds shortcodes for nested HTML elements
     *
     * @since   1.0.0
     * @access  private
     */
    private function add_nested_shortcodes() {

        $nested_items = $this->fetch_array_keys( $this->html_tags->get_html_tags(), 'nest', 'title', true );
        foreach ( $nested_items as $item => $value ) {
            for ( $i = 1; $i <= $this->defaults->get_max_nest_depth(); $i++ ) {
                add_shortcode( 'd24nc_' . $value . '_' . $i, array($this, 'set_' . $value) );
            }
        }

    }

    /**
     * Return an array of matching keys
     *
     * @since   1.0.0
     * @access  private
     * @param   array       $search_array       The array to search through
     * @param   string      $key_to_search      The key to perform the search on
     * @param   string      $key_to_return      The value of this key will be added to the return array
     * @param   string      $value_to_search    Return key will only be added if this value matches the value of the search key
     * @return  array                           An array of strings
     */
    private function fetch_array_keys( $search_array, $key_to_search, $key_to_return, $value_to_search = null ) {

        $return_array = array();
        foreach ( $search_array as $key => $value ) {
            if ( array_key_exists( $key_to_search, $value ) ) {
                if ( isset( $value_to_search ) ) {
                    if ( $value_to_search === $value[$key_to_search] ) {
                        $return_array[] = $value[$key_to_return];
                    }
                } else {
                    $return_array[] = $value[$key_to_return];
                }
            }
            if ( is_array( $value ) ) {
                $new_return_array = $this->fetch_array_keys( $value, $key_to_search, $key_to_return, $value_to_search );
                $return_array = array_merge( $return_array, $new_return_array );
            }
        }
        return $return_array;

    }

    /**
     * Add general (i.e. not per user) shortcodes
     *
     * @since   1.0.0
     * @access  private
     */
    private function add_general_shortcodes() {

        $shortcode_tags = $this->fetch_array_keys( $this->html_tags->get_html_tags(), 'shortcode', 'shortcode' );
        foreach ( $shortcode_tags as $item => $value ) {
            $shortcode_raw = str_replace('d24nc_', '', $value);
            add_shortcode( $value, array($this, 'set_' . $shortcode_raw) );
        }

    }

    /**
     * Add shortcodes
     *
     * @since  1.0.0
     */
    public function add_shortcodes() {

        $this->add_general_shortcodes();
        $this->add_nested_shortcodes();

    }

    /**
     * Sets the $posts_content variable
     *
     * @since   1.0.0
     * @param   string      $posts_content      Posts content string
     */
    public function set_posts_content( $posts_content ) {
        $this->posts_content = $posts_content;
    }

    /**
     * Set the post object variable
     *
     * @since   1.0.0
     * @param   object      $post_object        The current post object
     */
    public function set_post_object( $post_object ) {
        $this->post_object = $post_object;
    }

    /**
     * Return string saved in $custom_output
     *
     * @since   1.0.0
     * @return  string      String saved in $custom_output
     */
    public function get_custom_shortcode() {
        return $this->custom_output;
    }

    /**
     * Adds a shortcode
     *
     * @since   1.0.0
     * @param   string  $shortcode  The string of the shortcode to add
     * @param   string  $output     The output for this shortcode
     */
    public function add_shortcode( $shortcode, $output ) {

        $this->custom_output = $output;
        add_shortcode( $shortcode, array( $this, 'get_custom_shortcode' ) );

    }
}