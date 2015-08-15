<?php
/**
 * gp functions and definitions
 *
 * @package gp
 * @since gp 1.0
 */

/**
 * Define common constants.
 */
define( 'gp_IMAGES_URI',  get_template_directory_uri() . '/images' );
define( 'gp_LIB_DIR', 	  dirname(__FILE__) . '/lib' );
define( 'gp_INC_DIR', 	  dirname(__FILE__) . '/inc' );
define( 'gp_CSS_DIR', 	  dirname(__FILE__) . '/css' );
define( 'gp_JS_DIR', 	  dirname(__FILE__) . '/js' );
define( 'gp_WIDGETS_DIR', gp_INC_DIR . '/widgets' );

/**
 * Require various files.
 */
require_once gp_LIB_DIR . '/intheme-utils.php';				// File contains useful functions for general tasks.
require_once gp_LIB_DIR . '/appreciate.php';				// Appreciate Post functionality.
require_once gp_INC_DIR . '/gp-theme.php';
require_once gp_INC_DIR . '/slider.php';					// Full page slider functionality.
// require_once gp_INC_DIR . '/portfolio/portfolio.php';		// Portfolio functionality.
require_once gp_INC_DIR . '/contacts.php';					// Contacts page functionality.
require_once gp_INC_DIR . '/background.php';				// Page with background image functionality.
require_once gp_INC_DIR . '/template-tags.php';				// Custom template tags for this theme.
require_once gp_INC_DIR . '/tweaks.php';					// Various functionality tweaks.
require_once gp_INC_DIR . '/shortcodes.php';				// Shortcodes.
require_once gp_INC_DIR . '/post-formats.php';				// Post formats.
require_once gp_INC_DIR . '/widgets.php';					// Widgets.


/**
 * Initialize gp Theme.
 */
function gp_init() {

	/**
	 * Add custom image sizes.
	 */
	add_image_size( 'gp-thumbnail', 583, 328, true ); 			// used in blog index page
	add_image_size( 'gp-gallery-thumbnail', 500, 500, true );	// used in content gallery

	/**
	 * Maximum image size displayed on site.
	 * Used on: full page slider, portfolio, etc.
	 */
	add_image_size( 'gp-max', 1920, 1280, false );				// another good option 1500x1000

	/**
	 * Note, if you are changing the existing size dimensions,
	 * then Wordpress will not automatically regenerate all the images.
	 *
	 * To do so, you could try using it_regenerate_wp_images() function.
	 * Put it inside your admin_init hook, and visit admin section.
	 * After waiting for usually a long time, all the images will be
	 * available in a newly specified size.
	 */

	/**
	 * Remove admin bar for everyone
	 */
	add_filter( 'show_admin_bar' , '__return_false');

}
add_action( 'init', 'gp_init', 1 );


/**
 * Initialize admin side.
 */
function gp_admin_init() {

	/**
	 * General scripts and styles for admin area.
	 */
    wp_enqueue_script( 'gp-wp-admin', get_template_directory_uri() . '/js/wp-admin/admin.js' );
    wp_enqueue_style( 'gp-wp-admin', get_template_directory_uri() . '/css/wp-admin/admin.css' );

    add_editor_style( 'css/wp-admin/editor-styles.css' );

}
add_action( 'admin_init', 'gp_admin_init', 1 );


/**
 * Specify the maximum content width.
 * This is based on CSS, when screen becomes big enough the content
 * area becomes fixed, so there is no need to have bigger images.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1021;
}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * @since gp 1.0
 */
function gp_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 */
	load_theme_textdomain( 'gp', get_template_directory() . '/languages' );

	/**
	 * Enable theme support for standard features.
	 */
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Register menus.
	 */
	register_nav_menus( array(
		'header_primary' 	=> __( 'Header Primary Menu', 'gp' ),
		'header_secondary' 	=> __( 'Header Secondary Menu', 'gp' ),
		'footer_primary' 	=> __( 'Footer Primary Menu', 'gp' )
	) );


	/**
	 * Enable shortcodes for widgets.
	 */
	add_filter( 'widget_text', 'do_shortcode' );


	/**
	 * Initialize theme options.
	 */
	require_once gp_INC_DIR . '/options.php';

	if ( !function_exists( 'optionsframework_init' ) ) {
		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/options-framework/' );
		require_once dirname(__FILE__) . '/options-framework/options-framework.php';
	}

}
add_action( 'after_setup_theme', 'gp_setup' );



/* MMM */

// fix some badly enqueued scripts with no sense of HTTPS
// add_action('wp_print_scripts', 'enqueueScriptsFix', 100);
// add_action('wp_print_styles', 'enqueueStylesFix', 100);
 
/**
* force plugins to load scripts with SSL if page is SSL
*/
// function enqueueScriptsFix() {
//     if (!is_admin()) {
//         if (!empty($_SERVER['HTTPS'])) {
//             global $wp_scripts;
//             foreach ((array) $wp_scripts->registered as $script) {
//                 if (stripos($script->src, 'http://', 0) !== FALSE)
//                     $script->src = str_replace('http://', 'https://', $script->src);
//             }
//         }
//     }
// }
 
/**
* force plugins to load styles with SSL if page is SSL
*/
// function enqueueStylesFix() {
//     if (!is_admin()) {
//         if (!empty($_SERVER['HTTPS'])) {
//             global $wp_styles;
//             foreach ((array) $wp_styles->registered as $script) {
//                 if (stripos($script->src, 'http://', 0) !== FALSE)
//                     $script->src = str_replace('http://', 'https://', $script->src);
//             }
//         }
//     }
// }

/* MMM */


/**
 * Enqueue scripts and styles.
 */
function gp_scripts_and_styles() {

	/**
	 * CSS
	 */
	// if(is_ssl()) {
	// 	echo home_url('https');
	// 	echo "SSL";
	// }
	// else {
	// 	echo home_url('http');
	// 	echo "NO SSL";
	// }
	get_template_directory_uri();
	wp_enqueue_style( 'global', 	 	   get_template_directory_uri() . '/css/global.css' );		// global CSS, should consist of tags only
    wp_enqueue_style( 'gp-grid', 	   get_template_directory_uri() . '/css/grid.css' );		// fluid grid used in content columns
    // wp_enqueue_style( 'fontawesome',       get_template_directory_uri() . '/css/font-awesome.css', 'style');
	wp_enqueue_style( 'fontello-icons',    get_template_directory_uri() . '/css/fontello.css' );	// font icon containing Entypo collection, add more icons at fontello.com
	wp_enqueue_style( 'style', 		 	   get_stylesheet_uri() );									// main stylesheet
    wp_enqueue_style( 'gp-responsive', get_template_directory_uri() . '/css/responsive.css' );  // responsive rules


    wp_enqueue_style( 'mCustomScrollbar', 		   get_template_directory_uri() . '/css/jquery.mCustomScrollbar.css' );  // mCustomScrollbar

	wp_enqueue_style( 'gp_style', 		   get_template_directory_uri() . '/css/style.css' );  // gp_styling

    if ( file_exists( gp_CSS_DIR . '/user.css' ) ) {
    	wp_enqueue_style( 'user',		   get_template_directory_uri() . '/css/user.css' );  		// user custom rules
    }
    
	wp_enqueue_style( 'slider_style', 	   get_template_directory_uri() . '/js/slider/jquery.bxslider.css' );  // gp_styling
    
	/**
	 * JS
	 */
	wp_enqueue_script( 'tinyscrollbar', 	get_template_directory_uri() . '/js/jquery.tinyscrollbar.js',    array( 'jquery' ), false, true );	// scrollbar plugin
	wp_enqueue_script( 'sharrre', 			get_template_directory_uri() . '/js/jquery.sharrre-1.3.4.js',    array( 'jquery' ), false, true );	// share count plugin
	wp_enqueue_script( 'jquery-transit',	get_template_directory_uri() . '/js/jquery.transit.js', 	     array( 'jquery' ), false, true );	// css3 transition plugin
	wp_enqueue_script( 'gp-utils', 		get_template_directory_uri() . '/js/utils.js', 				     array( 'jquery' ), false, true ); // other tiny plugins
	// wp_enqueue_script( 'gp-size', 		get_template_directory_uri() . '/js/size.js', 				     array( 'jquery', 'gp-utils' ), false, true );	// file containing size adjustmens for DOM elements on window.resize event
	// wp_enqueue_script( 'gp-grid', 		get_template_directory_uri() . '/js/jquery.gp-grid.js',      array( 'jquery' ), false, true );	// grid portfolio layout plugin
	wp_enqueue_script( 'jquery-reveal',		get_template_directory_uri() . '/js/jquery.reveal.js', 		     array( 'jquery' ), false, true ); // modal box plugin
	wp_enqueue_script( 'gp-lightbox',	get_template_directory_uri() . '/js/jquery.gp-lightbox.js',  array( 'jquery', 'jquery-transit' ), false, true ); // lightbox plugin
	wp_enqueue_script( 'iscroll',  			get_template_directory_uri() . '/js/iscroll.js', 	 			 array(), false, true ); // iScroll 4
	wp_enqueue_script( 'gp-slider',  	get_template_directory_uri() . '/js/jquery.gp-slider.js', 	 array( 'jquery', 'jquery-transit', 'iscroll' ), false, true ); // full page slider plugin
	//wp_enqueue_script( 'gp', 			get_template_directory_uri() . '/js/main.js', 					 array( 'jquery', 'gp-utils' ), false, true ); // main script
	
	/* MMM */
	wp_enqueue_script( 'modernizr', 		get_template_directory_uri() . '/js/modernizr-latest.js', 		 array( 'jquery' ), false, true ); // main script
	wp_enqueue_script( 'scrollbar', 		get_template_directory_uri() . '/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), false, true ); // main script
	wp_enqueue_script( 'partials', 			get_template_directory_uri() . '/js/partials.js', 			     array( 'jquery' ), false, true ); // main script
	wp_enqueue_script( 'images_loaded', 	get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', 			     array( 'jquery' ), false, true ); // main script
	
	global $wp_query;

    //Check which template is assigned to current page we are looking at
    $template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );
    // echo $template_name;
	wp_enqueue_script( 'textfill', 			get_template_directory_uri() . '/js/jquery.textfill.min.js', 	array( 'jquery' ), false, true ); // subpage H1 script	
	
	//if($template_name != 'vertical.php' && $template_name != 'vertical_subnavigation.php' ){ /* MMM start was used to integrate grid.js only at some places*/
        //If page is using grid_page_category template then load our slider script
		
		// Old Isotope Version
		wp_enqueue_script( 'isotope', 			get_template_directory_uri() . '/js/jquery.isotope.min.js',  array( 'jquery' ), false, true ); // isotope script
		
		// New Isotope version
		// wp_enqueue_script( 'isotope', 			get_template_directory_uri() . '/js/isotope.pkgd.min.js',  array( 'jquery' ), false, true ); // isotope script
		// wp_enqueue_script( 'isotope-horizontal', 			get_template_directory_uri() . '/js/masonry-horizontal.js',  array( 'jquery' ), false, true ); // horizontal layout isotope script
		
		wp_enqueue_script( 'elements_grid', 	get_template_directory_uri() . '/js/grid.js', 				 array( 'jquery' ), false, true ); // grid script	
		
		wp_enqueue_script( 'jquery.bxslider', 	get_template_directory_uri() . '/js/slider/jquery.bxslider.js', array( 'jquery' ), false, true ); // slider script	
		
	//} /* MMM end was used to integrate grid.js only at some places*/

	/* MMM */


    if ( file_exists( gp_JS_DIR . '/user.js' ) ) {
		wp_enqueue_script( 'gp-user',	get_template_directory_uri() . '/js/user.js', 					 array( 'jquery', 'gp-utils' ), false, true ); // user custom javascript
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );	// WP standard comment reply script
	}


	wp_enqueue_script( 'respond', 	get_template_directory_uri() . '/js/respond.js', 			     array( 'jquery' ), false, true ); // main script
	


	echo '<script> var _resources = "'. get_template_directory_uri() . '/json/"; </script>';

	
}

add_action( 'wp_enqueue_scripts', 'gp_scripts_and_styles' );


/**
 * Modify native menu walker class with some extra functionality.
 */
class Intheme_Menu_Walker extends Walker_Nav_Menu {

	function is_item_portfolio_index( $object_id ) {

		return it_is_template( $object_id, 'template-portfolio.php' ) ||
			   it_is_template( $object_id, 'template-portfolio-grid.php' );

	}

	function has_current( $elements ) {

		foreach ( $elements as $element ) {

			if ( $element->current ) {
				return true;
			}

		}

		return false;

	}

	function walk( $elements, $max_depth, $args ) {

		global $post;

		// We are on a project page
		if ( $post && $post->post_type == 'gp_portfolio' && is_single() ) {

			// If there's a current element, then let's not do anything
			$found = $this->has_current( $elements );

			if ( ! $found ) {

				foreach ( $elements as $key => $element ) {

					// Search only root items first
					if ( 0 == $element->menu_item_parent ) {

						// If our current menu item is a Page with Portfolio Template
						if ( $this->is_item_portfolio_index( $element->object_id ) ) {
							$elements[$key]->classes[] = 'active';
							$found = true;
							break;
						}

					}

				}

				// We were unable to find Portfolio on the root, then activate any item that has
				// template-portfolio.php or template-portfolio-grid.php
				if ( ! $found ) {

					foreach ( $elements as $key => $element ) {

						if ( $this->is_item_portfolio_index( $element->object_id ) ) {
							$elements[$key]->classes[] = 'active';
							break;
						}

					}

				}

			}

		}

		return parent::walk( $elements, $max_depth, $args );

	}

	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element ) {
			return;
		}

		$theme_location = isset( $args[0] ) && isset( $args[0]->theme_location ) ? $args[0]->theme_location : '';

		global $post;

		/**
		 * Adjust menu if our current post_type is gp_portfolio
		 */
		if ( $post && $post->post_type == 'gp_portfolio' ) {

			// If our current menu item is a Page with Portfolio Template
			if ( $this->is_item_portfolio_index( $element->object_id ) ) {

				if ( isset( $children_elements[$element->ID] ) ) {

					// Check if this Portfolio menu item has children that are
					// currently active Project Type terms. This also means
					// that our current page is Project Type archive.

					foreach ( $children_elements[$element->ID] as $child ) {
						if ( in_array( 'current-gp-project-type-ancestor', $child->classes ) ) {
							$element->classes[] = 'active';
						}
					}

				}


			}

		}

		$id_field = $this->db_fields['id'];

		/**
		 * Adds the "has-children" class to the current item if it has children.
		 */
		if ( ! empty( $children_elements[$element->$id_field] ) ) {
			array_push( $element->classes, 'has-children' );
		}

		/**
		 * That's it, now call the default function to do the rest.
		 */
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

}

if ( is_admin() ) {
	require_once gp_INC_DIR . '/portfolio/upgrade.php';
}

include_once gp_INC_DIR . '/user.php';		// User modifications.

/* MMM */

function my_class_names($classes) {
	// add 'class-name' to the $classes array
	$classes[] = 'body_horizontal';
	// return the $classes array
	return $classes;
}

//Now add test class to the filter
add_filter('body_class','my_class_names');


# wp-content/themes/monochrome/functions.php
function force_https_the_content($content) {
  if ( is_ssl() )
  {
	$content = str_replace( 'src="http://', 'src="https://', $content );
    $content = str_replace( 'href="http://dev.germanpersonnel.de', 'href="https://dev.germanpersonnel.de', $content );
  }
  return $content;
}
add_filter('the_content', 'force_https_the_content');

/* The Display Widgets plugin doesn't update
 * is_active_sidebar() for hidden widgets.
 * This function tests dynamic_sidebar() to
 * see if it is empty. Use instead of
 * is_active_sidebar() to determine if the
 * sidebar has any widgets. Remember to
 * change back to is_active_sidebar() if no
 * longer using Display Widgets.
 */
function has_visible_widgets( $sidebar_id ) {

    // First check if sidebar has any widgets
    if ( is_active_sidebar($sidebar_id) ) {
        // Use PHP output buffer to load
        // the sidebar into a variable
        ob_start();
        dynamic_sidebar($sidebar_id);
        $sidebar = ob_get_contents();
        ob_end_clean();
        // Return false if sidebar is empty
        if ($sidebar == "") return false;
    } else {
        return false;
    }

    // Return true if sidebar is not empty
    return true;

}

add_filter('iphorm_needs_raw_tag', '__return_false');

function is_subpage() {
	global $post;
	if ( is_page() && $post->post_parent ) {
		$parentID = $post->post_parent;
		return $parentID;
	} else {
		return false;
	};
};

function get_category_tags($cat_id) {
	$wp_query = null;
	$wp_query = new WP_Query();
	$wp_query -> query('order=ASC&cat='.$cat_id.'&showposts=1000'.'&paged='.$paged);
    // if (have_posts()) : while (have_posts()) : the_post();
    while ($wp_query->have_posts()) : $wp_query->the_post();
        $posttags = get_the_tags();
        print_r($posttags);
        if ($posttags) {
            foreach($posttags as $tag) {
                $all_tags_arr[] = $tag -> name; //USING JUST $tag MAKING $all_tags_arr A MULTI-DIMENSIONAL ARRAY, WHICH DOES WORK WITH array_unique
            }
        }
    endwhile; 
    // endif; 

    $tags_arr = array_unique($all_tags_arr); //REMOVES DUPLICATES
    return $tags_arr;
}

remove_filter( 'the_content', 'wpautop' ); // MMM removed 2014.05.06

// remove_filter( 'the_excerpt', 'wpautop' );


	function myformatTinyMCE($in)
	{

	 $in['remove_linebreaks']=false;
	 $in['convert_newlines_to_brs']=true;
	 $in['remove_redundant_brs'] = false;
	 $in['force_br_newlines'] = true;
     $in['force_p_newlines'] = false;
	 $in['gecko_spellcheck']=false;
	 $in['keep_styles']=true;
	 $in['accessibility_focus']=true;
	 $in['tabfocus_elements']='major-publishing-actions';
	 $in['media_strict']=false;
	 $in['paste_remove_styles']=false;
	 $in['paste_remove_spans']=false;
	 $in['paste_strip_class_attributes']='none';
	 $in['paste_text_use_dialog']=true;
	 $in['wpeditimage_disable_captions']=true;
	 $in['plugins']='tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpfullscreen';
	 $in['content_css']=get_template_directory_uri() . "/editor-style.css";
	 $in['wpautop']=true;
	 $in['apply_source_formatting']=false;
	 $in['toolbar1']='bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_fullscreen,wp_adv ';
	 $in['toolbar2']='formatselect,underline,alignjustify,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help ';
	 $in['toolbar3']='';
	 $in['toolbar4']='';

	 print_r( $in );

	 return $in;
	}
	add_filter('tiny_mce_before_init', 'myformatTinyMCE' );