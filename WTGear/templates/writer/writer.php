<?php
/**
 * Template for Words Tree - Writer
 *
 * @author Savio Resende <savio@savioresende.com.br>
 */

$this->returnTemplate('breadcrumb_template');

$this_chapter_url = home_url(add_query_arg([
    'action' => 'persist-chapter'
],$wp->request));

// --
$post_id      = "";
$post_title   = "";
$post_content = "";
$book_terms   = [];
if( is_a($chapter, 'Repositories\Entities\Chapter') ){
    $post_id      = $chapter->id;
    $post_title   = $chapter->post_title;
    $post_content = $chapter->post_content;
    $book_terms   = $chapter->book_terms;
}
// --

if( isset($this->get_request['book']) )
    $book_id = sanitize_text_field($this->get_request['book']);

$chapter_terms = $book_terms;

$low_level_chapter_terms = reset($chapter_terms);

$wt_msg->display();

?>

<form name="writer-form" id="writer-form" action="<?php echo $this_chapter_url; ?>" method="post">

    <input name="id" type="hidden" value="<?php echo $post_id; ?>"/>

    <?php if( !empty($book_id) ){ ?>
        <input name="high_level_term" type="hidden" value="<?php echo $book_id; ?>" />
    <?php } ?>

    <input name="low_level_term" type="hidden" value="<?php echo ( isset($low_level_chapter_terms->term_id) ) ? $low_level_chapter_terms->term_id : "" ; ?>" />

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
