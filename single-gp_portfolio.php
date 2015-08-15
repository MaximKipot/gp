<?php
/**
 * Portfolio single project template.
 */

gp_add_html_class( 'horizontal-page' );

the_post();

$project = new PortfolioProject( get_the_ID() );
$project_media = $project->get_media();
$featured = $project->get_featured_media();

$max_upscale_height = of_get_option( 'gp_allow_image_upscaling' ) ? apply_filters( 'gp_max_upscale_height', 3000 ) : '';

if ( $featured ) {
    global $gp_theme;

    // Use featured image as page thumbnail.
    $image = '';
    if ( $featured->is_image() ) {
        $image = $featured->get_image_data( 'gp-max' );
    } else {
        $image = $featured->get_video_thumbnail( 'gp-max' );
    }

    if ( $image ) {
        $gp_theme->set_image( $image['src'] );
    }

    // Use project excerpt as page description.
    $excerpt = get_the_excerpt();
    if ( $excerpt ) {
        $gp_theme->set_description( $excerpt );
    }
}

get_header();

?>
<div id="main" class="site site-with-sidebar"><?php

    if ( ! post_password_required() ) :

        $lazy_loading = of_get_option( 'gp_lazy_loading' );

        ?>
        <div id="content" class="site-content">

            <article class="portfolio-single horizontal-content<?php echo ($lazy_loading ? ' lazy-loading' : ''); ?>" data-loading="<?php echo esc_attr( __( 'Please wait...', 'gp' ) ); ?>" data-lazy="<?php echo esc_attr( $lazy_loading ); ?>"><?php

                $index = 0;

                foreach ( $project_media as $media_item ) :

                    if ( ! $media_item->meta_published ) continue;

                    $index++;

                    if ( $media_item->is_image() ) :

                        $image = $media_item->get_image_data( 'gp-max' );

                        if ( ! $image ) continue;

                        ?>
                        <div class="horizontal-item project-image">
                            <figure>
                                <a href="<?php echo esc_url( $image['src'] ); ?>" class="project-image-link"><?php

                                    if ( $lazy_loading ) : ?>
                                        <div class="image lazy-image" data-width="<?php echo $image['width']; ?>" data-height="<?php echo $image['height']; ?>">
                                            <div class="loading"><?php _e( 'Please Wait', 'gp' ); ?></div>
                                        </div><?php
                                    else: ?>
                                        <img class="image" src="<?php echo esc_url( $image['src'] ); ?>" width="<?php echo $image['width']; ?>" height="<?php echo $image['height']; ?>" alt="<?php echo esc_attr( $media_item->get_alt() ); ?>" /><?php
                                    endif;

                                    ?>
                                </a><?php

                                if ( $media_item->meta_description ) : ?>
                                    <figcaption><?php echo nl2br( $media_item->meta_description ); ?></figcaption><?php
                                endif;

                                ?>
                            </figure>
                        </div><?php

                    else:

                        if ( ! $media_item->meta_embed ) continue;

                        $video_size = $media_item->get_video_size();

                        // Don't show video, if there is no width and height set
                        if ( ! $video_size['width'] || ! $video_size['height'] ) {
                            continue;
                        }

                        ?>
                        <div class="horizontal-item project-video">
                            <?php echo $media_item->meta_embed; ?>
                        </div><?php

                    endif;

                endforeach;


                /**
                 * Portfolio navigation & sharing.
                 */

                $disable_like_this_project = of_get_option( 'gp_disable_like_this_project' );
                $disable_other_projects = of_get_option( 'gp_disable_other_projects' );

                if ( ! ( $disable_like_this_project && $disable_other_projects ) ) : ?>

                    <nav class="portfolio-navigation"><?php

                        if ( ! $disable_like_this_project ) : ?>

                            <header<?php if ( $disable_other_projects ) { echo ' class="other-projects-disabled"'; } ?>>
                                <h3><?php _e( 'Like this project?', 'gp' ); ?></h3>
                                <div class="feedback-buttons"><?php

                                    $args = array(
                                            'class' => 'btn-appreciate',
                                            'title' => __( 'Appreciate', 'gp' ),
                                            'title_after' => __( 'Appreciated', 'gp' )
                                        );
                                    gp_appreciate( $post->ID, $args );

                                    $args = array(
                                            'id' => 'sharrre-project',
                                            'data-buttons-title' => array(
                                                    __( 'Share this project', 'gp' )
                                                )
                                        );

                                    $sharrre = gp_get_social_share( $args );

                                    if ( $sharrre ) : ?>
                                        <span class="choice"><span><?php _e( 'Or', 'gp' ); ?></span></span><?php
                                        echo $sharrre;
                                    endif;

                                ?>
                                </div>
                            </header><?php

                        endif; // end of Like this project?

                        if ( ! $disable_other_projects ) : ?>

                            <div class="navigation">
                                <div class="other-projects">

                                    <h3><?php _e( 'Other projects', 'gp' ); ?></h3>
                                    <?php

                                    $previous_project = false;
                                    $next_project = false;
                                    $related_projects = $project->get_other_projects();

                                    $index = 0;

                                    foreach ( $related_projects as $key => $related_project ) :

                                        $index++;
                                        $current = $project->post->ID == $related_project->post->ID;

                                        if ( $current ) {

                                            if ( $key && isset( $related_projects[$key - 1] ) ) {
                                                $previous_project = $related_projects[$key - 1];
                                            }

                                            if ( isset( $related_projects[$key + 1] ) ) {
                                                $next_project = $related_projects[$key + 1];
                                            }

                                        }

                                        $featured_media = $related_project->get_featured_media();

                                        $attr = array(
                                                'href' => array(
                                                        esc_url( get_permalink( $related_project->post->ID ) )
                                                    ),
                                                'class' => array(
                                                        'item-' . $index,
                                                        $index % 4 == 0 ? 'fourth' : '',
                                                        $current ? 'active' : ''
                                                    )
                                            );

                                        $image = array( 'src' => get_template_directory_uri() . '/images/no-portfolio-thumbnail.png' );

                                        if ( $featured_media ) {
                                            $featured_image = $featured_media->get_thumbnail( 'gp-gallery-thumbnail' );
                                            if ( $featured_image ) {
                                                $image = $featured_image;
                                            }
                                        }

                                        $attr['style'] = array(
                                                'background-image: url(' . $image['src'] . ')'
                                            );

                                        ?>
                                        <a<?php echo it_array_to_attributes( $attr ); ?>>
                                            <?php echo $related_project->post->post_title; ?>
                                            <span class="hover"><?php
                                                if ( $current ) {
                                                    _e( 'Current', 'gp' );
                                                } else {
                                                    _e( 'View', 'gp' );
                                                }
                                                ?>
                                            </span>
                                        </a><?php

                                    endforeach; ?>
                                </div><?php

                                /**
                                 * Next / Previous / Back to portfolio buttons.
                                 */

                                if ( $previous_project ) : ?>
                                    <a href="<?php echo esc_url( get_permalink( $previous_project->post->ID ) ); ?>" class="button-minimal prev-project button-icon-left icon-left-open-big"><?php _e( 'Previous', 'gp' ); ?></a><?php
                                endif;

                                if ( $next_project ) : ?>
                                    <a href="<?php echo esc_url( get_permalink( $next_project->post->ID ) ); ?>" class="button-minimal next-project button-icon-right icon-right-open-big"><?php _e( 'Next', 'gp' ); ?></a><?php
                                endif;

                                $back_title = $project->meta_back_to_link ? __( 'Back to projects', 'gp' ) : __( 'Back to portfolio', 'gp' );

                                ?>
                                <a href="<?php echo esc_url( $project->get_back_link() ); ?>" class="button-minimal back-portfolio"><?php echo $back_title ?></a>
                            </div><?php

                        endif; // end of Other Proejcts

                        ?>
                    </nav><?php

                endif;

                ?>
            </article>

        </div><?php

    endif;

    get_sidebar( 'portfolio-single' );

    ?>
</div><?php // end of #main

get_footer();