<?php

/**
 * Chapter template
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 * @internal This file is to always run inside the WTWriterShortcode context
 */

global $term,
       $wp;

$this_book_url = home_url(add_query_arg([
    'action' => 'search-chapters',
    'book' => $term->term_id
],$wp->request));

$delete_book_url = home_url(add_query_arg([
    'action' => 'delete-book',
    'book' => $term->term_id
],$wp->request));

$edit_book_url = home_url(add_query_arg([
    'action' => 'edit-book',
    'book' => $term->term_id
],$wp->request));

?>

<article
    id="post-<?php echo $term->term_id; ?>"
    class="blog-entry clr isotope-entry col span_1_of_4 grid-entry col-3 post-<?php echo $term->term_id; ?> post type-post status-publish format-standard has-post-thumbnail hentry category-lifestyle tag-lifestyle tag-woman entry has-media flex-item">

    <div class="blog-entry-inner clr">

        <header class="blog-entry-header clr">
            <h2 class="blog-entry-title entry-title">
                <a href="<?php echo $this_book_url; ?>" rel="bookmark"><?php echo $term->name; ?></a>
            </h2><!-- .blog-entry-title -->
        </header><!-- .blog-entry-header -->

        <footer class="wt-summary-item-options">

            <a class="wt-summary-item-options-link" href="<?php echo $edit_book_url; ?>"><span class="dashicons dashicons-edit"></span></a>

            <a class="wt-summary-item-options-link" href="<?php echo $delete_book_url; ?>"><span class="dashicons dashicons-trash"></span></a>

        </footer>

    </div><!-- .blog-entry-inner -->

</article>
