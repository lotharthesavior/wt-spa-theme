<?php
/**
 * Writer Summary
 *
 * @Savio Resende <savio@savioresende.com.br>
 *
 * @internal This file is to always run inside the WTWriterShortcode context
 */

$this->returnTemplate('breadcrumb_template');

$wt_msg->display();

$this_note_url = home_url(add_query_arg([
    'action' => 'edit-note'
],$wp->request));

?>

<div class="wt-shortcode-header">
    <h2>Notes</h2>

    <a class="wt-header-shortcode-btn button" href="<?php echo $this_note_url; ?>">New Note</a>

    <div class="cleaner"></div>
</div>

<div class="flex-container">
<?php

while ($posts->valid())
{
    global $post;

    $key = $posts->key();

    $post = $posts->current();

    $this->returnTemplate('note_template');

    $posts->next();
}

?>
</div>
