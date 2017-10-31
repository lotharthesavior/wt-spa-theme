<?php
/**
 * Writer Summary
 *
 * @Savio Resende <savio@savioresende.com.br>
 *
 * @internal This file is to always run inside the WTWriterShortcode context
 */

// TODO: sanitize this GET
//var_dump($_GET);exit;
$get_items = implode('/', array_merge($_GET, [
    'action' => 'edit-book'
]));
//var_dump($get_items);exit;

//$this_book_url = home_url(add_query_arg([
//    'action' => 'edit-book'
//],$wp->request));
//var_dump($this_book_url);exit;

//$this_book_url = $this_book_url . '&' . $get_items;
//var_dump($this_book_url);exit;

$wt_msg->display();

?>

<div class="wt-shortcode-header">
    <h2>Books</h2>

    <a class="wt-header-shortcode-btn button" href="#/<?php echo $get_items; ?>">New Book</a>

    <div class="cleaner"></div>
</div>

<div class="flex-container">

<?php

while ($terms->valid())
{
    global $term;

    $key = $terms->key();

    $term = $terms->current();

//    echo "<pre>";var_dump($term);exit;

    $this->returnTemplate('term_template');

    $terms->next();
}

?>

</div>
