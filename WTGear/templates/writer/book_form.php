<?php
/**
 * Template for Words Tree - Writer
 *
 * @author Savio Resende <savio@savioresende.com.br>
 */

$this->returnTemplate('breadcrumb_template');

//if( substr($wp->request, -1) != '/' )
//    $wp->request = $wp->request . '/';

$this_book_url = home_url(add_query_arg([
    'action' => 'persist-book'
],$wp->request));

// --
$term_id = "";
$name    = "";

if(
    is_a($book, 'Repositories\Entities\Book')
    || is_a($book, 'WP_Term')
){
    $term_id = $book->term_id;
    $name    = $book->name;
}
// --

$wt_msg->display();

?>

<form name="writer-form" id="writer-form" action="<?php echo $this_book_url; ?>" method="post">

    <input name="book_id" type="hidden" value="<?php echo $term_id; ?>"/>

    <div class="wt-shortcode-header">
        <a class="wt-header-shortcode-btn button" onclick="jQuery('#writer-form').submit();">Save</a>

        <div class="cleaner"></div>
    </div>

    <div class="cleaner"></div>

    <div class="wt-writer-input-container">
        <input type="text" name="book_name" placeholder="Name" class="wt-writer-input" value="<?php echo $name; ?>" />
    </div>

</form>
