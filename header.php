<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package gp
 * @since gp 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); gp_html_classes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width,  initial-scale=1, maximum-scale=1" />
<!-- <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1"> -->
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '&mdash;', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " &mdash; $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' / ' . sprintf( __( 'Page %s', 'gp' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<!--FONT from Typekit-->
 	<script type="text/javascript" src="//use.typekit.net/zus6pww.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
<!--FONT from Typekit END-->


 <!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->

<!-- Auf alte Browser prüfen -->
<script type="text/javascript"> 
var $buoop = {}; 
$buoop.ol = window.onload; 
window.onload=function(){ 
 try {if ($buoop.ol) $buoop.ol();}catch (e) {} 
 var e = document.createElement("script"); 
 e.setAttribute("type", "text/javascript"); 
 e.setAttribute("src", "//browser-update.org/update.js"); 
 document.body.appendChild(e); 
} 
</script> 
	


<?php

	wp_head();

?>
</head>

<body <?php body_class(); ?>>
<noscript>
    <div style="position: fixed; top: 0px; left: 0px; z-index: 100000; height: 100%; width: 100%; background-color: #D0D0D0">
        
		<div id="main" class="site">

			<div class="page-with-background">
				<article id="post-0" style="margin-top: 20px;" class="post error404 not-found js-vertical-center">
					<header>
						<h1 class="entry-title">Uups! Leider geht die Webseite ohne Javascript nicht.</h1>
					</header>
					<div>
						<p>Bitte aktivieren Sie Javascript in ihrem Browser.</p>
						<!-- <p>Es sieht so aus das hier nichts gefunden werden kann. Probieren Sie die Navigation oben oder die Suche.</p> -->
									</div>
				</article>
			</div>

		</div>


    </div>
</noscript>
<div id="page-wrapper">
	<div id="login">
        <!-- <div id="per-logo">
        </div>
        <div id="login-frame">
            <form action="">
                <fieldset>
                    <div class="user">
                        <label for="benutzer">Benutzer</label>
                        <input type=”text” name="username" id="benutzer" />
                    </div>
                    <div class="password">
                        <label for="password">Passwort</label>
                        <input type="password" name="password" id="password" />
                        <p><a href="#">Passwort vergessen?</a></p>
                    </div>
                    <div class="button">
                        <label></label>
                        <button type="submit" value="login">Anmelden</button>
                    </div>
                </fieldset>
            </form>
        </div> -->
        <iframe id="top_iframe" src="about:blank"></iframe>
		<div id="login-btn">
            <div id="log-txt">persy Login</div>
        </div>
    </div>	
	<header id="header" class="clearfix">
		
		<a href="<?php echo home_url(); ?>">
			<div id="header-logo-block">
			</div>
		</a>

		
		<div id="header-navigation">
			<?php
				// show menus only if they have been assigned
				if ( has_nav_menu( 'header_primary' ) ) : ?>
					<nav class="primary-navigation"><?php
						wp_nav_menu( array( 'theme_location' => 'header_primary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
					</nav><?php
				endif;

				if ( has_nav_menu( 'header_secondary' ) ) : ?>
					<nav class="secondary-navigation"><?php
						wp_nav_menu( array( 'theme_location' => 'header_secondary', 'walker' => new Intheme_Menu_Walker() ) ); ?>
					</nav><?php
				endif;
			?>
		</div>
	</header>
	<div id="header-push"></div>
	<section id="grid_content">
	<?php

		do_action( 'before' );

