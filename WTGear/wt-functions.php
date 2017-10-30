<?php
/**
 * WT specific functions
 */

if (!session_id()) @session_start();

$wt_theme_dir = plugin_dir_path( __FILE__ );

//var_dump($wt_theme_dir);exit;

require $wt_theme_dir . 'WTThemePlugin.php';

require $wt_theme_dir . '../vendor/autoload.php';

WTGear\WTThemePlugin::handleRegistration();
WTGear\WTThemePlugin::start();
WTGear\Features\WTThemeFilters::start();
WTGear\Features\WTThemeActions::start();

WTGear\Features\WTThemeShortcodes::addWriterShortcode();
