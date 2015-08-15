<?php
/**
 * Post Sidebar.
 *
 * @package gp
 * @since gp 1.0
 */

global $post;

?>
<!-- <div idi="starnge_sidebar" class="sidebar sidebar-post widget-area"> -->
<div id="subnavigation_holder" class="clearfix">
    <div id="subnavigation" class="clearfix">
    
    <h1>
        <?php
            the_title();
        ?>
    </h1>

    <?php


    /*
    * show related posts from the same parent category
    */
            //// Get the post ID
            // $id = get_the_ID(); 
            // $_orig_post = $post->ID;        

            // //get all the categories ( this function will return all categories for a post in an array)
            // $category= get_the_category( $id );
            // // echo "<pre>"; print_r($category); echo "</pre>";

            // if ($category->category_parent == 0) {

            //     //extract the first category from the array
            //     $catID = $category[count($category)-1]->cat_ID;

            //     //Assign the category ID into the query
            //     $verticalNavigationSwitcher = "category__in=$catID&orderby=ID&order=ASC";
            // }              


            // $result = new WP_Query($verticalNavigationSwitcher);

            // echo "<ul class='submenu'>";
            // while ($result->have_posts()) : $result->the_post(); 

            // if($_orig_post != $post->ID) {
            // ?>


             <!-- <li><a href='<?php the_permalink() ?>'><span><?php the_title(); ?></span></a></li> -->


            <?php 
            //         }
            //         endwhile; 
            //         wp_reset_postdata();
         
            // echo "</ul>";


    /**
     * Show attachment navigation if we are on
     * an attachment page and there is at least one sibling.
     */

    if ( is_attachment() ) :

        $prev = gp_get_previous_image_link( false );
        $next = gp_get_next_image_link( false );

        if ( $prev || $next ) : ?>

            <nav class="widget image-navigation"><?php
                if ( $prev ) : ?>
                    <a href="<?php echo esc_url( $prev ); ?>" class="button-minimal button-icon-left icon-left-open-big"><?php _e( 'Previous', 'gp' ); ?></a><?php
                endif;

                if ( $next ) : ?>
                    <a href="<?php echo esc_url( $next ); ?>" class="button-minimal button-icon-right icon-right-open-big"><?php _e( 'Next', 'gp' ); ?></a><?php
                endif; ?>
            </nav><?php

        endif;

    endif;

    do_action( 'before_sidebar' );

    dynamic_sidebar( 'sidebar-post' );

    //dynamic_sidebar('sidebar-template-with-subnav');

    ?>
</div>

</div>

