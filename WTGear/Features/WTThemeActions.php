<?php

/**
 *
 * WT Theme Actions
 *
 * This Class works as a central place for Actions for this theme.
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

namespace WTGear\Features;

class WTThemeActions
{

    /**
     * Register Actions
     */
    public static function start()
    {
        add_action('wp_enqueue_scripts', ['\\WTGear\\Features\\WTThemeActions', 'wtSpaThemeEnqueueParentStyle']);

        add_action('wp_enqueue_scripts', ['\\WTGear\\Features\\WTThemeActions', 'wtSpaThemeEnqueueScripts']);

        add_action('init', ['\\WTGear\\Features\\WTThemeActions', 'registerWtMenuForExtraLinks']);

        add_action('init', ['\\WTGear\\Features\\WTThemeActions', 'addWtMenuForExtraLinksEndpoint']);

        add_action('init', ['\\WTGear\\Features\\WTThemeActions', 'blockusers_init'] );

        self::registerWtActions();
    }

    /**
     * Block access to wp-admin without administrator user_type
     */
    public static function blockusers_init() {
        if ( is_admin() && ! current_user_can( 'administrator' ) &&
            ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    /**
     * For eventual actions for this theme
     */
    public static function registerWtActions()
    {
//        add_action( 'wt_owp_', ['', ''], 10, 3 );
    }

    /**
     * Load the parent style.css file
     *
     * @internal registered with: add_action
     * @internal hook: wp_enqueue_scripts
     * @link http://codex.wordpress.org/Child_Themes
     */
    public static function wtSpaThemeEnqueueParentStyle()
    {
        // Load the stylesheet
        wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('oceanwp-style'), '');

        // Load Font-Awesome
        wp_register_style('wt-fontawesome', get_template_directory_uri() . '/fonts/font-awesome-4.7.0/css/font-awesome.css', array(), '1.0');
        wp_enqueue_style('wt-fontawesome'); // Enqueue it!
    }

    /**
     * Load JS scripts for the Theme
     */
    public static function wtSpaThemeEnqueueScripts()
    {
        // activate the web-api, which allows the usage of BackboneJS Models
        wp_enqueue_script( 'wp-api' );

        // vue
        wp_register_script('wt-vue-js','https://unpkg.com/vue', array('wp-api'), null, true); // WT
        wp_enqueue_script('wt-vue-js');

        // vue router
        wp_register_script('wt-vue-router-js','https://unpkg.com/vue-router/dist/vue-router.js', array('wp-api'), null, true); // WT
        wp_enqueue_script('wt-vue-router-js');

        // nav
        wp_register_script('wordstreescripts-nav', get_template_directory_uri() . '/js/wt-structure/wt-nav.js', array('wp-api', 'wt-vue-js'), null, true); // WT
        wp_enqueue_script('wordstreescripts-nav');

        // pages
        wp_register_script('wordstreescripts-page', get_template_directory_uri() . '/js/wt-structure/wt-page.js', array('wp-api', 'wt-vue-js', 'wordstreescripts-nav'), null, true); // WT
        wp_enqueue_script('wordstreescripts-page');

        // router
        wp_register_script('wordstreescripts-router', get_template_directory_uri() . '/js/wt-structure/wt-router.js', array('wp-api', 'wt-vue-js', 'wordstreescripts-page', 'wordstreescripts-nav'), null, true); // WT
        wp_enqueue_script('wordstreescripts-router');
    }

    /**
     * Code hidden to register menus
     *
     * @internal registered with: add_action
     * @internal hook: init
     * @return void
     */
    public static function registerWtMenuForExtraLinks()
    {
        foreach (\WTGear\WTThemePlugin::$menu_items_to_be_registered as $menu_item) {
            register_nav_menu($menu_item['location'], __($menu_item['description'][0], $menu_item['description'][1]));
        }
    }

    /**
     * Add Endpoint to registered pages on woocommerce myaccount menu
     *
     * @internal registered with: add_action
     * @internal hook: init
     * @return void
     */
    public static function addWtMenuForExtraLinksEndpoint()
    {
        $menu_items = wp_get_nav_menu_items("My Account extra items");
        foreach ($menu_items as $key => $menu_item) {
            $menu_item_endpoint = \WTGear\Helpers\WtHelpers::slugFromString($menu_item->title);

            add_rewrite_endpoint($menu_item_endpoint, EP_PAGES);

            add_action('woocommerce_account_' . $menu_item_endpoint . '_endpoint', function () use ($menu_item_endpoint) {
                \WTGear\WTThemePlugin::addContentToEndpoint($menu_item_endpoint);
            });
        }
    }

}