<?php
namespace Nimble;
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/* ------------------------------------------------------------------------- *
*  REGISTER PAGE
/* ------------------------------------------------------------------------- */
// function nb_register_settings() {
//    add_option( 'myplugin_option_name', 'This is my option value.');
//    register_setting( 'myplugin_options_group', 'myplugin_option_name', '\Nimble\myplugin_callback' );
// }
// add_action( 'admin_init', '\Nimble\nb_register_settings' );

function nb_register_options_page() {
  if ( !sek_current_user_can_access_nb_ui() )
    return;
  add_options_page(
    apply_filters( 'nb_admin_settings_title', __('Nimble Builder', 'nimble-builder') ),
    apply_filters( 'nb_admin_settings_title', __('Nimble Builder', 'nimble-builder') ),
    'manage_options',
    NIMBLE_OPTIONS_PAGE,
    '\Nimble\nb_options_page'
  );
}
add_action( 'admin_menu', '\Nimble\nb_register_options_page');

// callback of add_options_page()
// fired @'admin_menu'
function nb_options_page() {
  $option_tabs = Nimble_Manager()->admin_option_tabs;
  $active_tab_id = nb_get_active_option_tab();
  $default_title = esc_html( get_admin_page_title() );
  $page_title = isset( $option_tabs[$active_tab_id] ) ? $option_tabs[$active_tab_id]['page_title'] : $default_title;
  $page_title = empty($page_title) ? $default_title : $page_title;
  ?>

  <div id="nimble-options" class="wrap">
      <h1 class="nb-option-page-title">
        <?php
        printf('<span class="sek-nimble-title-icon"><img src="%1$s" alt="Build with Nimble Builder">%2$s</span>',
            NIMBLE_BASE_URL.'/assets/img/nimble/nimble_icon.svg?ver='.NIMBLE_VERSION,
            apply_filters( 'nimble_option_title_icon_after', '' )
        );
        echo apply_filters( 'nimble_parse_admin_text', $page_title );
        ?>
      </h1>
      <div class="nav-tab-wrapper">
          <?php
            foreach ($option_tabs as $tab_id => $tab_data ) {
              printf('<a class="nav-tab %1$s" href="%2$s">%3$s</a>',
                  $tab_id === nb_get_active_option_tab() ? 'nav-tab-active' : '',
                  admin_url( NIMBLE_OPTIONS_PAGE_URL ) . '&tab=' . $tab_id,
                  $tab_data['title']
              );
            }
          ?>
      </div>
      <div class="tab-content-wrapper">
        <?php
          $_cb = $option_tabs[$active_tab_id]['content'];
          if( is_string( $_cb ) && !empty( $_cb ) ) {
            if ( function_exists( $_cb ) ) {
              call_user_func( $_cb );
            } else {
              echo $_cb;
            }
          } else if ( is_array($_cb) && 2 == count($_cb) ) {
            if ( is_object($_cb[0]) ) {
              $to_return = call_user_func( array( $_cb[0] ,  $_cb[1] ) );
            }
            //instantiated with an instance property holding the object ?
            else if ( class_exists($_cb[0]) ) {

              /* PHP 5.3- compliant*/
              $class_vars = get_class_vars( $_cb[0] );

              if ( isset( $class_vars[ 'instance' ] ) && method_exists( $class_vars[ 'instance' ], $_cb[1]) ) {
                $to_return = call_user_func( array( $class_vars[ 'instance' ] ,  $_cb[1] ) );
              }

              else {
                $_class_obj = new $_cb[0]();
                if ( method_exists($_class_obj, $_cb[1]) )
                  $to_return = call_user_func( array( $_class_obj, $_cb[1] ) );
              }
            }
          }
        ?>
      <div>
  </div><!-- .wrap -->
  <?php
}


/* ------------------------------------------------------------------------- *
*  ADD SETTINGS LINKS
/* ------------------------------------------------------------------------- */
function nb_settings_link($links) {
    $doc_link = sprintf('<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>', 'https://docs.presscustomizr.com/article/337-getting-started-with-the-nimble-builder-plugin', __('Docs', 'nimble-builder') );
    array_unshift($links, $doc_link );
    $settings_link = sprintf('<a href="%1$s">%2$s</a>',
        add_query_arg( array( 'tab' => 'options' ), admin_url( NIMBLE_OPTIONS_PAGE_URL ) ),
        __('Settings', 'nimble-builder')
    );
    array_unshift($links, $settings_link );
    return $links;
}
add_filter("plugin_action_links_".plugin_basename(NIMBLE_PLUGIN_FILE), '\Nimble\nb_settings_link' );


/* ------------------------------------------------------------------------- *
*  SAVE OPTION HOOK + CUSTOMIZABLE REDIRECTION
/* ------------------------------------------------------------------------- */
// fired @'admin_post'
function nb_save_options() {
    do_action('nb_admin_post');
    //wp_safe_redirect( urldecode( admin_url( NIMBLE_OPTIONS_PAGE_URL ) ) );
    nb_admin_redirect();
}
add_action( 'admin_post', '\Nimble\nb_save_options' );


// fired @'admin_post'
function nb_admin_redirect() {
    $url = sanitize_text_field(
            wp_unslash( $_POST['_wp_http_referer'] ) // Input var okay.
    );
    // Default option url : urldecode( admin_url( NIMBLE_OPTIONS_PAGE_URL ) )
    $url = urldecode( $url );
    $url = empty($url) ? urldecode( admin_url( NIMBLE_OPTIONS_PAGE_URL ) ) : $url;
    // Finally, redirect back to the admin page.
    // Note : filter 'nimble_admin_redirect_url' is used in NB pro to add query params used to display warning/error messages
    wp_safe_redirect( apply_filters('nimble_admin_redirect_url', $url ) );
    exit;
}

// @return bool
function nb_has_valid_nonce( $option_group = 'nb-options-save', $nonce = 'nb-options-nonce' ) {
    // If the field isn't even in the $_POST, then it's invalid.
    if ( !isset( $_POST[$nonce] ) ) { // Input var okay.
        return false;
    }
    return wp_verify_nonce( wp_unslash( $_POST[$nonce] ), $option_group );
}


/* ------------------------------------------------------------------------- *
*  REGISTER TABS
/* ------------------------------------------------------------------------- */
Nimble_Manager()->admin_option_tabs = array();
// @return void
function nb_register_option_tab( $tab ) {
    $tab = wp_parse_args( $tab, array(
        'id' => '',
        'title' => '',
        'page_title' => '',
        'content' => '',
    ));
    Nimble_Manager()->admin_option_tabs[$tab['id']] = $tab;
}



function nb_get_active_option_tab() {
    // check that we have a tab param and that this tab is registered
    $tab_id = isset( $_GET['tab'] ) ? $_GET['tab'] : 'welcome';
    if ( !array_key_exists( $tab_id, Nimble_Manager()->admin_option_tabs ) ) {
        sek_error_log( __FUNCTION__ . ' error => invalid tab');
        $tab_id = 'welcome';
    }
    return $tab_id;
}

/* ------------------------------------------------------------------------- *
*  WELCOME PAGE
/* ------------------------------------------------------------------------- */
nb_register_option_tab([
    'id' => 'welcome',
    'title' => __('Welcome', 'nimble-builder'),
    'page_title' => __('Nimble Builder', 'nimble-builder' ),
    'content' => '\Nimble\print_welcome_page',
]);
function print_welcome_page() {
    ?>
    <div class="nimble-welcome-content">
      <?php echo sek_get_welcome_block(); ?>
    </div>
    <div class="clear"></div>
    <hr/>
    <div>
      <h2><?php _e('Watch the video below for a brief overview of Nimble Builder features', 'nimble-builder'); ?></h2>
      <iframe src="https://player.vimeo.com/video/328473405?loop=1&title=0&byline=0&portrait=0" width="640" height="424" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
    </div>

    <?php
}

/* ------------------------------------------------------------------------- *
*  OPTIONS PAGE
/* ------------------------------------------------------------------------- */
nb_register_option_tab([
    'id' => 'options',
    'title' => __('Options', 'nimble-builder'),
    'page_title' => __('Nimble Builder Options', 'nimble-builder' ),
    'content' => '\Nimble\print_options_page',
]);
function print_options_page() {
    ?>
    <form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <tr>
          <th scope="row"><?php _e('Shortcodes', 'nimble-builder'); ?></th>
          <td>
            <fieldset><legend class="screen-reader-text"><span><?php _e('Shortcodes', 'nimble-builder'); ?></span></legend>
              <?php
                $shortcode_opt_val = get_option( 'nb_shortcodes_parsed_in_czr' );
              ?>
              <label for="nb_shortcodes_parsed_in_czr"><input name="nb_shortcodes_parsed_in_czr" type="checkbox" id="nb_shortcodes_parsed_in_czr" value="on" <?php checked( $shortcode_opt_val, 'on' ); ?>>
              <?php _e('Parse shortcodes when building your pages in the customizer', 'nimble-builder'); ?></label>
              <p class="description"><?php _e('Shortcodes are disabled by default when customizing to prevent any conflicts with Nimble Builder interface.', 'nimble-builder'); ?></p>
            </fieldset>
          </td>
        </tr>
        <tr>
          <th scope="row"><?php _e('Debug Mode', 'nimble-builder'); ?></th>
          <td>
            <fieldset><legend class="screen-reader-text"><span><?php _e('Debug Mode', 'nimble-builder'); ?></span></legend>
              <?php
                $nb_debug_mode_opt_val = get_option( 'nb_debug_mode_active' );
              ?>
              <label for="nb_debug_mode_active"><input name="nb_debug_mode_active" type="checkbox" id="nb_debug_mode_active" value="on" <?php checked( $nb_debug_mode_opt_val, 'on' ); ?>>
              <?php _e('Activate the debug mode when customizing', 'nimble-builder'); ?></label>
              <p class="description"><?php _e('In debug mode, during customization Nimble Builder deactivates all modules content and prints only the structure of your sections. This lets you troubleshoot, remove or edit your modules safely.', 'nimble-builder'); ?></p>
            </fieldset>
          </td>
        </tr>
      </tbody>
    </table>
    <?php
      do_action('nb_admin_options_tab_after_content');
      wp_nonce_field( 'nb-base-options', 'nb-base-options-nonce' );
      submit_button();
    ?>
    </form>
    <?php
}
add_action( 'nb_admin_post', '\Nimble\nb_save_base_options' );
// hook : nb_admin_post
function nb_save_base_options() {
    // First, validate the nonce and verify the user as permission to save.
    if ( !nb_has_valid_nonce( 'nb-base-options', 'nb-base-options-nonce' ) || !current_user_can( 'manage_options' ) )
        return;

    // Shortcode parsing when customizing
    nb_maybe_update_checkbox_option( 'nb_shortcodes_parsed_in_czr', 'off' );
    // Debug mode
    nb_maybe_update_checkbox_option( 'nb_debug_mode_active', 'off' );
}

// helper to update a checkbox option
// the option is updated only if different than the default val or if the option exists already
function nb_maybe_update_checkbox_option( $opt_name, $unchecked_value ) {
    $opt_value = get_option( $opt_name );
    $posted_value = array_key_exists( $opt_name, $_POST ) ? $_POST[$opt_name] : $unchecked_value;
    if ( $unchecked_value !== $posted_value ) {
        update_option( $opt_name, esc_attr( $posted_value ), 'no' );
    } else {
        // if the option was never set before, then leave it not set
        // otherwise update it to 'off'
        if ( false !== $opt_value ) {
            update_option( $opt_name, $unchecked_value, 'no' );
        }
    }
}

do_action('nb_base_admin_options_registered');



/* ------------------------------------------------------------------------- *
*  RESTRICT USERS
/* ------------------------------------------------------------------------- */
//register option tab and print the form
if ( sek_is_pro() || ( defined( 'NIMBLE_PRO_UPSELL_ON') && NIMBLE_PRO_UPSELL_ON ) ) {
    $restrict_users_title = __('Restrict users', 'nimble-builder');
    if ( !sek_is_pro() ) {
        $restrict_users_title = sprintf( '<span class="sek-pro-icon"><img src="%1$s" alt="Pro feature"></span><span class="sek-title-after-icon">%2$s</span>',
            NIMBLE_BASE_URL.'/assets/czr/sek/img/pro_orange.svg?ver='.NIMBLE_VERSION,
            __('Restrict users', 'nimble-builder' )
        );
    }
    nb_register_option_tab([
        'id' => 'restrict_users',
        'title' => $restrict_users_title,
        'page_title' => __('Restrict users', 'nimble-builder' ),
        'content' => '\Nimble\print_restrict_users_options_content',
    ]);

    function print_restrict_users_options_content() {
        if ( !sek_is_pro() ) {
          ?>
            <h4><?php _e('Nimble Builder can be used by default by all users with an administrator role. With Nimble Builder Pro you can decide which administrators are allowed to use the plugin.', 'nimble-builder'); ?></h4>
            <h4><?php _e('Unauthorized users will not see any reference to Nimble Builder when editing a page, in the customizer and in the WordPress admin screens.', 'nimble-builder') ?></h4>
            <a class="sek-pro-link" href="https://presscustomizr.com/nimble-builder-pro/" rel="noopener noreferrer" title="Go Pro" target="_blank"><?php _e('Go Pro', 'nimble-builder'); ?> <span class="dashicons dashicons-external"></span></a>
          <?php
        }
        do_action( 'nb_restrict_user_content' );
    }
}


/* ------------------------------------------------------------------------- *
*  SYSTEM INFO
/* ------------------------------------------------------------------------- */
nb_register_option_tab([
    'id' => 'system-info',
    'title' => __('System info', 'nimble-builder'),
    'page_title' => __('System info', 'nimble-builder' ),
    'content' => '\Nimble\print_system_info',
]);
function print_system_info() {
    require_once( NIMBLE_BASE_PATH . '/inc/admin/system-info.php' );
    ?>
     <h3><?php _e( 'System Informations', 'nimble-builder' ); ?></h3>
      <h4><?php _e( 'Please include your system informations when posting support requests.' , 'nimble-builder' ) ?></h4>
      <textarea readonly="readonly" onclick="this.focus();this.select()" id="system-info-textarea" name="tc-sysinfo" title="<?php _e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'nimble-builder' ); ?>" style="width: 800px;min-height: 800px;font-family: Menlo,Monaco,monospace;background: 0 0;white-space: pre;overflow: auto;display:block;"><?php echo sek_config_infos(); ?></textarea>
    <?php
}

/* ------------------------------------------------------------------------- *
*  DOCUMENTATION
/* ------------------------------------------------------------------------- */
nb_register_option_tab([
    'id' => 'doc',
    'title' => __('Documentation', 'nimble-builder'),
    'page_title' => __('Nimble Builder knowledge base', 'nimble-builder' ),
    'content' => '\Nimble\print_doc_page',
]);
function print_doc_page() {
    ?>
      <div class="nimble-doc">
          <ul>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/337-getting-started-with-the-nimble-builder-plugin"><span>Getting started with Nimble Page Builder for WordPress</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/386-how-to-access-the-live-customization-interface-of-the-nimble-builder"><span>How to access the live customization interface of Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/371-how-to-start-building-from-a-blank-page-with-the-wordpress-nimble-builder"><span>How to start building from a blank ( full width ) page with WordPress Nimble Builder?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href=" https://docs.presscustomizr.com/article/427-how-to-insert-and-edit-a-module-with-nimble-builder"><span>How to insert and edit a module with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/358-building-your-header-and-footer-with-the-nimble-builder"><span>How to build your WordPress header and footer with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/350-how-to-use-shortcodes-from-other-plugins-with-the-nimble-builder-plugin"><span>How to embed WordPress shortcodes in your pages with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/366-how-to-add-an-anchor-to-a-section-and-integrate-it-into-the-menu-with-the-nimble-page-builder"><span>How to add an anchor to a section and integrate it into the menu with Nimble Page Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/380-how-to-set-a-parallax-background-for-a-section-in-wordpress-with-the-nimble-builder"><span>How to set a parallax background for a section in WordPress with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/343-designing-for-mobile-devices-with-wordpress-nimble-builder"><span>Designing for mobile devices with the WordPress Nimble Builder</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/414-nimble-builder-and-website-performances"><span>Nimble Builder and website performance ????</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/393-how-to-add-post-grids-to-any-wordpress-page-with-nimble-builder"><span>How to add post grids to any WordPress page with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/372-design-your-404-page-with-the-nimble-builder"><span>How to design your 404 error page with Nimble Builder</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/391-how-to-export-and-import-templates-with-nimble-builder"><span>How to reuse sections and templates with the export / import feature of Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/401-how-to-create-a-video-background-with-nimble-builder-wordpress-plugin"><span>How to create a video background with Nimble Builder WordPress plugin ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/408-how-to-insert-a-responsive-carousel-in-your-wordpress-pages-with-nimble-builder"><span>How to insert a responsive carousel in your WordPress pages with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/389-how-to-visualize-the-structure-of-the-content-created-with-nimble-builder"><span>How to visualize the structure of the content created with Nimble Builder ?</span></a></li>
            <li><a target="_blank" rel="noopener noreferrer" href="https://docs.presscustomizr.com/article/383-how-to-customize-the-height-of-your-sections-and-columns-with-the-nimble-builder"><span>How to customize the height of your sections and columns with Nimble Builder ?</span></a></li>

          </ul>
        <a href="https://docs.presscustomizr.com" target="_blank" class="button button-primary button-hero" rel="noopener noreferrer"><span class="dashicons dashicons-search"></span>&nbsp;<?php _e('Explore Nimble Builder knowledge base', 'nimble-builder'); ?></a>
      </div>

    <?php
}

?>