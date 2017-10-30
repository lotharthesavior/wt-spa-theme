<?php
/**
 * WT Theme Structure
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

namespace WTGear;

class WTThemePlugin
{

    public static $menu_items_to_be_registered = [
        [
            'location' => 'wt_myaccount_menu',
            'description' => [
                'Words Tree My Account Menu',
                'wt-myaccount-menu'
            ]
        ]
    ];

    /**
     * @internal hook: register_activation_hook
     * @return void
     */
    public static function install()
    {
        // TODO: check OceanWP installation
        // TODO: check Woocommerce installation
        // TODO: check other dependencies installation
        // TODO: create book taxonomy
    }

    /**
     * @internal hook: register_deactivation_hook
     * @return void
     */
    public static function uninstall()
    {
        // TODO: offer options to keep the data somewhere
    }

    /**
     * Start theme
     */
    public static function start()
    {
        self::restrictAdminBarAccess();
    }

    /**
     * Make restrict the access to Admin Top Bar
     */
    public static function restrictAdminBarAccess()
    {
        $current_user = wp_get_current_user();
        if( !in_array("administrator", $current_user->roles) ) {
            show_admin_bar(false);
        }
    }

    /**
     *
     */
    public static function handleRegistration()
    {
        register_activation_hook(__FILE__, ['\\WTGear\\WTThemePlugin', 'install']);
        register_deactivation_hook(__FILE__, ['\\WTGear\\WTThemePlugin', 'uninstall']);
    }

    /**
     * Populate the content of custom page.
     *
     * @param string $menu_item_endpoint
     * @return void
     */
    public static function addContentToEndpoint(string $menu_item_endpoint)
    {
        $args = array(
            'name'        => $menu_item_endpoint,
            'post_type'   => 'page',
            'post_status' => 'publish',
            'numberposts' => 1
        );
        $my_posts = get_posts($args);

        if( count($my_posts) > 0 ){
            echo do_shortcode($my_posts[0]->post_content);
        }
    }

    /**
     * Add item to My Account's page
     *
     * @internal called by wt_do_remove_my_account_links
     * @param WP_Post $menu_item
     * @param array $menu_links
     */
    public static function addMenuItemToMyAccount(\WP_Post $menu_item, array $menu_links)
    {
        $extra_page = [
            \WTGear\Helpers\WtHelpers::slugFromString($menu_item->title) => $menu_item->title
        ];

        $key = array_keys($extra_page)[0];

        $menu_links[$key] = $extra_page[$key];

        return $menu_links;
    }

}