<div class="d24nc-metabox-builder">
    <div class="d24nc-metabox-builder__posts">
        <?php // Store the vals for builder in a var
        $meta_vals = get_post_meta( $post->ID, '_' . $post_type . '_builder', true );

        // We're going to get all the posts but we don't want to output posts here that
        // will be output later in the builder output boxes

        // Setup the exclude array to be used later
        $exclude_arr = [];

        // Get array of special post hidden ids
        $campaign_template_id = get_post_meta( $post->ID, '_d24nc_campaign_template-select', true );

        // If this is not a new campaign with anything saved
        if ( isset( $campaign_template_id ) && $campaign_template_id !== '' ) {

            $special_posts = get_post_meta( $campaign_template_id, '_d24nc_template_repeater', true );

            if ( $special_posts ) {
                // Put the special template ids in an array so we can match them with what's saved
                $special_ids = [];
                foreach ( $special_posts as $special_post ) {
                    $special_ids[] = $special_post['d24nc_metabox_template_hidden'];
                }
            }

            if ( $meta_vals ) {
                foreach ( $meta_vals as $meta_val_key => $value ) {

                    // Find the last part of the key (the hash)
                    $this_key = explode( '_', $meta_val_key );
                    $this_key_val = end( $this_key );

                    // If the hash appears in $special_ids, exclude it
                    if ( $this_key_val === 'post' || in_array( $this_key_val, $special_ids ) ) {
                        foreach ( $value as $val ) {
                            $exclude_arr[] = $val;
                        }
                    }
                }
            }
        }

        // Fetch the list of posts
        $builder_posts_args = array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'orderby'          => 'post_date',
            'order'            => 'DESC',
            'post_type'        => 'post',
            'post_status'      => 'publish',
            'exclude'          => $exclude_arr
        );
        $builder_posts = get_posts( $builder_posts_args );

        // Output the list of posts
        echo $this->get_builder_post_from_block( __( 'Posts', $this->plugin_name ), $builder_posts );

        // Check for custom posts
        $args = array(
           'public'   => true,
           '_builtin' => false // discard the builtin post types
        );

        $post_types = get_post_types( $args, 'objects' );
        // List all the Newsletter Campaign post types in an array so that we can discard those
        $d24nc_post_types = array( 'd24nc_campaign', 'd24nc_template', 'd24nc_subscriber' );

        // Loop through the user's post types removing any d24nc post types
        foreach ( $post_types as $key => $value ) {
            if ( array_search( $key, $d24nc_post_types ) !== false ) {
                unset( $post_types[$key] );
            }
        }

        if ( $post_types ) {
            // The user has some custom post types
            // Loop through each post type
            foreach ( $post_types as $key => $value ) {

                // Fetch the list of posts
                $builder_custom_posts_args = array(
                    'posts_per_page'   => -1,
                    'offset'           => 0,
                    'orderby'          => 'post_date',
                    'order'            => 'DESC',
                    'post_type'        => $key,
                    'post_status'      => 'publish',
                    'exclude'          => $exclude_arr
                );
                $builder_custom_posts = get_posts( $builder_custom_posts_args );

                // Output the list of posts
                echo get_builder_post_from_block( ucfirst( $key ), $builder_custom_posts );
            }
        }

        ?>
    </div>

    <?php // The area for regular posts to be dropped into ?>
    <h4 class="d24nc-metabox-builder__posts-title"><?php _e( 'Posts', $this->plugin_name ); ?></h4>
    <div class="d24nc-metabox-builder__output" style="background:lightgray;min-height:50px;" data-name="post">
        <?php if ( $meta_vals ) {
            foreach ( $meta_vals as $meta_val => $value ) {
                if ( $meta_val === 'd24nc_metabox_campaign_builder_post' ) {
                    foreach ( $value as $this_post ) {
                        $selected_post = get_post( $this_post );
                        echo $this->get_builder_post( $selected_post, 'post' );
                    }
                }
            }
        }?>
    </div>

    <?php // Get details for template associated with this campaign
    $campaign_template_id = get_post_meta( $post->ID, '_d24nc_campaign_template-select', true );
    $special_posts = get_post_meta( $campaign_template_id, '_d24nc_template_repeater', true );
    if ( isset( $special_posts ) && !empty( $special_posts ) ) {
        foreach ( $special_posts as $special_post ) {
            // Only output the special template block if the name field is there (hidden field will be there even for an empty template)
            if ( isset( $special_post['d24nc_metabox_template_special-name'] ) ) {
                // One or more special posts have been saved, output the special posts container ?>
                <h4 class="d24nc-metabox-builder__posts-title"><?php echo esc_html( $special_post['d24nc_metabox_template_special-name'] ); ?></h4>
                <?php // Store the special post hash id as data-name to be used for consistent saving ?>
                <div class="d24nc-metabox-builder__output" style="background:lightgray;min-height:50px;" data-name="<?php echo esc_attr( $special_post['d24nc_metabox_template_hidden'] ); ?>">
                    <?php if ( $meta_vals ) {
                        foreach ( $meta_vals as $meta_val => $value ) {
                            if ( $meta_val === 'd24nc_metabox_builder_' . $special_post['d24nc_metabox_template_hidden'] ) {
                                foreach ( $value as $this_post ) {
                                    $selected_post = get_post( $this_post );
                                    echo $this->get_builder_post( $selected_post, $special_post['d24nc_metabox_template_hidden'] );
                                }
                            }
                        }
                    }?>
                </div>
                <?php
            }
        }
    }
    ?>
</div>