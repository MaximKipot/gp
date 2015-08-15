<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package gp
 * @since gp 1.0
 */


$is_full_width = it_is_template( get_the_ID(), 'template-full-width.php' );
$is_full_width = $is_full_width ? 'full-width' : '';

?>
<div id="post-<?php the_ID(); ?>" <?php post_class( $is_full_width ); ?>>
	<?php
        if ( has_post_thumbnail() ) {

            $image_info = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'gp-max' );
            $image = $image_info[0];

            ?>
            <article class="entry-header">   
                <div class="post-image">
                    <img src="<?php echo esc_url( $image ); ?>" width="<?php echo $image_info[1]; ?>" height="<?php echo $image_info[2]; ?>" alt="" />
                    <div class="cover">
                        <h1><?php the_title(); ?></h1>
                    </div>
                </div>

            </article>
            <?php

        } 
    ?>

	<article class="entry-content">
        <?php

    		the_content();
    		wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'gp' ), 'after' => '</div>' ) );
    		edit_post_link( __( 'Edit', 'gp' ), '<span class="edit-link">', '</span>' );

        ?>
	</article>
</div>
