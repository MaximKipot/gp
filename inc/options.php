<?php
/**
 * File contains definition and implementation of Theme Options.
 *
 * @since gp 1.0
 */


/**
 * Returns all supported Social Networks.
 */
function gp_get_social_networks() {

    $networks = array(
            'Dribbble' => 'dribbble',
            'Facebook' => 'facebook',
            'Google Plus' => 'gplus',
            'Flickr' => 'flickr',
            'Pinterest' => 'pinterest',
            'Twitter' => 'twitter',
            'Tumblr' => 'tumblr',
            'Vimeo' => 'vimeo',
            'Linkedin' => 'linkedin',
            'Instagram' => 'instagram',
        );

    return $networks;

}


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 */
function optionsframework_options() {

    /**
     * Options page: General Settings
     */
    $options = array();

    $options[] = array(
            'name' => __( 'General Settings', 'gp' ),
            'type' => 'heading'
        );

    // $options[] = array(
    //         'name' => __( 'Custom logo', 'gp' ),
    //         'id' => 'gp_logo',
    //         'type' => 'upload',
    //         'desc' => __( 'The maximum width is 190px, the recommended height is under 40px.', 'gp' )
    //     );

    // $options[] = array(
    //         'name' => __( 'Custom logo (retina)', 'gp' ),
    //         'id' => 'gp_logo_retina',
    //         'type' => 'upload',
    //         'desc' => __( 'Here you should upload two times bigger version of your logo. It will be displayed on high resolution devices (such as new iPads and iPhones). This will make logo look crisp.', 'gp' )
    //     );

    $options[] = array(
            'name' => __( 'Footer Copyright', 'gp' ),
            'desc' => __( 'Copyright displayed in the bottom. You can use HTML tags.' , 'gp' ),
            'id' => 'gp_copyright_text',
            'std' => '&copy; gp Wordpress Theme',
            'type' => 'text',
            'html' => true
        );

    $options[] = array(
            'name' => __( 'Favicon', 'gp' ),
            'desc' => __( 'Upload a 16x16 sized png/gif image that will be used as a favicon.' , 'gp' ),
            'id' => 'gp_favicon',
            'type' => 'upload'
        );

    // $options[] = array(
    //         'name' => __( 'Facebook Image', 'gp' ),
    //         'desc' => __( 'Image used on Facebook timeline when someone likes the website. If visitor likes a content page (blog post / gallery) then image will be taken automatically from content. Should be at least 200x200 in size.' , 'gp' ),
    //         'id' => 'gp_facebook_image',
    //         'type' => 'upload'
    //     );

    $options[] = array(
            'name' => __( 'Site Description', 'gp' ),
            'desc' => __( 'Give a short description of your website. This is visible in search results and when sharing your website. The description will not be used on content pages like blog post or portfolio project.' , 'gp' ),
            'id' => 'gp_site_description',
            'type' => 'textarea'
        );

    $options[] = array(
            'name' => __( 'Disable Meta Tags', 'gp' ),
            'desc' => __( 'By default this theme will create page description and page thumbnail meta tags. If you use any SEO plugin, then you should disable meta tag generation.' , 'gp' ),
            'id' => 'gp_disable_meta_tags',
            'std' => '0',
            'type' => 'checkbox'
        );

    $options[] = array(
            'name' => __( 'Tracking code', 'gp' ),
            'id' => 'gp_tracking_code',
            'desc' => __( 'Paste your Google Analytics or any other tracking code. Important: do not include &lt;script&gt;&lt;/script&gt; tags.' , 'gp' ),
            'type' => 'textarea'
        );

    /**
     * Options page: Social
     */
    $options[] = array(
            'name' => __( 'Social', 'gp' ),
            'type' => 'heading'
        );

    $options[] = array(
            'name' => __( 'Enable share buttons', 'gp' ),
            'desc' => __( 'Show social sharing buttons in the footer.' , 'gp' ),
            'id' => 'gp_share_enabled',
            'std' => '1',
            'type' => 'checkbox'
        );

    $social_networks = array(
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'googleplus' => 'Google+',
            'pinterest' => 'Pinterest',
            'linkedin' => 'LinkedIn',
            'digg' => 'Digg',
            'delicious' => 'Delicious',
            'stumbleupon' => 'StumbleUpon'
        );

    $social_networks_defaults = array(
            'facebook' => 1,
            'twitter' => 1,
            'googleplus' => 1
        );

    $options[] = array(
            'name' => __( 'Sharing Networks', 'gp' ),
            'desc' => __( 'Select social networks on which you want to share your website.' , 'gp' ),
            'id' => 'gp_share_services',
            'std' => false,
            'type' => 'multicheck',
            'options' => $social_networks,
            'str' => $social_networks_defaults
        );

    $options[] = array(
            'name' => __( 'Enable social networks', 'gp' ),
            'desc' => __( 'Show social network links in the footer.' , 'gp' ),
            'id' => 'gp_social_enabled',
            'std' => '0',
            'type' => 'checkbox'
        );

    foreach ( gp_get_social_networks() as $label => $network) {

        $options[] = array(
                'name' => $label . ' ' . __( 'URL', 'gp' ),
                'id'   => 'gp_' . $network . '_url',
                'type' => 'text'
            );

    }


    /**
     * Options page: Style
     */
    $options[] = array(
            'name' => __( 'Style', 'gp' ),
            'type' => 'heading'
        );

    // $css_color_dir = get_template_directory() . '/css/skins/';
    // $css_select = array();

    // if ( is_dir( $css_color_dir ) ) {
    //     if ( $dh = opendir( $css_color_dir ) ) {
    //         while ( ( $file = readdir( $dh ) ) !== false ) {
    //             if ( pathinfo( $file, PATHINFO_EXTENSION ) == 'css' ) {
    //                 $css_select[ $file ] = $file;
    //             }
    //         }
    //         closedir($dh);
    //     }
    // }

    // $options[] = array(
    //         'name' => __( 'Stylesheet', 'gp' ),
    //         'id' => 'gp_stylesheet',
    //         'type' => 'select',
    //         'class' => 'mini',
    //         'options' => $css_select,
    //         'std' => 'light.css'
    //     );

    $options[] = array(
            'name' => __( 'Custom CSS', 'gp' ),
            'id' => 'gp_custom_css',
            'desc' => __( 'Add your custom CSS rules here. Note: it is better to use user.css file (located in your theme\'s css directory) to add custom rules.' , 'gp' ),
            'type' => 'textarea'
        );


    $options[] = array(
            'name' => __( 'Background', 'gp' ),
            'type' => 'heading'
        );
    
    $_bg_options ['bg_color'] = 'Color';
    $_bg_options ['bg_image'] = 'Image';
    $_bg_options ['bg_css3'] = 'CSS3';

    $options[] = array(
        'name' => __( 'Body Background Options', 'gp' ),
        'id' => 'gp_body_background',
        'desc' => __( 'Body Background Options.' , 'gp' ),
        'type' => 'select',
        'options' => $_bg_options
    );

    $options[] = array(
        'name' => __( 'Background Color', 'gp' ),
        'id' => 'gp_bg_color',
        'desc' => __( 'Select a background color.' , 'gp' ),
        'std' => '#D0D0D0',
        'type' => 'color'
    );

    $options[] = array(
        'name' => __( 'CSS3 Background Options', 'gp' ),
        'id' => 'gp_bg_css3',
        'desc' => __( 'Paste your CSS3 gradient code, do not include any tags or HTML in thie field. Here you can get some CSS3 http://www.colorzilla.com/gradient-editor/' , 'gp' ),
        'type' => 'textarea'
    );

    

    $options[] = array(
        'name' => __( 'Background Image', 'gp' ),
        'id' => 'gp_bg_image',
        'desc' => __( 'Please choose an image or insert an image url to use for the main content area backgroud.' , 'gp' ),
        'type' => 'background'
    );

    // $_repeat_options['repeat'] = 'repeat';
    // $_repeat_options['repeat-x'] = 'repeat-x';
    // $_repeat_options['repeat-y'] = 'repeat-y';
    // $_repeat_options['no-repeat'] = 'no-repeat';

    // $options[] = array(
    //     'name' => __( 'Background Image repeat', 'gp' ),
    //     'id' => 'gp_bg_image_repeat',
    //     'desc' => __( 'Add your custom CSS rules here. Note: it is better to use user.css file (located in your theme\'s css directory) to add custom rules.' , 'gp' ),
    //     'type' => 'select',
    //     'options' => $_repeat_options
    // );

    $options[] = array(
        'name' => __( 'Background Image 100%', 'gp' ),
        'id' => 'gp_bg_image_100_percent',
        'desc' => __( 'Check this box to have the background image display at 100% in width and height and scale according to the browser size. This option overwrites the repeat Optino of a background image.' , 'gp' ),
        'type' => 'checkbox'
    );


    /**
     * Options page: Misc
     */
    // $options[] = array(
    //         'name' => __( 'Misc', 'gp' ),
    //         'type' => 'heading'
    //     );

    // $options[] = array(
    //         'name' => __( 'Hide password protected projects', 'gp' ),
    //         'desc' => __( 'Check to hide password protected projects from appearing inside portfolios. Password protected posts will be still accessible by using direct URL.' , 'gp' ),
    //         'id' => 'gp_hide_password_protected_projects',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    // $options[] = array(
    //         'name' => __( 'Lazy load project images', 'gp' ),
    //         'desc' => __( 'Load project images once they are visible in the user viewport.' , 'gp' ),
    //         'id' => 'gp_lazy_loading',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    // $options[] = array(
    //         'name' => __( 'Disable &quot;Like this Project?&quot;', 'gp' ),
    //         'desc' => __( 'Check to hide project sharing widget at the end of each project.' , 'gp' ),
    //         'id' => 'gp_disable_like_this_project',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    // $options[] = array(
    //         'name' => __( 'Disable &quot;Other Projects&quot; navigation', 'gp' ),
    //         'desc' => __( 'Check to hide navigation at the end of each project.' , 'gp' ),
    //         'id' => 'gp_disable_other_projects',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    // $options[] = array(
    //         'name' => __( 'Upscale images to fit screen height', 'gp' ),
    //         'desc' => __( 'Check to allow image upscaling in order to make them fill the whole screen. This affects horizontal portfolio, projects, blog posts and vertical blog.' , 'gp' ),
    //         'id' => 'gp_allow_image_upscaling',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    // $options[] = array(
    //         'name' => __( 'Show navigation arrows on Full Page Slider', 'gp' ),
    //         'desc' => __( 'Check to always show navigation arrows on Full Page Slider. If not checked arrows and titles will be only shown when mouse is hovering slide image.' , 'gp' ),
    //         'id' => 'gp_show_slider_arrows',
    //         'std' => '0',
    //         'type' => 'checkbox'
    //     );

    return $options;
}


/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */
function optionsframework_option_name() {

    // This gets the theme name from the stylesheet
    $themename = get_option( 'stylesheet' );
    $themename = preg_replace("/\W/", "_", strtolower($themename) );

    $optionsframework_settings = get_option( 'optionsframework' );
    $optionsframework_settings['id'] = $themename;
    update_option( 'optionsframework', $optionsframework_settings );

}


/**
 * ------------------------------------------------------------------------------------
 * The functions below implement Theme Options.
 * ------------------------------------------------------------------------------------
 */


/**
 * Tracking code
 */
function gp_tracking_code() {
    $option = of_get_option( 'gp_tracking_code' );
    if ( ! empty( $option ) ) {
        echo '<script>' . $option . '</script>';
    }
}

if ( ! is_admin() && ! is_preview() ) {
    add_action( 'wp_footer', 'gp_tracking_code', 1000 );
}


/**
 * Custom Background CSS3
 */
function gp_bg_css3() {
    $option = of_get_option( 'gp_bg_css3' );
    if ( ! empty( $option ) ) {
        echo "<style>\nbody{" . $option . "}\n</style>\n";
    }
}

function gp_bg_image() {
    $option = of_get_option( 'gp_bg_image' );
    $gp_bg_image_100_percent = of_get_option( 'gp_bg_image_100_percent' );
    $gp_bg_image_repeat = of_get_option( 'gp_bg_image_repeat' );
    
    // echo "<pre>"; print_r($option); echo "</pre>";

    if($gp_bg_image_100_percent == true) {
        // do the 100% background with option background fill or position it middle
        $_cover = '; background-size: cover;';
    }
    if ( ! empty( $option ) ) {
        echo "<style>\nbody{background: ".$option['color']."  url(".$option['image'].") ".$option['repeat']." ".$option['position']." ".$option['attachment']." ".$_cover."}\n</style>\n";
    }
}

function gp_bg_color() {
    $option = of_get_option( 'gp_bg_color' );
    if ( ! empty( $option ) ) {
        echo "<style>\nbody{background-color:" . $option . "}\n</style>\n";
    }
}

function gp_body_background() {
    $option = of_get_option( 'gp_body_background' );
    // echo $option;
    switch($option) {
        case 'bg_css3':
            gp_bg_css3();
            break;
        case 'bg_color':
            gp_bg_color();
            break;
        case 'bg_image':
            gp_bg_image();
            break;
        default:
            add_action( 'wp_head', 'gp_bg_color' );
    }
}

if ( ! is_admin() ) {
    // global $gp_theme;
    // print_r($gp_theme->options);
    // $option = of_get_option( 'gp_body_background' );
    add_action( 'wp_head', 'gp_body_background' );        
}

/**
 * Custom CSS
 */
function gp_custom_css() {
    $option = of_get_option( 'gp_custom_css' );
    if ( ! empty( $option ) ) {
        echo "<style>\n" . $option . "\n</style>\n";
    }
}

if ( ! is_admin() ) {
    add_action( 'wp_head', 'gp_custom_css' );
}


/**
 * Favicon
 */
function gp_favicon() {
    $option = of_get_option( 'gp_favicon' );
    if ( ! empty( $option ) ) {
        echo '<link rel="shortcut icon" href="' . esc_url( $option ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'gp_favicon' );


function gp_options_init() {

   global $gp_theme;

    $disable_meta = of_get_option( 'gp_disable_meta_tags' );
    if ( $disable_meta ) {

        $gp_theme->options['enable_meta_description'] = false;
        $gp_theme->options['enable_meta_image'] = false;

    } else {

        // Page Thumbnail
        $option = of_get_option( 'gp_facebook_image' );
        if ( $option && ! is_single() ) {
            $gp_theme->set_image( $option );
        }

        // Page Description
        $option = of_get_option( 'gp_site_description' );
        if ( $option && ! is_single() ) {
            $gp_theme->set_description( $option );
        }

    }

}
add_action( 'init', 'gp_options_init' );


/**
 * Share widget
 */
function gp_get_social_share( $args = array() ) {

    if ( is_404() ) {
        return false;
    }

    $option = of_get_option( 'gp_share_enabled' );

    if ( $option && $option == '1' ) {

        $share_services = of_get_option( 'gp_share_services' );
        $data_services = array();

        if ( is_array( $share_services ) && count( $share_services ) ) {

            foreach ( $share_services as $key => $service ) {
                if ( $service ) {
                    $key = $key == 'googleplus' ? 'googlePlus' : $key;
                    $data_services[] = $key;
                }
            }

        }

        if ( count( $data_services ) == 0 ) {
            return false;
        }

        $defaults = array(
                'data-url' => array(
                        esc_url( get_permalink() )
                    ),
                'data-curl' => array(
                        esc_url( get_template_directory_uri() . '/lib/sharrre.php' )
                    ),
                'data-services' => array(
                        join( ',', $data_services )
                    ),
                'data-title' => array(
                        __( 'Share', 'gp' )
                    ),
                'class' => array(
                        'sharrre'
                    )
            );

        $args = array_merge( $defaults, $args );

        $html = '<div' . it_array_to_attributes( $args ) . '></div>';

        return $html;

    } else {

        return false;

    }

}


/**
 * Share widget that is located in the footer.
 */
function gp_footer_social_share() {
    $args = array(
            'id' => 'sharrre-footer',
            'data-buttons-title' => __( 'Share this page', 'gp' )
        );
    $html = gp_get_social_share( $args );
    if ( $html ) {
        echo $html;
    }
}

if ( !is_admin() && !is_404() ) {
    add_action( 'footer_social', 'gp_footer_social_share' );
}


/**
 * Social networks
 */
function gp_social_networks() {

    $option = of_get_option( 'gp_social_enabled' );

    if ( $option && $option == '1' ) {

        $html = '';

        foreach ( gp_get_social_networks() as $network) {

            $option = of_get_option( 'gp_' . $network . '_url' );
            $title = esc_attr( sprintf( __( 'Connect on %s', 'gp' ), ucfirst( $network ) ) );

            if ( !empty( $option ) ) {
                $html .= '<a class="icon-social icon-' . $network . '-circled" href="' . esc_url ( $option ) . '" target="_blank" title="' . $title . '" rel="nofollow"></a>';
            }

        }

        if ( !empty( $html ) ) : ?>
            <div class="social-networks"><?php echo $html; ?></div><?php
        endif;

    }

}

if ( ! is_admin() ) {
    add_action( 'footer_social', 'gp_social_networks' );
}


/**
 * CSS Stylesheet
 */
function gp_css_stylesheet() {

    $color_css = of_get_option( 'gp_stylesheet' );

    if ( $color_css ) {
        wp_enqueue_style( 'gp-color', get_template_directory_uri() . '/css/skins/' . (string) $color_css );
    }

}

if ( ! is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'gp_css_stylesheet', 1000 );
}


/**
 * This function is called when saving custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_gp_logo( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * This function is called when saving RETINA custom logo.
 * It will try to retrieve logo size and save it for later use.
 */
function of_update_option_gp_logo_retina( $value, $id ) {
    return of_update_image_option( $value, $id );
}


/**
 * Retrieves image size and saves it as transient.
 */
function of_update_image_option( $value, $id ) {

    if ( $value ) {

        $size = getimagesize( $value );

        if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) &&
             is_numeric( $size[0] ) && is_numeric( $size[1] ) &&
             $size[0] && $size[1] ) {

            set_transient( 'gp_option_' . $id, array( $size[0], $size[1] ) );

        } else {

            /**
             * If we are unable to set image data, then store the error.
             */
            set_transient( 'gp_option_' . $id, 'unable to get image data' );

        }

    } else {

        delete_transient( 'gp_option_' . $id );

    }

    return $value;

}


/**
 * Returns an array with logo information: url, size.
 */
function gp_get_logo() {

    $logo = of_get_option( 'gp_logo' );

    $output = array();

    if ( $logo ) {

        $output[0] = $logo;

        $size = get_transient( 'gp_option_gp_logo' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'gp_logo' );
            $size = get_transient( 'gp_option_gp_logo' );
        }

        if ( is_array( $size ) ) {

            $output[1] = $size[0];
            $output[2] = $size[1];
            $output[3] = 'width="' . esc_attr( $size[0] ) . '"';
            $output[4] = 'height="' . esc_attr( $size[1] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else {

            // populate array with empty values
            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}


/**
 * Returns an array with RETINA logo information: url, size.
 */
function gp_get_logo_retina() {

    $logo = of_get_option( 'gp_logo_retina' );

    $output = array();

    if ( $logo ) {

        $output[0] = $logo;

        $size = get_transient( 'gp_option_gp_logo_retina' );

        /**
         * If transient does not exist, then let's try to set it.
         */
        if ( $size === false ) {
            of_update_image_option( $output[0], 'gp_logo_retina' );
            $size = get_transient( 'gp_option_gp_logo_retina' );
        }

        if ( is_array( $size ) ) {

            $output[1] = round( $size[0] / 2 );
            $output[2] = round( $size[1] / 2 );
            $output[3] = 'width="' . esc_attr( $output[1] ) . '"';
            $output[4] = 'height="' . esc_attr( $output[2] ) . '"';
            $output['size'] = ' ' . $output[3] . ' ' . $output[4] . ' ';

        } else {

            // populate array with empty values
            $output[1] = '';
            $output[2] = '';
            $output[3] = '';
            $output[4] = '';
            $output['size'] = '';

        }

    }

    return $output;

}

