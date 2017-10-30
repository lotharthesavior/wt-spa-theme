<?php
/**
 * Words Tree Shortcode Interface
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 * Expected API
 *
 */

namespace WTGear\Features\Shortcodes\Interfaces;

interface WTShortcodeInterface
{

    /**
     * Receive the request and route it
     *
     * @return void
     */
    public function run();

    /**
     * Run the procedure according to the Rule on the self::start();
     *
     * @param string $action
     * @return void
     */
    public function doAction( string $action);

    /**
     * Require the template
     *
     * @param string $template
     * @return void
     */
    public function returnTemplate( string $template );

}