<?php
/**
 * Blog sidebar.
 *
 * @package gp
 * @since gp 1.0
 */

?>
<div id="subnavigation_holder">
    <div id="subnavigation">
                <?php

                    if ( is_search() ) : ?>
                        <div class="search-results-hgroup">
                            <h2 class="subtitle"><?php _e( 'Search Results For', 'gp' ); ?></h2>
                            <h1 class="title">&ldquo;<?php echo get_search_query(); ?>&rdquo;</h1>
                        </div><?php
                    endif;

                    if ( is_archive() ) : ?>

                        <div class="archive-results-hgroup">
                            <!-- <h2 class="subtitle"><?php
                                if ( is_category() ) {
                                    _e( 'Category Archives', 'gp' );
                                } elseif ( is_tag() ) {
                                    _e( 'Category Archives', 'gp' );
                                } elseif ( is_author() ) {
                                    _e( 'Author Archives', 'gp' );
                                } elseif ( is_day() ) {
                                    _e( 'Daily Archives', 'gp' );
                                } elseif ( is_month() ) {
                                    _e( 'Monthly Archives', 'gp' );
                                } elseif ( is_year() ) {
                                    _e( 'Yearly Archives', 'gp' );
                                } else {
                                    _e( 'Archives', 'gp' );
                                } ?>
                            </h2> -->
                            <h1 class="title"><?php
                                if ( is_category() ) {
                                    echo single_cat_title( '', false );
                                } elseif ( is_tag() ) {
                                    echo single_tag_title( '', false );
                                } elseif ( is_author() ) {
                                    /* Queue the first post, that way we know
                                     * what author we're dealing with (if that is the case).
                                    */
                                    the_post();
                                    echo get_the_author();
                                    /* Since we called the_post() above, we need to
                                     * rewind the loop back to the beginning that way
                                     * we can run the loop properly, in full.
                                     */
                                    rewind_posts();
                                } elseif ( is_day() ) {
                                    echo get_the_date();
                                } elseif ( is_month() ) {
                                    echo get_the_date( 'F Y' );
                                } elseif ( is_year() ) {
                                    echo get_the_date( 'Y' );
                                } ?>
                            </h1>
                        </div>

                    <?php

                    endif;

                    do_action( 'before_sidebar' );

                    dynamic_sidebar( 'sidebar-blog' );

                ?>
    </div>
</div>