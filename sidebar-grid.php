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

                    ?>

                    <h1 class="title">
                        <?php
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
                    <p>
                        <?php 
                        $_post = get_post($post->ID); 
                        echo $_post->post_content;  
 
                        // Post Thumbnail Get Data
                        if(has_post_thumbnail()) {                    
                            $image_src = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
                            $_have_thumbnail = TRUE;
                        } 

                        ?>
                    </p>  
                    
                        <?php
                            global $_need_cat;
                            $wp_query_sidebar = null;
                            $wp_query_sidebar = new WP_Query();
                            $wp_query_sidebar -> query('order=ASC&cat='.$_need_cat.'&showposts=1000');
                            // if (have_posts()) : while (have_posts()) : the_post();
                            while ($wp_query_sidebar->have_posts()) : $wp_query_sidebar->the_post();
                                $posttags = get_the_tags();
                                // print_r($posttags);
                                if ($posttags) {
                                    foreach($posttags as $tag) {
                                        $all_tags_arr[] = $tag -> name; //USING JUST $tag MAKING $all_tags_arr A MULTI-DIMENSIONAL ARRAY, WHICH DOES WORK WITH array_unique
                                    }
                                }
                            endwhile; 
                            // endif; 

                            if(is_array($all_tags_arr)) $tags_arr = array_unique($all_tags_arr); //REMOVES DUPLICATES
                            
                            $sub_categories =  get_categories('parent='.$_need_cat.'&hide_empty=0');
                            
                            if($tags_arr || $sub_categories) {
                                
                                ?>

                                <h2>Filter:</h2>
                                <ul id="filters">
                                <li><a href="#" class="active-filter" data-filter="*">Alle</a></li>
                                
                                <?php

                                if($tags_arr) {
                                    foreach($tags_arr as $key => $val) {
                                        echo '<li><a href="#" data-filter=".'.str_replace(" ", "_", $val).'">'.$val.'</a></li>';   
                                    }
                                }                                              
                                
                                if ($sub_categories) {
                                    foreach($sub_categories as $cat) {
                                        // $all_cats_arr[] = $cat -> category_nicename; //USING JUST $tag MAKING $all_tags_arr A MULTI-DIMENSIONAL ARRAY, WHICH DOES WORK WITH array_unique
                                        echo '<li><a href="#" data-filter=".'.$cat -> category_nicename.'">'.$cat -> name.'</a></li>';   
                                    }
                                }

                                ?>

                                </ul>

                                <?php

                                // wp_reset_postdata();

                            }

                            // Post Thumbnail defined above
                            if($_have_thumbnail) {                    
                                 echo '<img src="' . $image_src[0]  . '" width="100%"  />';
                            } 

                            
                        ?>
                    

                        

                    <?php

                    

                    do_action( 'before_sidebar' );

                    dynamic_sidebar( 'sidebar-grid' );

                ?>
    </div>
</div>