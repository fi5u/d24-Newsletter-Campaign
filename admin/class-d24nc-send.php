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
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @var     string      $plugin_name        The name of this plugin
     */
    public function __construct( $plugin_name ) {

        $this->plugin_name = $plugin_name;

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
/* @todo: get_template() */
        $template = $this->get_template( $campaign_id );
        // If no template found, save a message to $campaign_message[]
        if ( empty( $template['base'] ) || empty( $template['post'] ) ) {
            $campaign_message[] = sprintf( __( 'Couldn\'t find valid data in the selected template, %s not sent.', $this->plugin_name ), $send_type );
        }
        // Proceed only if no errors have been logged
        if( empty( $campaign_message ) ) {
            // Build email
/* @todo: get_headers() */
            $email_subject = $this->get_headers( $campaign_id, 'subject' );
            $email_from = $this->get_headers( $campaign_id, 'from' );
/* @todo: build_email() */
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
/* @todo: get_subscriber_list_text() */
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