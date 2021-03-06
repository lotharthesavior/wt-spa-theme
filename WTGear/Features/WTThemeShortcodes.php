<?php

/**
 *
 * WT Theme Shortcodes
 *
 * This Class works as a router for Shortcodes.
 *
 * Each Shortcode has it's own workflow.
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

namespace WTGear\Features;

class WTThemeShortcodes
{

    /**
     *
     * @param string $mode
     */
    public static function addWriterShortcode()
    {
        add_shortcode('wt-writer', function ($args) {
            if(!is_array($args)) $args = [$args];

            $wt_writer_shortcode = new Shortcodes\WTWriterShortcode();
            return $wt_writer_shortcode->run($args);
        });

        add_shortcode('wt-notesama', function ($args) {
            if(!is_array($args)) $args = [$args];

            $wt_notesama_shortcode = new Shortcodes\WTNotesamaShortcode();
            return $wt_notesama_shortcode->run($args);
        });
    }

}