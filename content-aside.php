<?php
/**
 * @package gp
 * @since gp 1.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="text-contents">
        <div class="entry-summary"><?php

            add_filter( 'excerpt_length', 'gp_increased_excerpt_lenght', 1001 );

            the_excerpt();

            remove_filter( 'excerpt_length', 'gp_increased_excerpt_lenght', 1001 );
            add_filter( 'excerpt_length', 'gp_excerpt_lenght', 1000 );

            ?>
        </div>
    </div>

</article>