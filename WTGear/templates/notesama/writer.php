<?php
/**
 * Template for Words Tree - Writer
 *
 * @author Savio Resende <savio@savioresende.com.br>
 */

$this->returnTemplate('breadcrumb_template');

$this_note_url = home_url(add_query_arg([
    'action' => 'persist-note'
],$wp->request));

// --
$post_id      = "";
$post_title   = "";
$post_content = "";
$book_terms   = [];
if( is_a($note, 'Repositories\Entities\Note') ){
    $post_id      = $note->id;
    $post_title   = $note->post_title;
    $post_content = $note->post_content;
    $book_terms   = $note->book_terms;
}
// --

$wt_msg->display();

?>

<form name="writer-form" id="writer-form" action="<?php echo $this_note_url; ?>" method="post">

    <input name="id" type="hidden" value="<?php echo $post_id; ?>"/>

    <div class="wt-shortcode-header">
        <a class="wt-header-shortcode-btn button" onclick="jQuery('#writer-form').submit();">Save</a>

        <div class="cleaner"></div>
    </div>

    <div class="cleaner"></div>

    <div class="wt-writer-input-container">
        <input type="text" name="post_title" placeholder="Title" class="wt-writer-input" value="<?php echo $post_title; ?>" />
    </div>

    <?php

    $settings = [
        'drag_drop_upload' => false,
        'media_buttons' => false
    ];

    wp_editor( $post_content, "wt-editor-content", $settings);

    ?>

</form>
