<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package gp
 * @since gp 1.0
 */
?>

    
</section>

<footer id="footer" class="clearfix">
    <div class="footer-inner clearfix">
        <?php do_action( 'footer_social' ); ?>
        <div class="footer-links"><?php

            // Show menu, if it has been assigned.
            if ( has_nav_menu( 'footer_primary' ) ): ?>
                <nav class="footer-navigation"><?php
                    @wp_nav_menu( array( 'theme_location' => 'footer_primary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
                </nav><?php
            endif;

            $copyright = of_get_option( 'gp_copyright_text' );
            if ( !empty( $copyright ) ) : ?>
                <div class="credits"><?php echo $copyright; ?></div><?php
            endif;

            ?>
        </div>
    </div>
</footer>
</div><!-- #page-wrapper -->


<?php

wp_footer();

?>

</body>
</html>