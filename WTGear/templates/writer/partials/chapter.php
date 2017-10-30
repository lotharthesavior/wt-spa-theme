<?php

/**
 * Chapter template
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 * @internal This file is to always run inside the WTWriterShortcode context
 */

global $post,
       $wp;

$book_id = sanitize_text_field($this->get_request['book']);

$this_chapter_url = home_url(add_query_arg([
    'action' => 'edit-chapter',
    'chapter' => $post->id,
    'book' => $book_id
],$wp->request));

$this_chapter_delete_url = home_url(add_query_arg([
    'action' => 'delete-chapter',
    'chapter' => $post->id,
    'book' => $book_id
],$wp->request));

?>

<div class="flex-container">

<article
    id="post-<?php echo $post->id; ?>"
    class="blog-entry clr isotope-entry col span_1_of_4 grid-entry col-3 post-<?php echo $post->id; ?> post type-post status-publish format-standard has-post-thumbnail hentry category-lifestyle tag-lifestyle tag-woman entry has-media flex-item">

    <div class="blog-entry-inner clr">

        <header class="blog-entry-header clr">
            <h2 class="blog-entry-title entry-title">
                <a href="<?php echo $this_chapter_url; ?>" title="Dapibus diam sed nisi nulla quis sem" rel="bookmark"><?php echo $post->post_title; ?></a>
            </h2><!-- .blog-entry-title -->
        </header><!-- .blog-entry-header -->

        <div class="blog-entry-summary clr" itemprop="text">

            <?php echo substr(strip_tags($post->post_content), 0, 150) . ( (strlen(strip_tags($post->post_content)) > 150)?"...":"" ); ?>

        </div><!-- .blog-entry-summary -->

        <footer class="wt-summary-item-options">

            <a class="wt-summary-item-options-link" href="<?php echo $this_chapter_url; ?>"><span class="dashicons dashicons-edit"></span></a>

            <a class="wt-summary-item-options-link"
               onclick="javascript:if(confirm('Do you really want to delete this Item?')){return true;}else{return false;}"
               href="<?php echo $this_chapter_delete_url; ?>"><span class="dashicons dashicons-trash"></span></a>

        </footer>

    </div><!-- .blog-entry-inner -->

</article>
