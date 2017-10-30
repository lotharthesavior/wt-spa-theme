<?php

/**
 *
 * WT Theme Filters
 *
 * This Class works as a central place for Filters for this theme.
 *
 * @author Savio Resende <savio@savioresende.com.br>
 * @internal originally maintained on oceanwp-child-wt
 */

namespace WTGear\Features;


class WTThemeFilters
{

    /**
     * Register Filters
     */
    public static function start()
    {
        add_filter('ocean_blog_meta_choices', ['\\WTGear\\Features\\WTThemeFilters', 'addBooksMeta']);
        add_filter('woocommerce_account_menu_items', ['\\WTGear\\Features\\WTThemeFilters', 'wtDoRemoveMyAccountLinks']);
        add_filter('wp_editor_settings', ['\\WTGear\\Features\\WTThemeFilters', 'wt_tinymce_settings']);
    }

    /**
     * Customize TinyMCE for WT
     *
     * @param array $settings
     * @return array
     */
    public static function wt_tinymce_settings( $settings ) {
        $settings['paste_data_images'] = true;
        $settings['paste_remove_styles'] = true;
        $settings['quicktags'] = false;
        return $settings;
    }

    /**
     * Add your own meta
     * By default, this new meta will be disabled, so you need to enable it in the customizer.
     *
     * @internal registered with: add_filter
     * @internal hook: ocean_blog_meta_choices
     * @param $return
     * @return mixed
     */
    public static function addBooksMeta($return)
    {
        // Add your meta name
        $return['books'] = esc_html__('Books', 'oceanwp');

        // Return
        return $return;
    }

    /**
     * Manage the items in the Woocommerce menu
     *
     * @internal registered with: add_filter
     * @internal hook: woocommerce_account_menu_items
     * @param $menu_links
     * @return mixed
     */
    public static function wtDoRemoveMyAccountLinks($menu_links)
    {
//        Default items ---
        unset($menu_links['edit-address']); // Addresses
//        unset( $menu_links['dashboard'] ); // Dashboard
        unset( $menu_links['payment-methods'] ); // Payment Methods
        unset( $menu_links['orders'] ); // Orders
        unset( $menu_links['downloads'] ); // Downloads
//        unset( $menu_links['edit-account'] ); // Account details
//        unset( $menu_links['customer-logout'] ); // Logout

        $menu_items = wp_get_nav_menu_items("My Account extra items");
        foreach ($menu_items as $key => $menu_item) {
            $menu_links = \WTGear\WTThemePlugin::addMenuItemToMyAccount($menu_item, $menu_links);
        }

        return $menu_links;
    }

}