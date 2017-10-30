<?php
/**
 * Writer Summary
 *
 * @Savio Resende <savio@savioresende.com.br>
 *
 * @internal This file is to always run inside the WTWriterShortcode context
 */

$this->returnTemplate('breadcrumb_template');

$book_id = sanitize_text_field($this->get_request['book']);

$this_book_url = home_url(add_query_arg([
    'action' => 'edit-chapter',
    'book' => $book_id
],$wp->request));

$wt_msg->display();

?>

<div class="wt-shortcode-header">
    <h2>Chapters</h2>

    <a class="wt-header-shortcode-btn button" href="<?php echo $this_book_url; ?>">New Chapter</a>

    <div class="cleaner"></div>
</div>

<div class="flex-container">
<?php

while ($posts->valid())
{
    global $post;

    $key = $posts->key();

    $post = $posts->current();

    $this->returnTemplate('chapter_template');

    $posts->next();
}

?>
</div>
