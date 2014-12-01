<?php

/**
 * Processes the send functionality.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 */

/**
 * Processes the send functionality.
 *
 *
 * @todo       $campaign_id used in most methods, use as a class var and update
 *             inner-method references
 * @package    d24_newsletter-campaign
 * @subpackage d24_newsletter-campaign/admin
 * @author     Your Name <email@example.com>
 */
class D24nc_Send {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin
     */
    private $plugin_name;

    /**
     * An instance of Shortcodes class.
     *
     * @since    1.0.0
     * @access   private
     * @var      Shortcodes     $shortcodes The instance of Shortcodes class
     */
    private $shortcodes;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @var     string      $plugin_name        The name of this plugin
     */
    public function __construct( $plugin_name ) {

        $this->plugin_name = $plugin_name;
        $this->shortcodes = $shortcodes;
//$this->build_email($_POST['post_ID']);
    }

    /**
     * Returns comma-separated html of subscriber address links
     * @since   1.0.0
     * @param   array       $addresses      An array of email addresses ( In the form of: Array([0]=>Array('id'=>'address'), [1]=>Array('id'=>'address')) )
     * @param   string      $type           ´valid´, ´invalid´ etc.
     * @param   boolean     $delete         Output delete link for subscriber address
     * @return  string                      HTML of subscriber edit links or delete links
     */
    private function get_subscriber_list_text( $addresses, $type, $delete = false ) {

        $subscriber_html_link = '';
        $i = 0;
        $count = count( $addresses[$type] );
        foreach ( $addresses[$type] as $addressess ) {
            foreach ( $addressess as $id => $address ) {
                $subscriber_html_link .= '<a href="' . get_edit_post_link( $id ) . '" target="_blank">' . $address . '</a>';
                if ( $delete === true ) {
                    $subscriber_html_link .= ' <a href="' . get_delete_post_link( $id ) . '" target="_blank">[' . __( 'delete', $this->plugin_name ) . ']</a>';
                }
                if ( $count - 2 === $i ) {
                    $subscriber_html_link .= ' ' . __( 'and', $this->plugin_name ) . ' ';
                } else if ($count - 1 !== $i) {
                    $subscriber_html_link .= ', ';
                }
            }
            $i++;
        }
        return $subscriber_html_link;

    }

    /**
     * Calculate the current iteration through template array
     *
     * @since   1.0.0
     * @param   array       $template_exploded      Array of template sub parts
     * @param   integer     $prev_iteration         The previous iteration count
     * @return  integer                             Current iteration
     */
    private function get_template_layout_part( $template_exploded, $prev_iteration ) {

        $count = count( $template_exploded );
        if ( $prev_iteration + 1 >= $count ) {
            $current_iteration = 0;
        } else {
            $current_iteration = $prev_iteration + 1;
        }
        return $current_iteration;

    }

    /**
     * Merges content with a single template
     *
     * @since   1.0.0
     * @param   array       $posts_arr      An array of post contents
     * @param   string      $template       The template to merge content into
     * @return  string                      Content formatted to the template
     */
    private function merge_content_template( $posts_arr, $template ) {

        // Set up an array to add posts content to
        $post_output_arr = array();
        // Fetch the shortcode divider text
        $options = get_option( 'd24nc_settings' );
        $divider_text = $options['d24nc_settings_field_divider'];
        // If template is split, get the parts (i.e multilayout template)
        $template_exploded = explode( '[' . $divider_text . ']', $template );
        if ( $template_exploded !== array( $template ) ) {
            $multilayout_template = true;
            // An array of template parts has been created
            // Set the template to the first part
            $template = $template_exploded[0];
            $layout_part_count = 0;
        }
        // Generate the output for each post
        $i = 0;
        foreach ( $posts_arr as $post_item ) {
            // Get the post object so we can get the content
            $post_object = get_post( $post_item );
            // Perform replacement
            $this->shortcodes->set_post_object( $post_object );
            // If mulitlayout is used and we're not on the first iteration
            if ( $multilayout_template && $i !== 0 ) {
                // Get the correct current layout part
                $layout_part_count = $this->get_template_layout_part( $template_exploded, $layout_part_count );
                // Set the template to the layout part
                $template = $template_exploded[$layout_part_count];
            }
            $post_output_arr[] = $this->shortcodes->do_shortcodes( $template );
            $i++;
        }
        // If doesn't end on a completed layout, output empty template parts
        if ( $multilayout_template && $i % count( $template_exploded ) !== 0 ) {
            $required_extra = count( $template_exploded ) - ( $i % count( $template_exploded ) );
            // Loop for each extra need empty template part
            for ( $i = 0; $i < $required_extra; $i++ ) {
                // Get the correct current layout part
                $layout_part_count = $this->get_template_layout_part( $template_exploded, $layout_part_count );
                // Set the template to the layout part
                $template = $template_exploded[$layout_part_count];
                // Output the empty template part removing any shortcodes
                $post_output_arr[] = strip_shortcodes( $template );
            }
        }
        // Join together all the posts
        $post_output = implode( '', $post_output_arr );
        return $post_output;

    }

    /**
     * Controls the merging of all the contents with all the templates and returns the result
     *
     * @since   1.0.0
     * @param   integer     $campaign_id    The post id of the current campaign
     * @param   array       $template_parts An array of template parts
     * @return  string                      A string of the entire email content
     */
    private function build_email( $campaign_id, $template_parts ) {

        // Get posts attached to this campaign
        $posts = get_post_meta( $campaign_id, '_d24nc_campaign_builder', true );
        // Get regular (non-custom) posts
        $posts_reg = $posts['d24nc_metabox_builder_post'];
        // Merge the regular posts with the template for regular posts
        $posts_reg_output = $this->merge_content_template( $posts_reg, $template_parts['post'] );
        // Loop through $posts array to get the special posts
        foreach ( $posts as $posts_special => $post_ids ) {
            // Ignore regular posts - everything else is a special post
            if ( $posts_special !== 'd24nc_metabox_builder_post' ) {
                // Fetch the hashed code from the end of the key
                $template_key_exploded = explode( '_', $posts_special );
                $template_key_code = array_pop( $template_key_exploded );
                foreach ( $template_parts['special'] as $template_special ) {
                    // Make sure the template is correct
                    if ( $template_special['d24nc_metabox_template_hidden'] === $template_key_code ) {
                        $shortcode_special = $template_special['d24nc_metabox_template_special-code'];
                        $posts_special_output[$shortcode_special] = $this->merge_content_template( $post_ids, $template_special['d24nc_metabox_template_special-body'] );
                        // Add posts content for shortcodes
                        $this->shortcodes->set_posts_content( $template_special['d24nc_metabox_template_special-body'] );
                        $this->shortcodes->add_shortcodes();
                        // Add shortcode for these special posts
                        $this->shortcodes->add_shortcode( $shortcode_special, $posts_special_output[$shortcode_special] );
                    }
                }
            }
        }
        // Place the regular posts in the main template
        $this->shortcodes->set_posts_content( $posts_reg_output );
        $this->shortcodes->add_shortcodes();
        $email_output = $this->shortcodes->do_shortcodes( $template_parts['base'] );
        return $email_output;

    }

    /**
     * Get the email headers string saved in the meta box
     *
     * @since   1.0.0
     * @access  private
     * @var     integer     $campaign_id    The post id of the current campaign
     * @var     string      $type           The type of header to return, ´subject´ / ´from´ etc.
     * @return  string                      The formatted header string
     */
    private function get_headers( $campaign_id, $type ) {
/* @todo: add the headers meta box */
        $header_str = get_post_meta( $campaign_id, '_d24nc_campaign_message-' . $type, true );
        if ( $type === 'from' ) {
            $header_str = 'From:' . html_entity_decode( $header_str );
        }
        return $header_str;

    }

    /**
     * Get the template attached to current campaign
     *
     * @since   1.0.0
     * @access  private
     * @var     integer     $campaign_id    The post id of the current campaign
     * @return                              An array of template parts
     */
    private function get_template( $campaign_id ) {

        // Set up an array to store the parts of the template in
        $template_parts = array();
        // Fetch the template ID from the meta data
        $template_id = get_post_meta( $campaign_id, '_d24nc_campaign_template-select', true );
        // Fetch the base html
        $base_html_meta = get_post_meta( $template_id, '_d24nc_template_base-html', true );
        if ( !empty( $base_html_meta ) ) {
            $template_parts['base'] = html_entity_decode( $base_html_meta );
        }
        // Fetch the post html
        $post_html_meta = get_post_meta( $template_id, '_d24nc_template_post-html', true );
        if ( !empty( $post_html_meta ) ) {
            $template_parts['post'] = html_entity_decode( $post_html_meta );
        }
        // Fetch any special htmls
        $special_html = get_post_meta( $template_id, '_d24nc_template_repeater', true );
        // Only send the special html back if there is at least name saved
        if ( isset( $special_html[0]['d24nc_metabox_d24nc_template_special-name'] ) ) {
            // One or more special posts saved
            $special_templates = get_post_meta( $template_id, '_d24nc_template_repeater', true );
            // Decode the html entities
            $i = 0;
            foreach ( $special_templates as $template_item ) {
                $special_templates[$i]['d24nc_metabox_d24nc_template_special-body'] = html_entity_decode($template_item['d24nc_metabox_d24nc_template_special-body']);
                $i++;
            }
            $template_parts['special'] = $special_templates;
        }
        return $template_parts;

    }

    /**
     * Return an array of email addresses from selected subscriber lists
     *
     * @since   1.0.0
     * @var     integer     $campaign_id    The post id of the current campaign
     * @return  array                       An array of email addresses
     */
    private function get_addresses( $campaign_id ) {

        // Get the ids of all selected subscriber lists
        $subscriber_lists_meta = get_post_meta( $campaign_id, '_campaign_subscriber-list-check', true );
        // Create an array to store subscriber lists ids
        $subscriber_lists_ids = array();
        // Flatten the array to create an array of the values
        array_walk_recursive( $subscriber_lists_meta, function ( $current ) use ( &$subscriber_lists_ids ) {
            $subscriber_lists_ids[] = $current;
        } );
        // Fetch all the (subscriber) posts that belong to the selected subscriber list(s)
        $send_campaign_subscriber_posts_args = array(
            'posts_per_page'    =>  -1,
            'orderby'           => 'title',
            'post_type'         => 'd24nc_subscriber',
            'tax_query' => array(
                array(
                    'taxonomy' => 'd24nc_subscriber_list',
                    'terms' => $subscriber_lists_ids
                )
            )
        );
        $subscriber_posts = get_posts( $send_campaign_subscriber_posts_args );
        // Set an array to store email addresses
        $subcriber_emails = array();
        // Set up an incremental count
        $i = 0;
        // Loop through the posts to generate an array of emails
        foreach ( $subscriber_posts as $subscriber ) {
            // Perform a check to make sure the address has not already been added
            if ( !in_array( $subscriber->post_title, $subcriber_emails['valid'] ) ) {
                // Check that it is a valid email address
                if ( is_email( $subscriber->post_title ) ) {
                    // Everything is fine with address, add it to array along with other data to be possibly put into individual messages
                    $subcriber_emails['valid'][$i]['id'] = $subscriber->ID;
                    $subcriber_emails['valid'][$i]['email'] = $subscriber->post_title;
                    $subcriber_emails['valid'][$i]['name'] = get_post_meta( $subscriber->ID, '_subscriber_name', true );
                    $subcriber_emails['valid'][$i]['extra'] = get_post_meta( $subscriber->ID, '_subscriber_extra', true );
                    $subcriber_emails['valid'][$i]['hash'] = get_post_meta( $subscriber->ID, '_subscriber_hash', true );
                } else {
                    // Not valid, add to invalid array
                    $subcriber_emails['invalid'][] = array( $subscriber->ID => $subscriber->post_title );
                }
            } else {
                // A duplicate, add to duplicate array
                $subcriber_emails['duplicate'][] = array( $subscriber->ID => $subscriber->post_title );
            }
            $i++;
        }
        return $subcriber_emails;

    }

    /**
     * Return an array of email addresses from the test addresses text input
     *
     * @since   1.0.0
     * @access  private
     * @var     integer     $campaign_id    The post id of the current campaign
     * @return  array                       An array of email addresses in sub arrays of ´valid´ or ´invalid´
     */
    private function get_test_addresses( $campaign_id ) {

        $test_addresses_text = get_post_meta( $campaign_id, '_d24nc_campaign_test-send', true );
        if ( empty( $test_addresses_text ) ) {
            // Return an empty array
            return array();
        }
        // Decode the test address data
        $test_addresses_decoded = html_entity_decode( $test_addresses_text[0]['d24nc_metabox_d24nc_campaign_test-send-addresses'] );
        // Delimiters:  comma newline, space newline, comma space, comma, space, newline,
        $test_addresses_arr = preg_split( "/(,\\n| \\n|, |,| |\\n)/", $test_addresses_decoded );
        // Create an array to store just the email addresses
        $subcriber_emails = array();
        // Loop through the addresses, adding to the appropriate array
        foreach ( $test_addresses_arr as $test_address ) {
            if ( is_email( $test_address ) ) {
                $subcriber_emails['valid'][] = $test_address;
            } else {
                $subcriber_emails['invalid'][] = $test_address;
            }
        }
        return $subcriber_emails;

    }

    /**
     * Handles the functionality of the send
     *
     * @todo    Don't join translatable sentence parts, see end of method
     * @since   1.0.0
     */
    public function send_campaign() {

        // Exit if a send request hasn't been sent
        if ( !isset( $_POST['d4nc_campaign_confirmation_true'] ) && !isset( $_POST['d24nc_metabox_d24nc_campaign_test-send-btn'] ) ) {
            return;
        }
        $send_test = isset( $_POST['d24nc_metabox_d24nc_campaign_test-send-btn'] ) ? true : false;
        // Get the current campagin id
        $campaign_id = $_POST['post_ID'];
        // Set up an array to hold return messages
        $campaign_message = array();
        // Get the list of email addresses
        if ( $send_test ) {
            $send_type = __(  'test email', $this->plugin_name );
            // Get the test email addresses
            $addresses = $this->get_test_addresses( $campaign_id );
        } else {
            $send_type = __( 'campaign', $this->plugin_name );
            // Get the campaign email addresses
            $addresses = $this->get_addresses( $campaign_id );
        }
        // If no addresses found, save a message to $campaign_message[]
        if ( empty( $addresses ) || empty( $addresses['valid'] ) ) {
            $campaign_message[] = sprintf( __( 'Couldn\'t find any valid addresses, %s not sent.', $this->plugin_name ), $send_type );
        }
        // Get the data from the template
        $template = $this->get_template( $campaign_id );
        // If no template found, save a message to $campaign_message[]
        if ( empty( $template['base'] ) || empty( $template['post'] ) ) {
            $campaign_message[] = sprintf( __( 'Couldn\'t find valid data in the selected template, %s not sent.', $this->plugin_name ), $send_type );
        }
        // Proceed only if no errors have been logged
        if ( empty( $campaign_message ) ) {
            // Build email
            $email_subject = $this->get_headers( $campaign_id, 'subject' );
            $email_from = $this->get_headers( $campaign_id, 'from' );
            $email_content = $this->build_email( $campaign_id, $template );
            // If $email_content does not return, save a message to $campaign_message[]
            // and set the post meta that campaign has not been sent
            if ( empty( $email_content ) ) {
                $campaign_message[] = sprintf( __( 'Something went wrong in using your template, %s not sent.', $this->plugin_name ), $send_type );
                update_post_meta( $campaign_id, 'mail_sent', array( 'no', $campaign_message ) );
            } else {
                $mail_success = $this->send_email($addresses['valid'], $email_subject, $email_from, $email_content);
                // If there were duplicate or invalid addresses display messages
                if ( !empty( $addresses['invalid'] ) ) {
                    // Get a formatted list of links to invalid addresses
                    if ( !$send_test ) {
                        $invalid_addresses = $this->get_subscriber_list_text( $addresses, 'invalid', true );
                    } else {
                        // Wrap each address in <strong> tag
                        foreach( $addresses['invalid'] as $key => $invalid_address ) {
                            $addresses['invalid'][$key] = '<strong>' . $invalid_address . '</strong>';
                        }
                        $invalid_addresses = implode(', ', $addresses['invalid']);
                    }
                    $campaign_message[] = __( 'Some email addresses were invalid and could not be sent: ', $this->plugin_name ) . $invalid_addresses . '.';
                }
                if ( !empty( $addresses['duplicate'] ) ) {
                    // Get a formatted list of links to duplicate addresses
                    $duplicate_addresses = $this->get_subscriber_list_text( $addresses, 'duplicate' );
                    $campaign_message[] = __( 'Some email addresses were duplicates and were not sent: ', $this->plugin_name ) . $duplicate_addresses . '.';
                }
                if ( $mail_success[0] === 'yes' ) { // all messages sent successfully
                    $campaign_message[] = sprintf( _n( '%s has been successfully sent to %d address.', '%s has been successfully sent to %d addresses.', $mail_success[2], $this->plugin_name ), ucfirst($send_type), $mail_success[2] );
                    // Set the mail_sent meta
                    update_post_meta( $campaign_id, 'mail_sent', array( 'yes', $campaign_message ) );
                } else {
                    $count_tried = count( $addresses['valid']['email'] );
                    $count_failed = count( $mail_success[1] );
                    $count_success = $count_tried - $count_failed;
                    if ( $count_tried === $count_failed ) {
                        $campaign_message[] = __( 'Campaign sending failed - all of the messages failed to send.', $this->plugin_name );
                        update_post_meta( $campaign_id, 'mail_sent', array( 'no', $campaign_message ) );
                    } else { // Some messages failed to send
                        $campaign_message[] = sprintf( _n( 'Out of %d message', 'Out of %d messages', $count_tried, $this->plugin_name), $count_tried) . ', ' . sprintf( _n('%d message was sent successfully', '%d messages were sent successfully', $count_success, $this->plugin_name ), $count_success ) . ' ' . sprintf( _n( 'and %d message failed to send.', '%d messages failed to send.', $count_failed, $this->plugin_name ), $count_failed );
                        update_post_meta( $campaign_id, 'mail_sent', array( 'yes', $campaign_message ) );
                    }
                }
            }
        } else {
            // Errors have been logged before trying to send
            update_post_meta( $campaign_id, 'mail_sent', array( 'no', $campaign_message ) );
        }

    }

}