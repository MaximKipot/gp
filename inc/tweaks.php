<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @since gp 1.0
 */


/**
 * Set excerpt length.
 */
function gp_excerpt_lenght( $length ) {
    return 50;
}
add_filter( 'excerpt_length', 'gp_excerpt_lenght', 1000 );


/**
 * Used to increase excerpt length on certain post types.
 */
function gp_increased_excerpt_lenght( $length ) {
    return 100;
}


function gp_continue_reading_link() {
    return '<div class="wrap-excerpt-more"><a class="excerpt-more" href="' . esc_url( get_permalink() ) . '">' . __( 'Continue reading', 'gp' ) . '</a></div>';
}


function gp_auto_excerpt_more( $more ) {
    return ' &hellip;';
}
add_filter( 'excerpt_more', 'gp_auto_excerpt_more' );


function gp_get_the_excerpt( $content ) {

    global $post;

    if ( ! is_attachment() && ( $post->post_type != 'gp_portfolio' ) ) {

        /**
         * Show continue reading only if content is not fully shown or there
         * is a n manually set excerpt.
         */
        if ( ( $post->post_content != $content ) || has_excerpt( $post->ID ) ) {

            $content .= gp_continue_reading_link();

        }

    }

    return $content;

}
add_filter( 'get_the_excerpt', 'gp_get_the_excerpt' );



/**
 * Modifies default [wp_caption] shortcode to remove
 * style="width: width+10" from output.
 */
function gp_image_caption( $foo, $attr, $content = null ) {

    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $attr));

    if ( 1 > (int) $width || empty($caption) )
        return $content;

    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';

    return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';

}
add_filter( 'img_caption_shortcode', 'gp_image_caption', 1, 3 );


function gp_add_image_link_class( $content ) {

    // find all links to images
    if ( preg_match_all( '/<a.*? href="(.*?)\.(png|jpg|jpeg|gif)">/i', $content, $matches ) ) {

        foreach ( $matches[0] as $match ) {

            if ( preg_match( '/class=".*?"/i', $match ) ) {
                $replacement = preg_replace( '/(<a.*? class=".*?)(".*?>)/', '$1 link-to-image$2', $match );
            } else {
                $replacement = preg_replace( '/(<a.*?)>/', '$1 class="link-to-image">', $match );
            }

            // replace them using links with classes
            $content = str_replace( $match, $replacement, $content );

        }

    }

    return $content;

}
add_filter( 'the_content', 'gp_add_image_link_class' );


/**
 * Filters that adds custom classes to comments paging navigation.
 */
function gp_comment_previous_page() {
    return ' class="button-minimal button-icon-right icon-right-open-big" ';
}
add_filter( 'previous_comments_link_attributes', 'gp_comment_previous_page' );


function gp_comment_next_page() {
    return ' class="button-minimal button-icon-left icon-left-open-big" ';
}
add_filter( 'next_comments_link_attributes', 'gp_comment_next_page' );


/**
 * Filters that adds a wrapping <span class="count" /> around item count in widgets.
 * Used for styling purposes.
 */
function gp_wp_list_categories_filter( $output ) {
    return preg_replace( '/\<\/a\>\s+?\((\d+)\)/', '</a><span class="count">[$1]</span>', $output );
}
add_filter( 'wp_list_categories', 'gp_wp_list_categories_filter' );


function gp_get_archives_link_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?\((\d+)\)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'get_archives_link', 'gp_get_archives_link_filter' );


function gp_wp_list_bookmarks_filter( $output ) {
    return preg_replace( '/\<\/a\>(&nbsp;)?(\s+)?(\d+)/', '</a><span class="count">[$3]</span>', $output );
}
add_filter( 'wp_list_bookmarks', 'gp_wp_list_bookmarks_filter' );

function remove_h1($post) {
    $content = $post->post_title;

    echo $content;

    $StartPos = strpos($content,"<h1>");
    $EndPos = strpos($content,"</h1>");

    echo '-'.$EndPos.'-';

    if ($StartPos && $EndPos) {
        $EndPos += 5; //remove <h1> tag aswell
        $len = $EndPos - $StartPos;

        $content = substr_replace($content, '', $StartPos, $len);
    }
    return $content;
}
