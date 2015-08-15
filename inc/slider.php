<?php
/**
 * Funcionality of Full Page Slider.
 *
 * @since gp 1.0
 */

/**
 * Adds a new tab in the media browser.
 */
function gp_slider_tab( $tabs ) {

    global $wpdb;

    $post_id = it_get_post_id();

    $tabs['gp_slider'] = __( 'Slider', 'gp' );

    return $tabs;

}
add_filter( 'media_upload_tabs', 'gp_slider_tab' );


/**
 * Retrievs every child slide of a post and looks for
 * empty order values. If finds one, then sets it to the
 * highest value available.
 *
 * Should be run every time new slide is created.
 */
function gp_slider_set_order( $post_id ) {

    global $wpdb;

    $args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'post_parent'    => $post_id
    );

    $attachments = get_children( $args );

    $ids = false;

    /**
     * Checks if any of the attachments has no slide_order set.
     * If so - sets it.
     */
    foreach ( $attachments as $slide ) {

        $order = get_post_meta( $slide->ID, 'slide_order', true );

        if ( empty( $order ) ) {

            if ( $ids === false ) {
                foreach ( $attachments as $temp_slide ) {
                    $ids[] = $temp_slide->ID;
                }
                $ids_sql = join( ',', $ids );
            }

            // select the highest existing order
            $current_max_order = $wpdb->get_var( "SELECT MAX(meta_value) AS max_value FROM $wpdb->postmeta
                                                  WHERE meta_key = 'slide_order' AND post_id IN ($ids_sql)" );

            $current_max_order = ! is_numeric( $current_max_order ) ? 0 : $current_max_order;

            update_post_meta( $slide->ID, 'slide_order', $current_max_order + 1 );

        }

    }

}


/**
 * Returns all slides of a post.
 */
function gp_slider_get_slides( $post_id, $args = array() ) {

    $defaults = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'any',
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'slide_order',
        'order'          => 'ASC',
        'posts_per_page' => -1,
        'post_parent'    => $post_id
    );

    $r = wp_parse_args( $args, $defaults );

    return get_posts( $r );

}


/**
 * Returns only published (meta key slide_published=true) slides.
 */
function gp_slider_get_published_slides( $post_id ) {

    $args = array(
        'meta_query'     => array(
            array(
                'key'       => 'slide_published',
                'value'     => '1'
            )
        )
    );

    return gp_slider_get_slides( $post_id, $args );
}


/**
 * Gets slide custom data.
 */
function gp_slider_get_slide_data( $slide_id ) {

    $image = wp_get_attachment_image_src( $slide_id, 'gp-max' );

    if ( is_array( $image ) ) {
        $image = $image[0];
    } else {
        $image = '';
    }

    $data = get_post_meta( $slide_id, 'slide_info', true );

    $publish = get_post_meta( $slide_id, 'slide_published', true );
    if ( ! $publish ) {
        $publish = 0;
    }

    return array(
            'image'               => $image,
            'title'               => it_get_key_value( $data, 'slide_title' ),
            'subtitle'            => it_get_key_value( $data, 'slide_subtitle' ),
            'description'         => it_get_key_value( $data, 'slide_description' ),
            'background_position' => it_get_key_value( $data, 'slide_background_position', 'center center' ),
            'info_box_position'   => it_get_key_value( $data, 'slide_info_box_position', 'center' ),
            'info_box_left'       => it_get_key_value( $data, 'slide_info_box_left' ),
            'info_box_top'        => it_get_key_value( $data, 'slide_info_box_top' ),
            'info_box_text_color' => it_get_key_value( $data, 'slide_text_color', 'black' ),
            'link_portfolio'      => it_get_key_value( $data, 'slide_link_portfolio' ),
            'link'                => it_get_key_value( $data, 'slide_link' ),
            'link_title'          => it_get_key_value( $data, 'slide_link_title' ),
            'dim_background'      => it_get_key_value( $data, 'slide_dim_background', '0' ),
            'publish'             => $publish
        );

}


/**
 * Set slide info box position.
 */
function gp_slider_set_position( $slides ) {

    global $post;

    /**
     * Check if we are in the mode of setting the Info Box position.
     */
    if ( isset( $_GET['set-infobox-position'] ) && isset( $_GET['slide'] ) ) {

        if ( is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
            $slide_id = $_GET['slide'];

            /**
             * Loop through all slides. Even if they are not published.
             */
            $slides = gp_slider_get_slides( $post->ID );
            foreach ( $slides as $k => $slide ) {
                if ( $slide->ID != $slide_id ) {
                    unset($slides[$k]);
                }
            }

            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'gp-wp-admin-slider', get_template_directory_uri() . '/js/wp-admin/slider.js', array('jquery-ui-draggable') );

            wp_enqueue_style( 'gp-wp-admin-slider-set-position', get_template_directory_uri() . '/css/wp-admin/slider-set-position.css' );

            ?>
            <div id="slider-set-position">
                <p>
                    <?php _e( 'Drag the box to place it anywhere on the page. Then click save.', 'gp'); ?>
                </p>
                <a href="#" class="save-position"><?php _e( 'Save & Close', 'gp'); ?></a>
            </div><?php

        }

    }

    return $slides;

}


/**
 * The content of Slider tab in media browser.
 */
function gp_slider_media_tab_content() {

    media_upload_header();

    // add scripts & styles that support the slider admin functionality
    wp_enqueue_style( 'gp-wp-admin-slider', get_template_directory_uri() . '/css/wp-admin/slider.css' );

    wp_enqueue_script( 'gp-wp-admin-slider', get_template_directory_uri() . '/js/wp-admin/slider.js', array( 'jquery-ui-sortable' ) );
    wp_enqueue_script( 'jquery-ui-sortable' );

    if ( ! isset($_GET['post_id']) ) {
        echo 'Post ID is not set.';
        return false;
    }

    $post_id = $_GET['post_id'];

    add_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );

    $portfolio_items = get_posts(array(
            'post_type'      => 'gp_portfolio',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order ID',
            'post_status'    => null,
            'order'          => 'DESC DESC'
        ));

    remove_filter( 'posts_orderby_request', 'gp_portfolio_orderby_filter' );

    $post = get_post( $post_id );

    if ( ! $post ) {
        echo 'Post not found.';
        return false;
    }

    gp_slider_set_order( $post_id );

    $slides = gp_slider_get_slides( $post_id );

    ?>
    <div id="gp-slider-tab" data-post-id="<?php echo esc_attr( $post_id ); ?>">

        <div class="slides-container"><?php

            if ( isset( $post ) && isset( $post->post_type ) && ( $post->post_type != 'page' ) ) : ?>

                <div class="gp-message">
                    <div class="gp-message-contents">
                        <?php _e( 'Slider only works with pages. Create a page with Full Page Slider template and visit this tab again.', 'gp' ); ?>
                    </div>
                </div><?php

            else:

                if ( ! it_is_template( $post_id, 'template-full-page-slider.php' ) ) : ?>

                    <div class="gp-message">
                        <div class="gp-message-contents">
                            <?php _e( 'Current page template is not set to Full Page Slider. To make Full Page Slider visible set the current page\'s template to Full Page Slider.', 'gp' ); ?>
                        </div>
                    </div><?php

                endif;


                if ( ! $slides ) : ?>

                    <div class="gp-message">
                        <div class="gp-message-contents">
                            <?php _e( 'No images were uploaded to current page. Please go to Insert Media tab and upload new images to create a Slider.', 'gp' ); ?>
                        </div>
                    </div><?php

                else:

                ?>
                    <table class="widefat" cellspacing="0" id="slides">
                        <thead>
                            <tr>
                                <th class="col-media"><?php _e( 'Media', 'gp' ); ?></th>
                                <th class="col-title"><?php _e( 'Title', 'gp' ); ?></th>
                                <th class="col-order"><?php _e( 'Order', 'gp' ); ?></th>
                                <th class="col-actions"><?php _e( 'Actions', 'gp' ); ?></th>
                            </tr>
                        </thead>
                        <tbody><?php

                            foreach ( $slides as $slide ) :

                                $id = $slide->ID;
                                $image = wp_get_attachment_image_src( $id, 'gp-slider-admin-thumbnail' );

                                $data = gp_slider_get_slide_data( $id );

                                ?>
                                <tr>
                                    <td>
                                        <?php echo wp_get_attachment_image( $id, 'gp-slider-admin-thumbnail' ); ?>
                                    </td>
                                    <td>
                                        <div class="wrap-slide-title<?php if ( empty($data['title']) ) echo ' slide-title-empty'; ?>">
                                            <i class="title-empty"><?php _e( 'Not set', 'gp' ); ?></i>
                                            <span class="slide-title"><?php echo $data['title']; ?></span>
                                        </div>
                                        <div class="wrap-slide-status <?php echo $data['publish'] ? 'slide-published' : 'slide-unpublished'; ?>">
                                            <span class="slide-status-published"><?php _e( 'Published', 'gp' ); ?></span>
                                            <span class="slide-status-unpublished"><?php _e( 'Unpublished', 'gp' ); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" value="<?php echo $id; ?>" class="slide-id" />
                                        <a href="#" class="slide-move"></a>
                                    </td>
                                    <td>
                                        <a href="#" class="show-toggle" data-hide="<?php _e( 'Hide details', 'gp' ); ?>" data-show="<?php _e( 'Show details', 'gp' ); ?>"><?php _e( 'Show Details', 'gp' ); ?></a>
                                        <div class="details" id="slide_<?php echo $id; ?>_details">
                                            <form enctype="multipart/form-data" method="post" action="/">
                                                <input type="hidden" name="security" value="<?php echo wp_create_nonce('gp-slider-form'); ?>" />
                                                <input type="hidden" name="action" value="gp_slider_slide_save" />
                                                <input type="hidden" name="slide_id" value="<?php echo $id; ?>" />
                                                <div class="field">
                                                    <label><?php _e( 'Title', 'gp' ); ?></label>
                                                    <input type="text" name="slide_title" value="<?php echo esc_attr( $data['title'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Subtitle', 'gp' ); ?></label>
                                                    <input type="text" name="slide_subtitle" value="<?php echo esc_attr( $data['subtitle'] ); ?>" class="input-text" />
                                                    <i><?php _e( 'eg. featured project', 'gp' ); ?></i>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Description', 'gp' ); ?></label>
                                                    <textarea name="slide_description"><?php echo $data['description']; ?></textarea>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Custom link', 'gp' ); ?></label>
                                                    <input type="text" name="slide_link" value="<?php echo esc_url( $data['link'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Link to portfolio', 'gp' ); ?></label><?php

                                                    $options = array( '' => '' );
                                                    foreach ( $portfolio_items as $item ) {
                                                        $options[ $item->ID ] = $item->post_title;
                                                    }

                                                    ?>
                                                    <select name="slide_link_portfolio">
                                                        <?php echo it_array_to_select_options( $options, $data['link_portfolio'] ); ?>
                                                    </select>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Link title', 'gp' ); ?></label>
                                                    <input type="text" name="slide_link_title" value="<?php echo esc_attr( $data['link_title'] ); ?>" class="input-text" />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Text color', 'gp' ); ?></label>
                                                    <select name="slide_text_color">
                                                        <option value="black"<?php echo $data['info_box_text_color'] == 'black' ? ' selected="selected"' : ''; ?>><?php _e( 'Black', 'gp' ); ?></option>
                                                        <option value="white"<?php echo $data['info_box_text_color'] == 'white' ? ' selected="selected"' : ''; ?>><?php _e( 'White', 'gp' ); ?></option>
                                                    </select>
                                                    <i><?php _e( 'Choose according to the picture brightness', 'gp' ); ?></i>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Info box position', 'gp' ); ?></label>
                                                    <?php

                                                        $link = get_permalink( $post_id );
                                                        $link = add_query_arg( array(
                                                                'set-infobox-position' => 1,
                                                                'slide' => $id
                                                            ), $link );

                                                        $info_box_position = $data['info_box_position'];
                                                        $position_custom_html = '';

                                                        if ( $info_box_position == 'custom' ) {
                                                            $position_custom_html = ' (' . $data['info_box_left'] . ' ' . $data['info_box_top'] . ')';
                                                        }

                                                    ?>
                                                    <select name="slide_info_box_position">
                                                        <option value="center"<?php echo $info_box_position == 'center' ? ' selected="selected"' : ''; ?>><?php _e( 'Center', 'gp' ); ?></option>
                                                        <option value="custom"<?php echo $info_box_position == 'custom' ? ' selected="selected"' : ''; ?> data-custom="<?php echo esc_attr( __( 'Custom', 'custom' ) ); ?>"><?php _e( 'Custom', 'custom' ); ?><?php echo $position_custom_html; ?></option>
                                                    </select>
                                                    <input type="hidden" value="<?php echo $data['info_box_left']; ?>" name="slide_info_box_left" />
                                                    <input type="hidden" value="<?php echo $data['info_box_top']; ?>" name="slide_info_box_top" />
                                                    <a href="<?php echo $link ?>" target="_blank" class="set-infobox-position"><?php _e( 'Set custom position', 'gp' ); ?></a>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Image scaling', 'gp' ); ?></label><?php

                                                    $options = array(
                                                            'top center'    => __( 'Crop Bottom', 'gp' ),
                                                            'center center' => __( 'Crop Top and Bottom', 'gp' ),
                                                            'bottom center' => __( 'Crop Top', 'gp' ),
                                                            'fit'           => __( 'Fit Image', 'gp' ),
                                                        );

                                                    ?>
                                                    <select name="slide_background_position">
                                                        <?php echo it_array_to_select_options( $options, $data['background_position'] ); ?>
                                                    </select>
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Dim Background', 'gp' ); ?></label>
                                                    <input type="checkbox" name="slide_dim_background" value="1"<?php echo $data['dim_background'] ? ' checked="checked"' : ''; ?> />
                                                </div>
                                                <div class="field">
                                                    <label><?php _e( 'Published', 'gp' ); ?></label>
                                                    <input type="checkbox" name="slide_published" value="1"<?php echo $data['publish'] ? ' checked="checked"' : ''; ?> />
                                                </div>
                                                <div class="field">
                                                    <label for="">&nbsp;</label>
                                                    <a href="#" class="button gp-save-slide"><?php _e( 'Save changes', 'gp' ); ?></a>
                                                    <span class="saving-status" data-saving="<?php _e( 'Saving...', 'gp' ); ?>" data-ok="<?php _e( 'Saved!', 'gp' ); ?>" data-failed="<?php _e( 'Failed', 'gp' ); ?>"></span>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr><?php

                            endforeach;

                            ?>
                        </tbody>
                    </table><?php

                endif;

            endif;

            ?>
            <form enctype="multipart/form-data" method="post" action="/" id="gp-slider-order-form">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('gp-slider-order-form'); ?>" />
                <input type="hidden" name="action" value="gp_slider_save_order" />
                <input type="hidden" name="order" value="" />
            </form>

        </div>

        <div class="gp-slider-sidebar"><?php

                $options = gp_slider_get_options( $post_id );
                $auto_slideshow = $options['slideshow'] ? ' checked="checked"' : '';

            ?>
            <form enctype="multipart/form-data" method="post" action="/" id="gp-slider-options-form">
                <input type="hidden" name="security" value="<?php echo wp_create_nonce('gp-slider-options-form'); ?>" />
                <input type="hidden" name="action" value="gp_slider_save_options" />
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />

                <h3><?php _e( 'Slider Options', 'gp' ); ?></h3>

                <div class="gp-slider-settings">

                    <label for="gp_slider_slideshow" class="setting">
                        <span><?php _e( 'Auto slideshow', 'gp' ); ?></span>
                        <input type="checkbox" name="gp_slider_slideshow" id="gp_slider_slideshow" value="1"<?php echo $auto_slideshow; ?> />
                    </label>

                    <label for="gp_slider_slideshow_interval" class="setting">
                        <span><?php _e( 'Slide change interval', 'gp' ); ?></span>
                        <input type="text" name="gp_slider_slideshow_interval" id="gp_slider_slideshow_interval" value="<?php echo $options['slideshow_interval']; ?>" />
                    </label>

                    <label for="gp_slider_slideshow_animation" class="setting">
                        <span><?php _e( 'Slideshow animation', 'gp' ); ?></span>
                        <?php

                            $animations = array(
                                    'slide' => 'Slide',
                                    'fade' => 'Fade',
                                    'fadeAndSlide' => 'Fade and Slide'
                                );

                        ?>
                        <select name="gp_slider_slideshow_animation" id="gp_slider_slideshow_animation">
                            <?php
                                echo it_array_to_select_options( $animations, $options['slideshow_animation'] );
                            ?>
                        </select>
                    </label>

                    <div class="setting setting-controls">
                        <span>&nbsp;</span>
                        <input type="submit" id="button-save-slider-settings" class="button" value="<?php echo esc_attr( __( 'Save', 'gp' ) ); ?>" />
                        <i class="status-saving"><?php echo esc_attr( __( 'Saving...', 'gp' ) ); ?></i>
                        <i class="status-saved"><?php echo esc_attr( __( 'Saved!', 'gp' ) ); ?></i>
                        <i class="status-error"><?php echo esc_attr( __( 'Error!', 'gp' ) ); ?></i>
                    </div>

                </div>
            </form>

        </div>

    </div><?php

}


/**
 * Use gp_slider_media_tab_content output as tab content.
 */
function gp_insert_slider_iframe() {

    return wp_iframe( 'gp_slider_media_tab_content' );

}
add_action( 'media_upload_gp_slider', 'gp_insert_slider_iframe' );


/**
 * Process AJAX save slide details request
 */
function gp_slider_slide_save() {

    check_ajax_referer( 'gp-slider-form', 'security' );

    $data = $_POST;
    unset( $data['security'], $data['action'] );

    if ( ! isset( $data['slide_id'] ) ) {
        die( '0' );
    }

    $post_id = $data['slide_id'];

    unset( $data['slide_id'] );

    $published = isset( $data['slide_published'] ) ? $data['slide_published'] : 0;

    update_post_meta( $post_id, 'slide_published', $published );
    update_post_meta( $post_id, 'slide_info', $data );

    die( '1' );

}
add_action( 'wp_ajax_gp_slider_slide_save', 'gp_slider_slide_save' );


/**
 * Process AJAX save slide order request
 */
function gp_slider_save_order() {

    check_ajax_referer( 'gp-slider-order-form', 'security' );

    $data = $_POST;
    unset( $data['security'], $data['action'] );

    $index = 1;

    if ( isset( $data['order'] ) && ! empty( $data['order'] ) ) {

        $order = explode( ',', $data['order'] );

        if ( is_array( $order ) ) {
            foreach ( $order as $slide_id ) {
                update_post_meta( $slide_id, 'slide_order', $index++ );
            }
        }

    }

    die( '1' );

}
add_action( 'wp_ajax_gp_slider_save_order', 'gp_slider_save_order' );


function gp_slider_get_options( $post_id ) {

    $options = array();

    $options['slideshow'] = get_post_meta( $post_id, 'gp_slider_slideshow', true );

    $options['slideshow_interval'] = get_post_meta( $post_id, 'gp_slider_slideshow_interval', true );
    if ( $options['slideshow_interval'] && !is_numeric( $options['slideshow_interval'] ) ) {
        $options['slideshow_interval'] = 7;
    }

    $options['slideshow_animation'] = get_post_meta( $post_id, 'gp_slider_slideshow_animation', true );
    if ( !in_array( $options['slideshow_animation'], array('fade', 'slide', 'fadeAndSlide') ) ) {
        $options['slideshow_animation'] = 'slide';
    }

    return $options;

}

/**
 * Save slider options AJAX request.
 */
function gp_slider_save_options() {

    check_ajax_referer( 'gp-slider-options-form', 'security' );

    if ( ! it_key_is_numeric( $_POST, 'post_id' ) ) {
        echo '1';
        die( 1 );
    }

    $post_id = $_POST['post_id'];

    $allowed_keys = array(
            'gp_slider_slideshow',
            'gp_slider_slideshow_interval',
            'gp_slider_slideshow_animation'
        );

    $gp_slider_slideshow = isset( $_POST['gp_slider_slideshow'] ) ? 1 : 0;
    update_post_meta( $post_id, 'gp_slider_slideshow', $gp_slider_slideshow );

    if ( it_key_is_numeric( $_POST, 'gp_slider_slideshow_interval' ) ) {
        update_post_meta( $post_id, 'gp_slider_slideshow_interval', $_POST['gp_slider_slideshow_interval'] );
    }

    if ( isset( $_POST['gp_slider_slideshow_animation'] ) ) {
        update_post_meta( $post_id, 'gp_slider_slideshow_animation', $_POST['gp_slider_slideshow_animation'] );
    }

    echo '0';
    die( 0 );

}
add_action( 'wp_ajax_gp_slider_save_options', 'gp_slider_save_options' );


function gp_slider_admin_init() {

    // image size to be used in admin area
    add_image_size( 'gp-slider-admin-thumbnail', 120, 90, true );

}
add_action( 'admin_init', 'gp_slider_admin_init' );


function gp_slider_init() {

    add_filter( 'gp_before_slider', 'gp_slider_set_position', 1, 1 );

}
add_action( 'init', 'gp_slider_init' );
