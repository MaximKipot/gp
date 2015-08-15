<?php
/**
 * @package gp
 * @since gp 1.0
 */

$post_categories = get_the_category();
$posted_in = array();

if ( $post_categories && gp_categorized_blog() ) {

	if ( count( $post_categories ) > 1 ) {

		foreach ( $post_categories as $post_cat ) {
			if ( $post_cat->term_id != 1 ) {
				$posted_in[] = $post_cat->name;
			}
		}

	} elseif ( !in_category( 1 ) ) {
		$posted_in[] = $post_categories[0]->name;
	}

}

?>
<article id="content-single" data-accordion="" data-column-width="430" data-gap="70" <?php post_class(); ?>>
	

	
		<?php the_content(); ?>
		<?php edit_post_link( __( 'Edit', 'gp' ), '<span class="edit-link">', '</span>' ); ?>
	

</article>