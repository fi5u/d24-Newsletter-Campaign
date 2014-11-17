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
     * Remove the subscriber taxonomy ui box in subscriber edit page
     * to be added back in by add_subscriber_taxonomy below submit box
     */
    public function remove_subscriber_taxonomy() {
        remove_meta_box('subscriber_listdiv', 'subscriber', 'side');
    }

    /**
     * Add the subscriber taxonomy ui box in subscriber edit page below submit box
     */
    public function add_subscriber_taxonomy() {
        add_meta_box('subscriber_listdiv', __( 'Subscriber Lists', $this->plugin_name ), array($this, 'subscriber_taxonomy_ui'), 'subscriber', 'side', 'low');
    }

    /**
     * The output for the subscriber tax ui
     * Used to add back in to the edit below the submit box
     */
    public function subscriber_taxonomy_ui() {
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
}