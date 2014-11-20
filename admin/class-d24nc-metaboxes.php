<?php

/**
 * Generates and saves metaboxes.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 */

/**
 * Generates and saves metaboxes.
 *
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 * @author     Your Name <email@example.com>
 */
class D24nc_Metaboxes {

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
     * An instance of html-tags class.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $html_tags    The instance of html-tags class.
     */
    private $html_tags;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      string    $plugin_name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version, $html_tags ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->html_tags = $html_tags;

    }

    /**
     * Remove the subscriber taxonomy ui box in subscriber edit page
     * to be added back in by add_subscriber_taxonomy below submit box
     *
     * @since    1.0.0
     */
    public function remove_subscriber_taxonomy() {

        remove_meta_box('d24nc_subscriber_listdiv', 'd24nc_subscriber', 'side');

    }

    /**
     * Add the subscriber taxonomy ui box in subscriber edit page below submit box
     *
     * @since    1.0.0
     */
    public function add_subscriber_taxonomy() {

        add_meta_box('d24nc_subscriber_listdiv', __( 'Subscriber Lists', $this->plugin_name ), array($this, 'output_subscriber_taxonomy_ui'), 'd24nc_subscriber', 'side', 'low');

    }

    /**
     * The output for the subscriber tax ui
     * Used to add back in to the edit below the submit box
     *
     * @since    1.0.0
     */
    public function output_subscriber_taxonomy_ui() {

        global $post;
        $defaults = array('taxonomy' => 'subscriber_list');
        if ( !isset($box['args']) || !is_array($box['args']) )
            $args = array();
        else
            $args = $box['args'];
        extract( wp_parse_args($args, $defaults), EXTR_SKIP );
        $tax = get_taxonomy($taxonomy);

        ?>
        <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
            <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
                <li class="tabs"><a href="#<?php echo $taxonomy; ?>-all"><?php echo $tax->labels->all_items; ?></a></li>
                <li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop"><?php _e( 'Most Used' ); ?></a></li>
            </ul>

            <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
                <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
                    <?php $popular_ids = wp_popular_terms_checklist($taxonomy); ?>
                </ul>
            </div>

            <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
                <?php
                $name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
                echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
                ?>
                <ul id="<?php echo $taxonomy; ?>checklist" data-wp-lists="list:<?php echo $taxonomy?>" class="categorychecklist form-no-clear">
                    <?php wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids ) ) ?>
                </ul>
            </div>
        <?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
                <div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
                    <h4>
                        <a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js">
                            <?php
                                /* translators: %s: add new taxonomy label */
                                printf( __( '+ %s' ), $tax->labels->add_new_item );
                            ?>
                        </a>
                    </h4>
                    <p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
                        <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
                        <input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" aria-required="true"/>
                        <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
                            <?php echo $tax->labels->parent_item_colon; ?>
                        </label>
                        <?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;' ) ); ?>
                        <input type="button" id="<?php echo $taxonomy; ?>-add-submit" data-wp-lists="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add" class="button category-add-submit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" />
                        <?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
                        <span id="<?php echo $taxonomy; ?>-ajax-response"></span>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <?php

    }

    /** UNNECESSARY POSS REMOVE
     * Add a meta box using params
     * @var  string       $id            ID of the meta box
     * @var  string       $title         Title of the meta box
     * @var  function     $cb            Callback function
     * @var  string       $post_type     The post type to attach the meta box
     * @var  string       $context       Where the meta box should be placed ('normal', 'advanced', 'side')
     * @var  string       $priority      How soon the meta box should be output ('high', 'core', 'default', 'low')
     * @var  array        $callback_args An array of extra arguments
     */
    /*private function add_meta_box( $id, $title, $cb, $post_type, $context, $priority, $callback_args = null ) {
        add_meta_box(
            $id,
            __($title, $this->plugin_name),
            array($this, $cb),
            $post_type,
            $context,
            $priority,
            $callback_args
        );
    }*/

    /**
     * Gets the meta and post meta strings
     *
     * @since   1.0.0
     * @var     string         $post_type       The post type
     * @var     array/string   $field           A single string for the field or an array of strings
     * @var     string         $meta_name       Used when multiple fields are passed
     * @return  array                           An array of the 'meta' and 'post_meta' values
     */
    private function get_meta( $post_type, $field, $meta_name ) {

        $meta_root = 'd24nc_metabox_' . $post_type . '_';
        if ( is_array( $field ) ) {
            // Provide a default of 'repeater' only if meta name hasn't been passed but it HAS multiple fields
            $meta_name = $meta_name == '' ? 'repeater' : $meta_name;
            $meta = $meta_root . $meta_name;
            $post_meta = '_' . $post_type . '_' . $meta_name;
        } else {
            $meta = $meta_root . $field;
            $post_meta = '_' . $post_type . '_' . $field;
        }

        $return_arr = array( 'meta' => $meta, 'post_meta' => $post_meta );

        return $return_arr;

    }

    /**
     * Get the html for the repeater's drop area
     *
     * @since  1.0.0
     * @param  bool     $closed     Whether to add the closing tag to the element or not
     * @return string               The drop area html
     */
    private function get_droparea($closed) {

        $returnStr = '<div class="d24nc-metabox-repeater__droparea" style="min-height:50px;background:#eee;border:1px solid darkgray;margin:10px 0;">';
        if ($closed === true) {
            $returnStr .= '</div>';
        }

        return $returnStr;

    }

    /**
     * Output an item for the repeater
     *
     * @since   1.0.0
     * @var     integer     $subfield_i     The iteration count
     * @var     string      $subfield       The name of the subfield item
     * @var     string      $post_type      Name of the current post type
     * @var     array       $meta_val       The value stored for the item
     */
    private function output_form_item($subfield_i, $subfield, $post_type, $meta_val = null) {

        // If not the first iteration, add a line break
        if ( $subfield_i !== 0 ) {
            echo '<br>';
        }

        $placeholder = isset( $subfield['placeholder'] ) ? esc_attr( $subfield['placeholder'] ) : esc_attr( $subfield['title'] );

        switch ( $subfield['type'] ) {
            case 'hidden':
                echo '<input type="hidden" class="d24nc-metabox-repeater__hidden-id" name="d24nc_metabox_' . esc_attr( $post_type . '_' . $subfield['field'] ) . '[]" value="';
                if ( isset( $meta_val['d24nc_metabox_' . $post_type . '_' .  $subfield['field']] ) ) {
                    echo $meta_val['d24nc_metabox_' . esc_attr( $post_type . '_' . $subfield['field'] )];
                } else {
                    // Generate a new random string
                    $num = 4;
                    $strong = true;
                    $bytes = openssl_random_pseudo_bytes( $num, $strong );
                    echo bin2hex( $bytes );
                }
                echo '">';

                break;

            case 'button':
                echo '<button name="d24nc_metabox_' . esc_attr( $post_type . '_' . $subfield['field'] ) . '" class="button button-small button-primary">' . esc_html( $subfield['title'] ) . '</button>';

                break;

            case 'textarea':
                echo '<textarea name="d24nc_metabox_' . esc_attr( $post_type . '_' . $subfield['field'] ) . '[]" placeholder="' . esc_attr( $placeholder ) . '" title="' . esc_attr( $placeholder ) . '">';
                if ( isset( $meta_val['d24nc_metabox_' . $post_type . '_' . $subfield['field']] ) ) {
                    echo esc_html( $meta_val['d24nc_metabox_' . $post_type . '_' . $subfield['field']] );
                }
                echo '</textarea>';

                break;

            case 'text':
                echo '<input type="text" name="d24nc_metabox_' . esc_html( $post_type . '_' . $subfield['field'] ) . '[]"';
                if ( isset( $meta_val['d24nc_metabox_' . $post_type . '_' . $subfield['field']] ) ) {
                    echo ' value="' . esc_attr( $meta_val['d24nc_metabox_' . $post_type . '_' . $subfield['field']] ) . '"';
                }
                echo ' placeholder="' . esc_attr( $placeholder ) . '" title="' . esc_attr( $placeholder ) . '">';

                break;

            default:
                // Output as text for default using the title attribute
                echo esc_html( $subfield['title'] );

                break;
        }

    }

    /**
     * Output repeater item delete button html
     *
     * @since   1.0.0
     */
    private function output_delete_button() {

        echo '<button type="button" class="button d24nc-metabox-repeater__droparea-delete">' . __( 'delete', $this->plugin_name ) . '</button>';

    }

    /**
     * Return the html for builder post item
     *
     * @todo             Move to partial
     * @since   1.0.0
     * @var     object   $post          The current post object
     * @var     string   $name_suffix   Suffix for after the name attribute
     * @return  string                  Html output of builder post item
     */
    private function get_builder_post( $post, $name_suffix = null ) {

        $builder_post_item = '<div class="d24nc-metabox-builder__post">
            <input type="hidden" class="d24nc-metabox-builder__post-id" value="' . esc_attr( $post->ID ) . '"';
        $builder_post_item .= $name_suffix ? 'name="d24nc_metabox_builder_' . esc_attr( $name_suffix ) . '[]"': '';
        $builder_post_item .= '>
            <div class="d24nc-metabox-builder__post-title">' . esc_html( $post->post_title ) . '</div>
            <div class="d24nc-metabox-builder__post-excerpt">';
        $builder_post_item .= $post->post_excerpt ? esc_html( $post->post_excerpt ) : esc_html( $post->post_content );

        $builder_post_item .= '</div>
        </div>';

        return $builder_post_item;
    }

    /**
     * Return the html for builder post ´from´ block
     *
     * @since   1.0.0
     * @var     string   $title         Title of the block
     * @var     array    $posts_arr     Array of posts
     * @return  string                  Html output of builder posts ´from´ block
     */
    private function get_builder_post_from_block( $title, $posts_arr ) {

        $builder_from_block = '<div class="d24nc-metabox-builder__from-block"><h4 class="d24nc-metabox-builder__from-block-title">' . esc_html( $title ) . '</h4>';
        if ( $posts_arr ) {
            foreach ( $posts_arr as $builder_post ) {
                $builder_from_block .= $this->get_builder_post( $builder_post );
            }
        } else {
            $builder_from_block .= '<p>' . __( 'No posts', $this->plugin_name ) . '</p>';
        }

        $builder_from_block .= '</div>'; // close div.d24nc-metabox-builder__from-block

        return $builder_from_block;

    }

    /**
     * Check if value is in a multidimensional array
     *
     * @since  1.0.0
     * @var    string       $needle     String to search for
     * @var    array        $haystack   Array to search through
     * @var    bool         $strict     Apply strict rules to the search
     * @return bool                     Is passed array multidimensional
     */
    private function is_multidimensional_array( $needle, $haystack, $strict = false ) {

        foreach ( $haystack as $item ) {
            if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && $this->is_multidimensional_array( $needle, $item, $strict ) ) ) {
                return true;
            }
        }
        return false;

    }

    /**
     * Output html for each meta box
     *
     * @since   1.0.0
     * @var     object     $post       The post object of the current post/page
     * @var     array      $metabox    Metabox item
     */
    public function output_meta_box( $post, $metabox ) {

        $post_type = $metabox['args']['post_type'];
        $field = $metabox['args']['field'];
        $title = $metabox['args']['title'];
        $type = isset( $metabox['args']['type'] ) ? $metabox['args']['type'] : 'text';
        $meta_name = isset( $metabox['args']['meta_name'] ) ? $metabox['args']['meta_name'] : '';

        // Fetch the meta string
        $meta_arr = $this->get_meta( $post_type, $field, $meta_name );
        $meta = $meta_arr['meta'];
        $post_meta = $meta_arr['post_meta'];

        // Set nonce fields
        if ( $type !== 'repeater' ) {
            wp_nonce_field( 'd24nc_metabox_' . $post_type . '_' . $field . '_box', 'd24nc_metabox_' . $post_type . '_' . $field . '_box_nonce' );
            $value = get_post_meta( $post->ID, '_' . $post_type . '_' . $field, true );
        } else {
            // Repeater has a different nonce
            wp_nonce_field( 'd24nc_metabox_' . $post_type . '_repeater_box', 'd24nc_metabox_' . $post_type . '_repeater_box_nonce' );
        }

        switch ( $type ) {
            case 'textarea':
                echo '<textarea id="d24nc_metabox_' . $post_type . '_' . $field .'" name="d24nc_metabox_' . $post_type . '_' . $field . '" placeholder="' . esc_attr( $title ) . '">';
                echo esc_attr( $value );
                echo '</textarea>';
                break;

            case 'select':
                // Get the array of options available keys should be ID and post_title
                $select_options = $metabox['args']['select_options'];

                // If the key or value are not set, issue error and do not render select
                if ( !isset( $metabox['args']['key'] ) || !isset( $metabox['args']['value'] ) ) {
                    echo 'Error: key and value not set';
                } else { // Key and value are set
                    $select_key = $metabox['args']['key'];
                    $select_value = $metabox['args']['value'];

                    if ($select_options) {
                        echo '<select name="d24nc_metabox_' . $post_type . '_' . $field . '">';

                        // Add a blank option
                        $title_lower = strtolower( $metabox['args']['title'] );
                        echo '<option>' . sprintf( __( 'Select %s', $this->plugin_name ), $title_lower ) . '</option>';

                        // Loop through and output options
                        foreach ( $select_options as $option ) {
                            echo '<option value="' . $option->$select_key . '"';
                            if ( $value == $option->$select_key ) {
                                echo ' selected';
                            }
                            echo '>' . $option->$select_value . '</option>';
                        }
                        echo '</select>';
                    } else { // No options found
                        if ( $metabox['args']['not_found'] ) {
                            echo '<p>';
                            $i = 0;
                            $not_found_qty = count( $metabox['args']['not_found'] );
                            foreach ( $metabox['args']['not_found'] as $not_found_line ) {
                                echo $not_found_line;

                                // Add a line break to every line except last line
                                if ($i !== $not_found_qty - 1) {
                                    echo '<br>';
                                }
                                $i++;
                            }
                            echo '</p>';
                        } else { // If no not found lines passed
                            echo __( 'Not found', $this->plugin_name );
                        }
                    }
                }

                break;

            case 'checkbox':
                // Get the array of options available keys should be ID and post_title
                $select_options = $metabox['args']['select_options'];

                // If the key or value are not set, issue error and do not render checkboxes
                if ( !isset( $metabox['args']['key'] ) || !isset( $metabox['args']['value'] ) ) {
                    echo 'Error: key and value not set';
                } else { // Key and value are set
                    $select_key = $metabox['args']['key'];
                    $select_value = $metabox['args']['value'];

                    if ( $select_options ) {
                        // Loop through and output options
                        $i = 0;
                        foreach ( $select_options as $option ) {
                            echo '<input type="checkbox" name="d24nc_metabox_' . $post_type . '_' . $field . '[]" value="' . $option->$select_key . '" id="d24nc_metabox_' . $post_type . '_' . $field . '_' . $option->$select_key . '"';

                            if ( is_array( $value ) && $this->is_multidimensional_array( $option->$select_key, $value ) ) {
                                echo ' checked';
                            }

                            echo '><label for="d24nc_metabox_' . $post_type . '_' . $field . '_' . $option->$select_key . '">' . $option->$select_value . '</label><br>';
                            ++$i;
                        }

                    } else { // No options found
                        if ( $metabox['args']['not_found'] ) {
                            echo '<p>';
                            $i = 0;
                            $not_found_qty = count( $metabox['args']['not_found'] );
                            foreach ( $metabox['args']['not_found'] as $not_found_line ) {
                                echo $not_found_line;

                                // Add a line break to every line except last line
                                if ( $i !== $not_found_qty - 1 ) {
                                    echo '<br>';
                                }
                                $i++;
                            }
                            echo '</p>';
                        } else { // If no not found lines passed
                            echo __( 'Not found', $this->plugin_name );
                        }
                    }
                }

                break;

            case 'repeater':
                // Build the container div
                echo '<div class="d24nc-metabox-repeater">';

                // Add an empty drop area at the start
                echo $this->get_droparea(true);

                // Fetch the repeater field array data from post meta
                $meta_vals = get_post_meta( $post->ID, '_' . $post_type . '_repeater', true );

                if ( $meta_vals ) {

                    foreach ( $meta_vals as $meta_val ) {

                        $subfields = $metabox['args']['subfields'];
                        // Set an incrementor to count each subfield we iterate over
                        $subfield_i = 0;

                        // Add a non-closing drop area that surrounds each repeater item
                        echo $this->get_droparea( false );

                        // Build the container HTML
                        echo '<div class="d24nc-metabox-repeater__item">';

                        // Add the handle to drag the item with
                        echo '<div class="d24nc-metabox-repeater__item-handle">' . __( 'Handle', $this->plugin_name ) . '</div>';

                        // For each field get the array of values stored for it
                        foreach ( $subfields as $subfield ) {
                            // Output the repeater item html
                            $this->output_form_item( $subfield_i, $subfield, $post_type, $meta_val );

                            // Increment the incrementor
                            $subfield_i++;
                        }

                        // Output the delete button
                        $this->output_delete_button();

                        // End div.d24nc-metabox-repeater__item
                        echo '</div>';

                        // End div.d24nc-metabox-repeater__droparea
                        echo '</div>';

                        // Add an empty drop area after the repeater
                        echo $this->get_droparea(true);
                    }

                } else { // Nothing saved in the repeater field yet

                    $subfields = $metabox['args']['subfields'];

                    // Set an incrementor to count each subfield we iterate over
                    $subfield_i = 0;

                    // Add the drop area that surrounds each repeater item
                    echo $this->get_droparea(false);

                    // Build the container HTML
                    echo '<div class="d24nc-metabox-repeater__item">';

                    // For each field get the array of values stored for it
                    foreach ( $subfields as $subfield ) {
                        // Output the repeater item html
                        $this->output_form_item( $subfield_i, $subfield, $post_type );

                        // Increment the incrementor
                        $subfield_i++;
                    }

                    // Output the delete button
                    $this->output_delete_button();

                    // End div.d24nc-metabox-repeater__item
                    echo '</div>';

                    // End div.d24nc-metabox-repeater__droparea
                    echo '</div>';

                    // Add an empty drop area after the repeater
                    echo $this->get_droparea(true);

                }

                // End div.d24nc-metabox-repeater
                echo '</div>';

                // Print out the button row
                ?>
                <div class="d24nc-metabox-repeater__btn-row">
                    <button type="button" class="button" id="d24nc_metabox_repeater_btn_add" disabled="false"><?php echo sprintf( __( 'Add %s', $this->plugin_name ), $metabox['args']['singular'] ); ?></button>
                </div>
                <?php

                break;

            case 'multi':
                // Fetch the multi field array data from post meta
                $meta_vals = get_post_meta( $post->ID, $post_meta, true );

                if ( $meta_vals ) {
                    foreach ( $meta_vals as $meta_val ) {
                        $subfields = $metabox['args']['subfields'];
                        // Set an incrementor to count each subfield we iterate over
                        $subfield_i = 0;

                        // Loop through each subfield
                        foreach ( $subfields as $subfield ) {
                            // Output the repeater item html
                            $this->output_form_item( $subfield_i, $subfield, $post_type, $meta_val );

                            // Increment the incrementor
                            $subfield_i++;
                        }
                    }
                } else {
                    // Nothing yet saved
                    $subfields = $metabox['args']['subfields'];
                    // Set an incrementor to count each subfield we iterate over
                    $subfield_i = 0;

                    // Loop through each subfield
                    foreach ( $subfields as $subfield ) {
                        // Output the repeater item html
                        $this->output_form_item( $subfield_i, $subfield, $post_type );

                        // Increment the incrementor
                        $subfield_i++;
                    }
                }

                break;

            case 'custom':
                if ( $metabox['args']['custom_type'] === 'builder' ) {
                    include_once( 'partials/d24nc-metabox-builder.php' );
                }

                break;

            case 'hash':
                echo '<input type="text" id="d24nc_metabox_' . esc_attr( $post_type . '_' . $field ) . '" name="d24nc_metabox_' . $post_type . '_' . $field . '" value="';
                if ( isset( $value ) && !empty( $value ) ) {
                    echo esc_attr( $value );
                } else {
                    // Generate a random string of 8 digits
                    echo mt_rand();
                }
                echo '">';

                break;

            default:
                // input[type=text]
                echo '<input type="text" id="d24nc_metabox_' . esc_attr( $post_type . '_' . $field ) .'" name="d24nc_metabox_' . esc_attr( $post_type . '_' . $field ) . '"';
                echo ' value="' . esc_attr( $value ) . '" placeholder="'. esc_attr( $title ) . '">';

                break;
        }

    }

    /**
     * Output the html for the send campaign meta box
     *
     * @todo    Needs gettext:ing
     * @since    1.0.0
     */
    public function output_send_campaign_meta_box() {
        global $post;

        // Get the array of ids for all the subscriber lists
        $subscriber_list_ids = get_post_meta( $post->ID, '_d24nc_campaign_subscriber-list-check', true );

        // Prepare the subscriber list output (list which subscriber lists are chosen)
        $subscriber_list_output = '';
        $subscriber_lists_count = count( $subscriber_list_ids );
        $i = 0;
        $email_count = 0;
        if ( $subscriber_list_ids ) {
            foreach ( $subscriber_list_ids as $subscriber_list_id ) {
                // If $subscriber_list_id is an array, then more than one result is found
                if ( is_array( $subscriber_list_id ) ) {
                    $subscriber_list = get_term( $subscriber_list_id['d24nc_metabox_campaign_subscriber-list-check'], 'd24nc_subscriber_list' );
                } else {
                    $subscriber_list = get_term( $subscriber_list_id, 'd24nc_subscriber_list' );
                }
                $subscriber_list_output .= '<strong>' . esc_html( $subscriber_list->name ) . '</strong>';
                if ($subscriber_lists_count - 2 === $i) {
                    // If not the last iteration add a comma and space
                    $subscriber_list_output .= ' and ';
                } else if ($subscriber_lists_count - 1 !== $i) {
                    // If not the last iteration add a comma and space
                    $subscriber_list_output .= ', ';
                }

                $email_count = $email_count + $subscriber_list->count;

                $i++;
            }
        }

        if ( $subscriber_list_ids ) {
            echo '<div class="d24nc-metabox-confirmation" style="display:none;">';
            echo '<p>';
            echo 'You are about to send <strong>' . $post->post_title . '</strong> to ' . esc_html( $subscriber_list_output ) . ' ' . sprintf( _n( 'subscriber list', 'subscriber lists', $subscriber_lists_count, $this->plugin_name ), $subscriber_lists_count ) . ' ' . sprintf( _n( '(which contains <strong>%d</strong> email address)', '(which contain <strong>%d</strong> email addresses)', $email_count, 'newsletter-campaign' ), $email_count ) . '.<br>';
            echo __( 'Are you sure you want to send it?', $this->plugin_name );
            echo '</p>';
            echo '<button type="button" class="button button-secondary" id="d24nc_campaign_send_campaign_cancel">' . __( 'Cancel send', $this->plugin_name );
            echo '</div>';
        } else {
            // No subscriber lists selected or saved
            echo '<div class="nc-campaign__confirmation">';
            echo __('No subscriber lists selected, select one or more subscriber lists and save before sending the campaign.');
            echo '</div>';
        }

        // Set the name of the button so that we can check on page save if we want to send campaign
        echo '<p>';
        echo '<button class="button button-primary" id="d24nc_campaign_send_campaign" name="d24nc_campaign_confirmation_true" value="send_true"';
        if ( !$subscriber_list_ids ) {
            // Prevent send campaign from being clickable if no list selected
            echo ' disabled="disabled"';
        }
        echo '>' . __( 'Send Campaign', $this->plugin_name ) . '</button>';
        echo '</p>';
    }

    /**
     * Add all the required meta boxes
     *
     * @since    1.0.0
     */
    public function add_meta_boxes() {

        add_meta_box(
            'd24nc_metabox_subscriber_name',
            __( 'Name', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_subscriber',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_subscriber',
                'field'     => 'name',
                'title'     => __( 'Name', $this->plugin_name )
            )
        );

        add_meta_box(
            'd24nc_metabox_subscriber_extra',
            __( 'Extra Information', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_subscriber',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_subscriber',
                'field'     => 'extra',
                'title'     => __( 'Extra Information', $this->plugin_name ),
                'type'      => 'textarea'
            )
        );

        add_meta_box(
            'd24nc_metabox_subscriber_hash',
            __( 'Secure Hash', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_subscriber',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_subscriber',
                'field'     => 'hash',
                'title'     => __( 'Secure Hash', $this->plugin_name ),
                'type'      => 'hash'
            )
        );

        add_meta_box(
            'd24nc_metabox_template_base_html',
            __( 'Base HTML', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_template',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_template',
                'field'     => 'base-html',
                'title'     => __( 'Base HTML', $this->plugin_name ),
                'type'      => 'textarea'
            )
        );

        add_meta_box(
            'd24nc_metabox_template_post_html',
            __( 'Post HTML', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_template',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_template',
                'field'     => 'post-html',
                'title'     => __( 'Post HTML', $this->plugin_name ),
                'type'      => 'textarea'
            )
        );

        add_meta_box(
            'd24nc_metabox_template_special_posts',
            __( 'Special Posts', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_template',
            'normal',
            'high',
            array(
                'post_type'     => 'd24nc_template',
                'field'         => 'special-posts',
                'title'         => __( 'Special Posts', $this->plugin_name ),
                'type'          => 'repeater',
                'singular'      => __( 'Special Post', $this->plugin_name ),
                'subfields'     => array(
                    array(
                        'field' => 'special-name',
                        'title' => __( 'Name', $this->plugin_name ),
                        'type'  => 'text'
                    ), array(
                        'field' => 'special-body',
                        'title' => __( 'Special Post HTML', $this->plugin_name ),
                        'type'  => 'textarea'
                    ), array(
                        'field' => 'special-code',
                        'title' => __( 'Special Template Code', $this->plugin_name ),
                        'type'  => 'text'
                    ), array(
                        'field' => 'hidden',
                        'title' => 'hidden',
                        'type'  => 'hidden'
                    )
                )
            )
        );

        add_meta_box(
            'd24nc_metabox_campaign_description',
            __( 'Description', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_campaign',
            'normal',
            'high',
            array(
                'post_type' => 'd24nc_campaign',
                'field'     => 'description',
                'title'     => __( 'Description', $this->plugin_name ),
                'type'      => 'textarea'
            )
        );

        $campaign_template_args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'DESC',
            'post_type'        => 'd24nc_template',
            'post_status'      => 'publish'
        );

        add_meta_box(
            'd24nc_metabox_campaign_template_select',
            __( 'Template', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_campaign',
            'side',
            'low',
            array(
                'post_type'     => 'd24nc_campaign',
                'field'         => 'template-select',
                'title'         => __( 'Template', $this->plugin_name ),
                'type'          => 'select',
                'select_options'=> get_posts( $campaign_template_args ),
                'key'           => 'ID',
                'value'         => 'post_title',
                'not_found'     => array(
                    __( 'No templates found', $this->plugin_name ),
                    '<a href="' . home_url() . '/wp-admin/post-new.php?post_type=d24nc_template">' . __( 'Create a template', $this->plugin_name ) . '</a>'
                )
            )
        );

        $campaign_subscriber_list_args = array(
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hide_empty'    => false
        );

        add_meta_box(
            'd24nc_metabox_campaign_subscriber_list_check',
            __( 'Subscriber List', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_campaign',
            'side',
            'low',
            array(
                'post_type'     => 'd24nc_campaign',
                'field'         => 'subscriber-list-check',
                'title'         => __( 'Subscriber List', $this->plugin_name ),
                'type'          => 'checkbox',
                'select_options'=> get_terms( 'd24nc_subscriber_list', $campaign_subscriber_list_args ),
                'key'           => 'term_id',
                'value'         => 'name',
                'not_found'     => array(
                    __( 'No subscriber lists found', $this->plugin_name ),
                    '<a href="' . home_url() . '/wp-admin/edit-tags.php?taxonomy=d24nc_subscriber_list&post_type=d24nc_subscriber">' . __( 'Create a subscriber list', $this->plugin_name ) . '</a>'
                )
            )
        );

        add_meta_box(
            'd24nc_metabox_campaign_builder',
            __( 'Newsletter Builder', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_campaign',
            'normal',
            'high',
            array(
                'post_type'     => 'd24nc_campaign',
                'field'         => 'builder',
                'title'         => __( 'Newsletter Builder', $this->plugin_name ),
                'type'          => 'custom',
                'custom_type'   => 'builder'
            )
        );

        add_meta_box(
            'd24nc_metabox_campaign_send',
            __( 'Send Campaign', $this->plugin_name ),
            array( $this, 'output_send_campaign_meta_box' ),
            'd24nc_campaign',
            'normal',
            'low'
        );

        add_meta_box(
            'd24nc_metabox_campaign_test_send',
            __( 'Test Send', $this->plugin_name ),
            array( $this, 'output_meta_box' ),
            'd24nc_campaign',
            'side',
            'low',
            array(
                'post_type' => 'd24nc_campaign',
                'field'     => 'test-send',
                'title'     => __( 'Test Send', $this->plugin_name ),
                'type'      => 'multi',
                'subfields' => array(
                    array(
                        'field'         => 'test-send-addresses',
                        'title'         => __( 'Email Addresses', $this->plugin_name ),
                        'type'          => 'textarea',
                        'placeholder'   => __( 'Comma, space or line separated email addresses', $this->plugin_name )
                    ),
                    array(
                        'field' => 'test-send-btn',
                        'title' => __( 'Send Test Email', $this->plugin_name ),
                        'type'  => 'button'
                    )
                ),
                'meta_name' => 'test-send'
            )
        );

    }

    /**
     * Merge two arrays recursively and return
     *
     * @since   1.0.0
     * @var     array   $array1     First array to merge
     * @var     array   $array2     Second array to merge
     * @return  array               The merged array
     */
    private function get_recursively_merged_array(&$array1, &$array2) {

        $result = Array();
        foreach($array1 as $key => &$value) {
            $result[$key] = array_merge($value, $array2[$key]);
        }
        return $result;

    }

    /**
     * Loop through the html tags array to generate an array to add into allowed kses tags
     *
     * @since  1.0.0
     * @return array    An array of html tags and attributes key:tag name, value: attributes
     */
    private function get_kses_extra_tags() {
        $tags = $this->html_tags->get_html_tags();
        foreach ( $tags[0]['children'] as $group ) {
            if ( $group['shortcode_only'] ) {
                continue;
            }
            foreach ( $group['children'] as $tag ) {
                if ( $tag['shortcode_only'] ) {
                    continue;
                }
                if ( $tag['children'] ) {
                    foreach ( $tag['children'] as $tag ) {
                        $this_args = [];
                        foreach ( $tag['args'] as $arg ) {
                            $this_args[] = $arg['arg'];
                        }
                        $this_formatted_args = [];
                        foreach ( $this_args as $arg ) {
                            $this_formatted_args[$arg] = true;
                        }
                        $new_kses[$tag['title']] = $this_formatted_args;
                    }
                } else {
                    $this_args = [];
                    foreach ( $tag['args'] as $arg ) {
                        $this_args[] = $arg['arg'];
                    }
                    $this_formatted_args = [];
                    foreach ( $this_args as $arg ) {
                        $this_formatted_args[$arg] = true;
                    }
                    $new_kses[$tag['title']] = $this_formatted_args;
                }
            }
        }
        return $new_kses;

    }

    /**
     * Sanitize string ready to be input to the database
     *
     * @since  1.0.0
     * @var    string   $value          The value to be sanitized
     * @var    string   $sanitize_as    code, text or false
     * @return string                   The sanitized string
     */
    private function get_sanitized_string( $value, $sanitize_as ) {

        switch ( $sanitize_as ) {
            case 'code':
                global $allowedposttags;
                $allowed_tags = $this->get_recursively_merged_array( $this->get_kses_extra_tags(), $allowedposttags );
                $sanitized_string = wp_kses( $value, $allowed_tags );

                break;
            case 'text':
                $sanitized_string = sanitize_text_field( $value );

                break;
            default:
                // No sanitization
                $sanitized_string = $value;

                break;
        }

        return $sanitized_string;

    }

    /**
     * Create an array of $sanitize_as values
     *
     * @since   1.0.0
     * @var     string      $sanitize_as    String to add to each array item
     * @var     integer     $count          Number of iterations to do
     * @return  array                       Array of sanitize_as strings
     */
    private function get_sanitize_as_array($sanitize_as, $count) {

        $return_arr = array();
        for ( $i = 0; $i < $count; $i++ ) {
            $return_arr[] = $sanitize_as;
        }

        return $return_arr;

    }

    /**
     * Iterate through each array item to sanitize
     * @since   1.0.0
     * @var     string     $item            The array item
     * @var     string     $sanitize_as     Is code
     * @return str
     */
    private function get_sanitized_array( $item, $sanitize_as ) {

        return $this->get_sanitized_string( $item, $sanitize_as );

    }

    /**
     * Saves the content of the meta box
     *
     * @since  1.0.0
     * @var    integer  $post_id        The ID of the current post
     * @var    string   $post_type      Name of the post type
     * @var    string   $field          Name of the field to save
     * @var    string   $meta_name      Meta name of the field
     * @var    string   $sanitize_as    Input type for sanitization
     */
    public function save_meta_box( $post_id, $post_type, $field, $meta_name = '', $sanitize_as = 'text' ) {

        $meta_root = 'd24nc_metabox_' . $post_type . '_';
        if ( is_array( $field ) ) {
            // Provide a default of 'repeater' only if meta name hasn't been passed but it HAS multiple fields
            $meta_name = $meta_name == '' ? 'repeater' : $meta_name;
            $meta = $meta_root . $meta_name;
            $post_meta = '_' . $post_type . '_' . $meta_name;
        } else {
            $meta = $meta_root . $field;
            $post_meta = '_' . $post_type . '_' . $field;
        }

        $nonce_name = $meta . '_box';
        $nonce = $nonce_name . '_nonce';
        $screen = get_current_screen();

        if ( $post_type !== $screen->post_type ) {
            return $post_id;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if ( !isset( $_POST['post_type'] ) ) {
            return $post_id;
        }

        if ( $post_type !== $_POST['post_type'] ) {
            return $post_id;
        }

        if ( !current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }

        // Check the nonce field
        if ( !isset( $_POST[$nonce] ) ) {
            return $post_id;
        }

        if ( !wp_verify_nonce( $_POST[$nonce], $nonce_name ) ) {
            return $post_id;
        }

        if ( is_array( $field ) ) {
            $return_val = array();

            foreach ( $field as $field_item ) {
                $count = count( $_POST[$meta_root . $field_item] );

                // Loop through each of the repeatable items, adding its data to the array
                for ( $i = 0; $i < $count; $i++ ) {

                    // Don't save if empty
                    if ( $_POST[$meta_root . $field_item][$i] != '' ) {

                        $data = isset( $_POST['d24nc_metabox_' . $post_type . '_' . $field_item][$i] ) ? $_POST['d24nc_metabox_' . $post_type . '_' . $field_item][$i] : '';
                        // Sanitize and return the data

                        // When $field is an array, each field item can have a different sanitization label
                        // Here we check for that
                        if ( is_array( $sanitize_as ) ) {
                            foreach ( $sanitize_as as $key => $value ) {
                                if ( $field_item === $key ) {
                                    // If on this iteration, the $field_item is the same as what has been listed in the sanitization array,
                                    // then use the passed value
                                    $return_val[$i][$meta_root . $field_item] = $this->get_sanitized_string( $data, $value );
                                } else {
                                    // Otherwise use the default of text
                                    $return_val[$i][$meta_root . $field_item] = $this->get_sanitized_string( $data, 'text' );
                                }
                            }
                        } else {
                            $return_val[$i][$meta_root . $field_item] = $this->get_sanitized_string( $data, $sanitize_as );
                        }
                    }
                }
            }

            // Update the meta field
            update_post_meta( $post_id, '_' . $post_type . '_' . $meta_name, $return_val );

        } else {
            // Check all the post and custom post values
            if ( isset( $_POST[$meta] ) ) {
                $count = count( $_POST[$meta] );
                // Create an array with the filled in values for sanitize_as
                $sanitize_as_array = $this->get_sanitize_as_array( $sanitize_as, $count );

                if ( $count > 1 ) {
                    for ( $i = 0; $i < $count; $i++ ) {
                        $return_val[$i][$meta] = $this->get_sanitized_string( $_POST[$meta][$i], $sanitize_as );
                    }
                } else { // only holds a single value
                    // Sanitize the single value (could still be a single value array)
                    if ( is_array( $_POST[$meta] ) ) { // for example - a single checkbox checked
                        $return_val = array_map( array( $this, 'get_sanitized_array' ), $_POST[$meta], $sanitize_as_array );
                    } else {
                        $return_val = $this->get_sanitized_string( $_POST[$meta], $sanitize_as );
                    }
                }

            } else {
                foreach( $_POST as $key => $value ) {
                    if ( strpos( $key, 'd24nc_metabox_' . $field . '_' ) === 0 ) {
                        $count = count($_POST[$key]);
                        // Loop through each of the items, adding its data to the array
                        for ( $i = 0; $i < $count; $i++ ) {
                            // Pass to get_sanitized_array(), if contains code the function will esc_html otherwise will sanitize_textarea
                            $sanitized_val = $code ? array_map( array( $this, 'get_sanitized_array' ), $_POST[$key], $code = array( true ) ) : array_map( array( $this, 'get_sanitized_array' ), $_POST[$key], $code = array( false ) );
                            $return_val[$key] = $sanitized_val;
                        }
                    }
                }

                if ( !isset( $return_val ) ) {
                    $return_val = 0;
                }
            }

            // Update the meta field
            if ( isset( $return_val ) ) {
                update_post_meta( $post_id, '_' . $post_type . '_' . $field, $return_val );
            }
        }
    }


    /**
     * Save meta boxes on post save
     *
     * @since   1.0.0
     * @var     object  $post   The current post object
     */
    public function save_meta_boxes($post) {

        /**
         * Subscribers
         */
        $this->save_meta_box( $post, 'd24nc_subscriber', 'name' );
        $this->save_meta_box( $post, 'd24nc_subscriber', 'extra' );
        $this->save_meta_box( $post, 'd24nc_subscriber', 'hash' );

        /**
         * Templates
         */
        $this->save_meta_box( $post, 'd24nc_template', 'base-html', '', 'code' );
        $this->save_meta_box( $post, 'd24nc_template', 'post-html', '', 'code' );
        $this->save_meta_box( $post, 'd24nc_template', array(
            'special-name', 'special-body', 'special-code', 'hidden'
            ), '', $sanitize_as = array('special-body' => 'code')
        );

        /**
         * Campaigns
         */
        $this->save_meta_box( $post, 'd24nc_campaign', 'description' );
        $this->save_meta_box( $post, 'd24nc_campaign', 'template-select' );
        $this->save_meta_box( $post, 'd24nc_campaign', 'subscriber-list-check' );
        $this->save_meta_box( $post, 'd24nc_campaign', 'builder', '', 'code' );
        $this->save_meta_box( $post, 'd24nc_campaign', 'message-subject' );
        $this->save_meta_box( $post, 'd24nc_campaign', 'message-from', '', 'code' );
        $this->save_meta_box( $post, 'd24nc_campaign', array('test-send-addresses'), 'test-send', 'code' );

    }

    /**
     * Used to hide certain metaboxes by filter: default_hidden_meta_boxes
     *
     * @since   1.0.0
     * @var     array   $hidden     The currently hidden metaboxes
     * @var     object  $screen     The object containing information about the current screen
     * @return  array               An array of all hidden metaboxes
     */
    public function hide_meta_boxes( $hidden, $screen, $use_defaults ) {

        switch ( $screen->id ) {
            case 'd24nc_subscriber':
                $hidden[] = 'd24nc_metabox_subscriber_hash';

                break;
        }
        return $hidden;

    }

}