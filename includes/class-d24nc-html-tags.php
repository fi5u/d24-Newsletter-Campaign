<?php

/**
 * The file that defines the html tags class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 */

/**
 * Set HTML tags to be used in shortcodes,
 * shortcode buttons and kses sanitization.
 *
 * @since      1.0.0
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/includes
 * @author     Your Name <email@example.com>
 */
class D24nc_Html_Tags {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * Default values for plugin functionality.
     *
     * @since    1.0.0
     * @access   protected
     * @var      object    $defaults    The default values object.
     */
    protected $defaults;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $plugin_name     The name of this plugin.
     */
    public function __construct( $plugin_name, $defaults ) {

        $this->plugin_name = $plugin_name;
        $this->defaults = $defaults;

    }

    /**
     * Return an array that is used for nesting shortcodes
     *
     * @since  1.0.0
     * @return array    An array of integers up to maximum nest depth
     */
    private function get_nest_depth_array() {
        $nest_array = array();
        for ($i = 0; $i <= $this->defaults->get_max_nest_depth(); $i++) {
            $nest_array[] = $i;
        }
        return $nest_array;
    }

    /**
     * Return the general HTML attributes that apply to most elements
     *
     * @since   1.0.0
     * @return  array   An array of HTML attributes
     */
    private function get_html_general_atts() {

        $attributes = array(
            array(
                'name'  => 'd24nc-shortcode-arg-class',
                'arg'   => 'class',
                'title' => 'class'
            ),
            array(
                'name'  => 'd24nc-shortcode-arg-id',
                'arg'   => 'id',
                'title' => 'id'
            ),
            array(
                'name'  => 'd24nc-shortcode-arg-style',
                'arg'   => 'style',
                'title' => 'style'
            ),
            array(
                'name'  => 'd24nc-shortcode-arg-title',
                'arg'   => 'title',
                'title' => 'title'
            )
        );
        return $attributes;

    }

    /**
     * Return an array of HTML tags nested into categories
     *
     * @since  1.0.0
     * @return array        An array of HTML tags
     */
    public function get_html_tags() {

        $options = get_option( 'd24nc_settings' );
        $subscriber_list_cat_args = array(
            'taxonomy'  => 'd24nc_subscriber_list'
        );

        // Fetch the array of subscriber lists, prepending with 'all lists' option
        $subscriber_list_cats = array_merge( array( array( 'name' => __( 'All lists' ), 'slug' => 'all' ) ), get_categories( $subscriber_list_cat_args ) );

        $html_tags = array(
            array(
                'title'             => __( 'HTML', $this->plugin_name ),
                'class'             => 'd24nc-button-bar__parent',
                'children'          => array(
                    array(
                        'title'             => __( 'Document structure', $this->plugin_name ),
                        'class'             => 'd24nc-button-bar__parent',
                        'instance_include'  => 'd24nc_metabox_template_base-html',
                        'shortcode_only'    => true,
                        'children'          => array(
                            array(
                                'title'     => 'doctype',
                                'id'        => 'd24nc-button-doctype',
                                'class'     => 'd24nc-button-bar__button',
                                'shortcode' => 'd24nc_doctype',
                                'args'      => array(
                                    array(
                                        'name'  => 'd24nc-shortcode-arg-html-doctype',
                                        'arg'   => 'doctype',
                                        'title' => 'Doctype',
                                        'type'  => 'select',
                                        'values'=>  array(
                                            array(
                                                'name' => 'HTML5',
                                                'value'=> 'html5'
                                            ),
                                            array(
                                                'name' => 'HTML 4.01 Strict',
                                                'value'=> 'html-4-01-strict'
                                            ),
                                            array(
                                                'name' => 'HTML 4.01 Transitional',
                                                'value'=> 'html-4-01-transitional'
                                            ),
                                            array(
                                                'name' => 'XHTML 1.0 Strict',
                                                'value'=> 'xhtml-1-strict'
                                            ),
                                            array(
                                                'name' => 'XHTML 1.0 Transitional',
                                                'value'=> 'xhtml-1-transitional'
                                            ),
                                        ),
                                        'key'   => 'name',
                                        'value' => 'value'
                                    )
                                )
                            ),
                            array(
                                'title'             => 'html',
                                'id'                => 'd24nc-button-html',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_html',
                                'enclosing'         => true,
                                'enclosing_text'    => __( 'HTML content', $this->plugin_name ),
                                'args'              => array_merge( array(
                                                            array(
                                                                'name'  => 'd24nc-shortcode-arg-html-xmlns',
                                                                'arg'   => 'xmlns',
                                                                'title' => 'xmlns'
                                                            ),
                                                            array(
                                                                'name'          => 'd24nc-shortcode-arg-html-manifest',
                                                                'arg'           => 'manifest',
                                                                'title'         => 'manifest',
                                                                'as_shortcode'  => false
                                                            )
                                                       ), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'head',
                                'id'                => 'd24nc-button-head',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_head',
                                'enclosing'         => true,
                                /* translators: do not translate ´Head´ - is an HTML element name */
                                'enclosing_text'    => __( 'Head content', $this->plugin_name ),
                                'args'              => array_merge( array(), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'body',
                                'id'                => 'd24nc-button-body',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_body',
                                'enclosing'         => true,
                                /* translators: do not translate ´Body´ - is an HTML element name */
                                'enclosing_text'    => __( 'Body content', $this->plugin_name ),
                                'args'              => array_merge( array(), $this->get_html_general_atts() )
                            )
                        )
                    ),
                    array(
                        /* translators: do not translate ´Head´ - is an HTML element name */
                        'title'             => __( 'Head elements', $this->plugin_name ),
                        'class'             => 'd24nc-button-bar__parent',
                        'instance_include'  => 'd24nc_metabox_template_base-html',
                        'shortcode_only'    => true,
                        'children'          => array(
                            array(
                                'title'             => 'base',
                                'id'                => 'd24nc-button-base',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_base',
                                'args'              => array_merge( array(
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-base-href',
                                                            'arg'   => 'href',
                                                            'title' => 'href'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-base-target',
                                                            'arg'   => 'target',
                                                            'title' => 'target',
                                                            'type'  => 'select',
                                                            'values'=>  array( '_blank', '_parent', '_self', '_top' )
                                                        )
                                                    ), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'link',
                                'id'                => 'd24nc-button-link',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_link',
                                'args'              => array_merge( array(
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-link-charset',
                                                            'arg'   => 'charset',
                                                            'title' => 'charset'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-link-href',
                                                            'arg'   => 'href',
                                                            'title' => 'href'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-link-media',
                                                            'arg'   => 'media',
                                                            'title' => 'media'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-link-rel',
                                                            'arg'   => 'rel',
                                                            'title' => 'rel',
                                                            'type'  => 'select',
                                                            'values'=> array( 'alternate', 'archives', 'author', 'bookmark', 'external', 'first', 'help', 'icon', 'last', 'license', 'next', 'nofollow', 'noreferrer', 'pingback', 'prefetch', 'prev', 'search', 'sidebar', 'stylesheet', 'tag', 'up' )
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-link-type',
                                                            'arg'   => 'type',
                                                            'title' => 'type'
                                                        )
                                                    ), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'meta',
                                'id'                => 'd24nc-button-meta',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_meta',
                                'args'              => array_merge( array(
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-meta-content',
                                                            'arg'   => 'content',
                                                            'title' => 'content'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-meta-http-equiv',
                                                            'arg'   => 'http_equiv',
                                                            'title' => 'http-equiv',
                                                            'type'  => 'select',
                                                            'values'=>  array( 'content-type', 'default-style', 'refresh' )
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-meta-name',
                                                            'arg'   => 'name',
                                                            'title' => 'name',
                                                            'type'  => 'select',
                                                            'values'=>  array( 'viewport', 'application-name', 'author', 'description', 'generator', 'keywords' )
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-meta-scheme',
                                                            'arg'   => 'scheme',
                                                            'title' => 'scheme'
                                                        )
                                                    ), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'style',
                                'id'                => 'd24nc-button-style',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_style',
                                'enclosing'         => true,
                                /* translators: do not translate ´Style´ - is an HTML element name */
                                'enclosing_text'    => __( 'Style content', $this->plugin_name ),
                                'args'              => array_merge( array(
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-style-media',
                                                            'arg'   => 'media',
                                                            'title' => 'media'
                                                        ),
                                                        array(
                                                            'name'  => 'd24nc-shortcode-arg-style-type',
                                                            'arg'   => 'type',
                                                            'title' => 'type',
                                                            'type'  => 'select',
                                                            'values'=> array('text/css')
                                                        )
                                                    ), $this->get_html_general_atts() )
                            ),
                            array(
                                'title'             => 'title',
                                'id'                => 'd24nc-button-title',
                                'class'             => 'd24nc-button-bar__button',
                                'shortcode'         => 'd24nc_title',
                                'enclosing'         => true,
                                /* translators: do not translate ´Title´ - is an HTML element name */
                                'enclosing_text'    => __( 'Title content', $this->plugin_name )
                            )
                        )
                    ),
                    array(
                        /* translators: do not translate ´Body´ - is an HTML element name */
                        'title'             => __( 'Body elements', $this->plugin_name ),
                        'class'             => 'd24nc-button-bar__parent',
                        'children'          => array(
                            array(
                                'title'     => __( 'Block elements', $this->plugin_name ),
                                'class'     => 'd24nc-button-bar__parent',
                                'children'  => array(
                                    array(
                                        'title'             => 'p',
                                        'id'                => 'd24nc-button-p',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_p',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´p´ - is an HTML element name */
                                        'enclosing_text'    => __( 'p content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-p-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h1',
                                        'id'                => 'd24nc-button-h1',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h1',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h1´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h1 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h1-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h2',
                                        'id'                => 'd24nc-button-h2',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h2',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h2´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h2 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h2-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array('left', 'right', 'center', 'justify')
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h3',
                                        'id'                => 'd24nc-button-h3',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h3',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h3´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h3 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h3-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h4',
                                        'id'                => 'd24nc-button-h4',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h4',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h4´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h4 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h4-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h5',
                                        'id'                => 'd24nc-button-h5',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h5',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h5´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h5 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h5-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'h6',
                                        'id'                => 'd24nc-button-h6',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_h6',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´h6´ - is an HTML element name */
                                        'enclosing_text'    => __( 'h6 content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-h6-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'dl',
                                        'id'                => 'd24nc-button-dl',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_dl',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´dl´ - is an HTML element name */
                                        'enclosing_text'    => __( 'dl content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'dt',
                                        'id'                => 'd24nc-button-dt',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_dt',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´dt´ - is an HTML element name */
                                        'enclosing_text'    => __( 'dt content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'dd',
                                        'id'                => 'd24nc-button-dd',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_dd',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´dd´ - is an HTML element name */
                                        'enclosing_text'    => __( 'dd content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'ol',
                                        'id'                => 'd24nc-button-ol',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_ol',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´ol´ - is an HTML element name */
                                        'enclosing_text'    => __( 'ol content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-ol-start',
                                                                    'arg'   => 'start',
                                                                    'title' => 'start'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-ol-type',
                                                                    'arg'   => 'type',
                                                                    'title' => 'type',
                                                                    'type'  => 'select',
                                                                    'values'=> array( '1', 'a', 'A', 'i', 'I' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-ol-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'ul',
                                        'id'                => 'd24nc-button-ul',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_ul',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´ul´ - is an HTML element name */
                                        'enclosing_text'    => __( 'ul content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-ul-type',
                                                                    'arg'   => 'type',
                                                                    'title' => 'type',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'disc', 'square', 'circle' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-ul-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'li',
                                        'id'                => 'd24nc-button-li',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_li',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´li´ - is an HTML element name */
                                        'enclosing_text'    => __( 'li content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-li-type',
                                                                    'arg'   => 'type',
                                                                    'title' => 'type',
                                                                    'type'  => 'select',
                                                                    'values'=> array( '1', 'A', 'a', 'I', 'i', 'disc', 'square', 'circle' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-li-value',
                                                                    'arg'   => 'value',
                                                                    'title' => 'value'
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-li-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'div',
                                        'id'                => 'd24nc-button-div',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_div',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´div´ - is an HTML element name */
                                        'enclosing_text'    => __( 'div content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-div-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-div-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'hr',
                                        'id'                => 'd24nc-button-hr',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_hr',
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-hr-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    )
                                )
                            ),
                            array(
                                'title'     => __( 'Inline elements', $this->plugin_name ),
                                'class'     => 'd24nc-button-bar__parent',
                                'children'  => array(
                                    array(
                                        'title'             => 'a',
                                        'id'                => 'd24nc-button-a',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_a',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´a´ - is an HTML element name */
                                        'enclosing_text'    => __( 'a content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-a-href',
                                                                    'arg'   => 'href',
                                                                    'title' => 'href'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-a-target',
                                                                    'arg'   => 'target',
                                                                    'title' => 'target',
                                                                    'type'  => 'select',
                                                                    'values'=> array( '_blank', '_self', '_parent', '_top' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'em',
                                        'id'                => 'd24nc-button-em',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_em',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´em´ - is an HTML element name */
                                        'enclosing_text'    => __( 'em content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'strong',
                                        'id'                => 'd24nc-button-strong',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_strong',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´strong´ - is an HTML element name */
                                        'enclosing_text'    => __( 'strong content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'span',
                                        'id'                => 'd24nc-button-span',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_span',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´span´ - is an HTML element name */
                                        'enclosing_text'    => __( 'span content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'br',
                                        'id'                => 'd24nc-button-br',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_br',
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'sub',
                                        'id'                => 'd24nc-button-sub',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_sub',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´sub´ - is an HTML element name */
                                        'enclosing_text'    => __( 'sub content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'sup',
                                        'id'                => 'd24nc-button-sup',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_sup',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´sup´ - is an HTML element name */
                                        'enclosing_text'    => __( 'sup content', $this->plugin_name ),
                                        'args'              => array_merge( array(), $this->get_html_general_atts() )
                                    )
                                )
                            ),
                            array(
                                'title'     => __( 'Images', $this->plugin_name ),
                                'class'     => 'd24nc-button-bar__parent',
                                'children'  => array(
                                    array(
                                        'title'             => 'img',
                                        'id'                => 'd24nc-button-img',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_img',
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-img-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'bottom', 'middle', 'left', 'right' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-img-alt',
                                                                    'arg'   => 'alt',
                                                                    'title' => 'alt'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-img-height',
                                                                    'arg'   => 'height',
                                                                    'title' => 'height'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-img-src',
                                                                    'arg'   => 'src',
                                                                    'title' => 'src'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-img-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    )
                                )
                            ),
                            array(
                                'title'     => __( 'Tables', $this->plugin_name ),
                                'class'     => 'd24nc-button-bar__parent',
                                'children'  => array(
                                    array(
                                        'title'             => 'table',
                                        'id'                => 'd24nc-button-table',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_table',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´table´ - is an HTML element name */
                                        'enclosing_text'    => __( 'table content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-table-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'center', 'right' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-table-cellpadding',
                                                                    'arg'   => 'cellpadding',
                                                                    'title' => 'cellpadding'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-table-cellspacing',
                                                                    'arg'   => 'cellspacing',
                                                                    'title' => 'cellspacing'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-table-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-table-border',
                                                                    'arg'   => 'border',
                                                                    'title' => 'border'
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-table-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'tr',
                                        'id'                => 'd24nc-button-tr',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_tr',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´tr´ - is an HTML element name */
                                        'enclosing_text'    => __( 'tr content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tr-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tr-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-tr-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?' ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'th',
                                        'id'                => 'd24nc-button-th',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_th',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´th´ - is an HTML element name */
                                        'enclosing_text'    => __( 'th content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-colspan',
                                                                    'arg'   => 'colspan',
                                                                    'title' => 'colspan'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-height',
                                                                    'arg'   => 'height',
                                                                    'title' => 'height'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-nowrap',
                                                                    'arg'   => 'nowrap',
                                                                    'title' => 'nowrap',
                                                                    'type'  => 'bool'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-rowspan',
                                                                    'arg'   => 'rowspan',
                                                                    'title' => 'rowspan'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-th-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-th-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?' ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'td',
                                        'id'                => 'd24nc-button-td',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_td',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´td´ - is an HTML element name */
                                        'enclosing_text'    => __( 'td content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-colspan',
                                                                    'arg'   => 'colspan',
                                                                    'title' => 'colspan'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-height',
                                                                    'arg'   => 'height',
                                                                    'title' => 'height'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-nowrap',
                                                                    'arg'   => 'nowrap',
                                                                    'title' => 'nowrap',
                                                                    'type'  => 'bool'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-rowspan',
                                                                    'arg'   => 'rowspan',
                                                                    'title' => 'rowspan'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-td-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-td-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?' ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts()),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'colgroup',
                                        'id'                => 'd24nc-button-colgroup',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_colgroup',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´colgroup´ - is an HTML element name */
                                        'enclosing_text'    => __( 'colgroup content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-colgroup-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-colgroup-span',
                                                                    'arg'   => 'span',
                                                                    'title' => 'span'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-colgroup-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-colgroup-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'col',
                                        'id'                => 'd24nc-button-col',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_col',
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-col-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-col-span',
                                                                    'arg'   => 'span',
                                                                    'title' => 'span'
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-col-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-col-width',
                                                                    'arg'   => 'width',
                                                                    'title' => 'width'
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'caption',
                                        'id'                => 'd24nc-button-caption',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_caption',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´caption´ - is an HTML element name */
                                        'enclosing_text'    => __( 'caption content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-caption-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'top', 'bottom' )
                                                                )
                                                            ), $this->get_html_general_atts() )
                                    ),
                                    array(
                                        'title'             => 'thead',
                                        'id'                => 'd24nc-button-thead',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_thead',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´thead´ - is an HTML element name */
                                        'enclosing_text'    => __( 'thead content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-thead-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-thead-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-thead-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'tbody',
                                        'id'                => 'd24nc-button-tbody',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_tbody',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´tbody´ - is an HTML element name */
                                        'enclosing_text'    => __( 'tbody content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tbody-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tbody-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-tbody-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    ),
                                    array(
                                        'title'             => 'tfoot',
                                        'id'                => 'd24nc-button-tfoot',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_tfoot',
                                        'enclosing'         => true,
                                        /* translators: do not translate ´tfoot´ - is an HTML element name */
                                        'enclosing_text'    => __( 'tfoot content', $this->plugin_name ),
                                        'args'              => array_merge( array(
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tfoot-align',
                                                                    'arg'   => 'align',
                                                                    'title' => 'align',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'left', 'right', 'center', 'justify', 'char' )
                                                                ),
                                                                array(
                                                                    'name'  => 'd24nc-shortcode-arg-tfoot-valign',
                                                                    'arg'   => 'valign',
                                                                    'title' => 'valign',
                                                                    'type'  => 'select',
                                                                    'values'=> array( 'top', 'middle', 'bottom', 'baseline' )
                                                                ),
                                                                array(
                                                                    'name'      => 'd24nc-shortcode-arg-tfoot-nesting',
                                                                    'arg'       => 'nesting',
                                                                    'title'     => __( 'How many levels deep nested within same element?', $this->plugin_name ),
                                                                    'type'      => 'select',
                                                                    'values'    => $this->get_nest_depth_array(),
                                                                    'default'   => 0
                                                                )
                                                            ), $this->get_html_general_atts() ),
                                        'nest'              => true
                                    )
                                )
                            ),
                            array(
                                'title'     => __( 'Comments', $this->plugin_name ),
                                'class'     => 'd24nc-button-bar__parent',
                                'children'  => array(
                                    array(
                                        'title'             => 'comment',
                                        'id'                => 'd24nc-button-comment',
                                        'class'             => 'd24nc-button-bar__button',
                                        'shortcode'         => 'd24nc_comment',
                                        'enclosing'         => true,
                                        'enclosing_text'    => __( 'comment content', $this->plugin_name )
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            array(
                'title'             => __( 'Email functionality', $this->plugin_name ),
                'class'             => 'd24nc-button-bar__parent',
                'instance_include'  => 'd24nc_metabox_template_base-html',
                'children'          => array(
                    array(
                        'title'             => __( 'Output posts', $this->plugin_name ),
                        'id'                => 'd24nc-button-posts',
                        'class'             => 'd24nc-button-bar__button',
                        'shortcode'         => 'd24nc_posts'
                    ),
                    array(
                        'title'             => __( 'View in browser', $this->plugin_name ),
                        'id'                => 'd24nc-button-view-browser',
                        'class'             => 'd24nc-button-bar__button',
                        'shortcode'         => 'd24nc_browser_link',
                        'enclosing'         => true,
                        'enclosing_text'    => __( 'View in browser', $this->plugin_name ),
                    ),
                    array(
                        'title'             => __( 'Unsubscribe link', $this->plugin_name ),
                        'id'                => 'd24nc-button-unsubscribe',
                        'class'             => 'd24nc-button-bar__button',
                        'shortcode'         => 'd24nc_unsubscribe_link',
                        'enclosing'         => true,
                        'enclosing_text'    => __( 'Unsubscribe text', $this->plugin_name ),
                        'args'              => array(
                            array(
                                'name'  => 'd24nc-shortcode-arg-unsubscribe-list',
                                'arg'   => 'list',
                                'title' => __( 'Subscriber list', $this->plugin_name ),
                                'type'  => 'select',
                                'values'=>  $subscriber_list_cats,
                                'key'   => 'name',
                                'value' => 'slug'
                            )
                        )
                    )
                )
            ),
            array(
                'title'     => __( 'Personal fields', $this->plugin_name ),
                'class'     => 'd24nc-button-bar__parent',
                'children'  => array(
                    array(
                        'title'     => __( 'Name', $this->plugin_name ),
                        'id'        => 'd24nc-button-personal-name',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_name',
                        'args'      => array(
                            array(
                                'name'  => 'd24nc-shortcode-arg-name-before',
                                'arg'   => 'before',
                                'title' => __( 'Before', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-name-after',
                                'arg'   => 'after',
                                'title' => __( 'After', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-name-noval',
                                'arg'   => 'noval',
                                'title' => __( 'If no value', $this->plugin_name )
                            )
                        )
                    ),
                    array(
                        'title'     => __( 'Email', $this->plugin_name ),
                        'id'        => 'd24nc-button-personal-email',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_email',
                        'args'      => array(
                            array(
                                'name'  => 'd24nc-shortcode-arg-email-before',
                                'arg'   => 'before',
                                'title' => __( 'Before', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-email-after',
                                'arg'   => 'after',
                                'title' => __( 'After', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-email-noval',
                                'arg'   => 'noval',
                                'title' => __( 'If no value', $this->plugin_name )
                            )
                        )
                    ),
                    array(
                        'title'     => __( 'Extra info', $this->plugin_name ),
                        'id'        => 'd24nc-button-personal-extra',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_extra',
                        'args'      => array(
                            array(
                                'name'  => 'd24nc-shortcode-arg-extra-before',
                                'arg'   => 'before',
                                'title' => __( 'Before', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-extra-after',
                                'arg'   => 'after',
                                'title' => __( 'After', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-extra-noval',
                                'arg'   => 'noval',
                                'title' => __( 'If no value', $this->plugin_name )
                            )
                        )
                    )
                )
            ),
            array(
                'title'             => __( 'Post', $this->plugin_name ),
                'class'             => 'd24nc-button-bar__parent',
                'instance_exclude'  => 'd24nc_metabox_template_base-html',
                'children'          => array(
                    array(
                        'title'     => __( 'Post title', $this->plugin_name ),
                        'id'        => 'd24nc-button-post-title',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_post_title'
                    ),
                    array(
                        'title'     => __( 'Post body', $this->plugin_name ),
                        'id'        => 'd24nc-button-post-body',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_post_body'
                    ),
                    array(
                        'title'     => __( 'Featured image', $this->plugin_name ),
                        'id'        => 'd24nc-button-feat-image',
                        'class'     => 'd24nc-button-bar__button',
                        'shortcode' => 'd24nc_feat_image',
                        'args'      => array(
                            array(
                                'name'  => 'd24nc-shortcode-arg-feat-img-size',
                                'arg'   => 'size',
                                'title' => __( 'Size', $this->plugin_name ),
                                'type'  => 'select',
                                'values'=>  get_intermediate_image_sizes()
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-feat-img-width',
                                'arg'   => 'width',
                                'title' => __( 'Width', $this->plugin_name )
                            ),
                            array(
                                'name'  => 'd24nc-shortcode-arg-feat-img-height',
                                'arg'   => 'height',
                                'title' => __( 'Height', $this->plugin_name )
                            ),
                        )
                    ),
                    array(
                        'title'             => __( 'Post Divider', $this->plugin_name ),
                        'id'                => 'd24nc-button-divider',
                        'class'             => 'd24nc-button-bar__button',
                        'shortcode'         => $options['d24nc_settings_field_divider']
                    )
                )
            )
        );

        return $html_tags;

    }
}