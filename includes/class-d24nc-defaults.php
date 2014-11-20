<?php

/**
 * Stores default values to be used throughout the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 */

/**
 * Stores default values to be used throughout the plugin.
 *
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 * @author     Your Name <email@example.com>
 */
class D24nc_Defaults {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      integer    $max_nest_depth    Holds the maximum depth for nesting shortcode elements.
     */
    protected $max_nest_depth = 9;

    /**
     * Return the maximum nesting depth for shortcodes of the same element
     *
     * @since  1.0.0
     * @return integer      The maximum allowed nesting depth of a single element
     */
    public function get_max_nest_depth() {
        return $this->max_nest_depth;
    }

}