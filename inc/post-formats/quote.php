<?php
/**
 * Quote Post Format functionality.
 *
 * @since gp 1.0
 */


function gp_quote_get_data( $post_id ) {

    $quote        = get_post_meta( $post_id, 'gp_quote', true );
    $quote_author = get_post_meta( $post_id, 'gp_quote_author', true );
    $quote_color  = get_post_meta( $post_id, 'gp_quote_text_color', true );
    if ( !$quote_color ) {
        $quote_color = 'black';
    }

    return array(
            'quote' => $quote,
            'quote_author' => $quote_author,
            'quote_color' => $quote_color
        );

}

/**
 * Displays quote markup.
 *
 * @since gp 1.0
 */
function gp_quote( $post_id = null) {

    if ( null == $post_id ) {
        $post_id = get_the_ID();
        if ( !$post_id ) {
            return false;
        }
    }

    $data = gp_quote_get_data( $post_id );
    extract( $data );

    if ( !$quote ) {
        return false;
    }

    $wrapping_class = array( 'wrap-quote', 'resizable', 'quote-' . $quote_color );

    if ( is_single() ) {
        $wrapping_class[] = 'quote-single';
    }

    $wrapping_class = ' class="' . join( ' ', $wrapping_class ) . '"';

    if ( $quote ) : ?>
        <div<?php echo $wrapping_class; ?>>
            <blockquote class="js-vertical-center">
                <p><span class="lq">&ldquo;</span><?php echo $quote; ?></p>
                <?php if ( $quote_author && !empty($quote_author) ) : ?>
                    <footer>
                        <cite><?php echo $quote_author; ?></cite>
                    </footer><?php
                endif; ?>
            </blockquote>
        </div><?php
    endif;
}

/**
 * Meta box contents.
 */
function gp_quote_meta_box_contents() {
    global $post;

    if ( ! $post) {
        return;
    }

    $quote       = get_post_meta( $post->ID, 'gp_quote', true );
    $quote_color = get_post_meta( $post->ID, 'gp_quote_text_color', true );
    $quote_author= get_post_meta( $post->ID, 'gp_quote_author', true );

    ?>
    <p>
        <?php _e( 'Quotes are displayed above post content. If a post has a featured image set, then the quote will be overlayed on top of the image.', 'gp' ); ?>
    </p>
    <label for="gp_quote"><?php _e( 'Quote', 'gp' ); ?></label>
    <div class="gp-meta-field">
        <textarea name="gp_quote" class="full-width-textarea"><?php echo $quote; ?></textarea>
    </div>
    <div class="gp-meta-field">
        <label for="gp_quote_text_color"><?php _e( 'Text color', 'gp' ); ?></label>
        <select name="gp_quote_text_color">
            <option value="black"<?php echo 'black' === $quote_color ? ' selected="selected"' : ''; ?>><?php _e( 'Black', 'gp' ); ?></option>
            <option value="white"<?php echo 'white' === $quote_color ? ' selected="selected"' : ''; ?>><?php _e( 'White', 'gp' ); ?></option>
        </select>
    </div>
    <div class="gp-meta-field">
        <label for="gp_quote_author"><?php _e( 'Quote author', 'gp' ); ?></label>
        <input type="text" name="gp_quote_author" class="quote-author" value="<?php echo $quote_author; ?>">
    </div>

    <?php
}


/**
 * Save meta box.
 */
function gp_quote_meta_box_save( $post_id ) {

    if ( ! it_check_save_action( $post_id ) ) {
        return $post_id;
    }

    $quote = isset($_POST['gp_quote']) ? $_POST['gp_quote'] : '';
    update_post_meta( $post_id, 'gp_quote', $quote );

    $quote_color = isset($_POST['gp_quote_text_color']) ? $_POST['gp_quote_text_color'] : '';
    update_post_meta( $post_id, 'gp_quote_text_color', $quote_color );

    $quote_author = isset($_POST['gp_quote_author']) ? $_POST['gp_quote_author'] : '';
    update_post_meta( $post_id, 'gp_quote_author', $quote_author );

}
add_action( 'save_post', 'gp_quote_meta_box_save' );


/**
 * Add Meta Box in Page.
 */
function gp_quote_add_meta_box() {
    add_meta_box(
            'gp_quote_meta_box',
            __( 'Quote', 'gp' ),
            'gp_quote_meta_box_contents',
            'post',
            'normal'
        );
}

/**
 * Initialize Admin-Side Post Format.
 */
function gp_post_format_quote_admin_init() {

    add_action( 'add_meta_boxes', 'gp_quote_add_meta_box' );

}
add_action( 'admin_init', 'gp_post_format_quote_admin_init', 1 );


