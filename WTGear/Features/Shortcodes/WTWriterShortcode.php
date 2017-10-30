<?php
/**
 * Words Tree Writer Shortcode
 *
 * Globals Used:
 *
 * - $wt_theme_dir
 * - $terms
 * - $posts
 * - $breadcrumb
 * - $chapter
 * - $book
 *
 *
 * @author Savio Resende <savio@savioresende.com.br>
 */

namespace WTGear\Features\Shortcodes;

use WTGear\Repositories\Books;
use WTGear\Repositories\Interfaces\CollectionInterface;
use WTGear\Repositories\Interfaces\RepositoryInterface;

global $wt_theme_dir;
global $terms;
global $posts;
global $breadcrumb;
global $chapter;
global $book;
global $wt_msg;

$breadcrumb = [];

$wt_msg = new \Plasticbrain\FlashMessages\FlashMessages();

class WTWriterShortcode implements Interfaces\WTShortcodeInterface
{
    protected $statement_cache = [];

    // templates

    protected $summary_template = "templates/writer/summary.php";

    protected $terms_summary_template = "templates/writer/terms_summary.php";

    protected $book_editor_template = "templates/writer/book_form.php";

    protected $editor_template = "templates/writer/writer.php";

    protected $chapter_template = "templates/writer/partials/chapter.php";

    protected $term_template = "templates/writer/partials/term.php";

    protected $breadcrumb_template = "templates/writer/partials/breadcrumb.php";

    // --


    /**
     * This keeps
     *
     * @var RepositoryInterface
     */
    private $main_repository;

    /**
     * This attribute keeps the GET request params
     *
     * @var
     */
    protected $get_request;

    /**
     * This attribute keeps the POST request params
     *
     * @var
     */
    protected $post_request;

    public function __construct()
    {
        $this->main_repository = new Books();
    }

    /**
     * Receive the request (Router)
     *
     * @return void
     */
    public function run()
    {
        if (
            !empty($_GET)
            && isset($_GET['action'])
        ) {

            $this->get_request = $_GET;
            $this->post_request = $_POST;

            $action = sanitize_text_field($this->get_request['action']);

            switch ($action) {

                case 'search-chapters':

                    $this->prepareBreacrumb($action);

                    break;

                case "edit-book":

                    $this->prepareBreacrumb($action);

                    break;

                // TODO: do this recursive!!!
                case 'edit-chapter':

                    $this->prepareBreacrumb($action);

                    break;

                case 'persist-chapter':

                    // go to $this->doAction()

                    break;

                case 'persist-book':

                    // go to $this->doAction()

                    break;

                case "delete-chapter":

                    break;

                case "delete-book":

                    break;

                default:

                    $this->doAction("search-books");

                    return;

                    break;

            }

            return $this->doAction($action);

        } else {

            return $this->doAction("search-books");

        }

    }

    /**
     * Require the template. This method is important to handle in a central place
     * the global wt_theme_dir.
     *
     * @param string $template
     * @throws \Exception
     * @return void
     */
    public function returnTemplate(string $template)
    {
        global $wt_theme_dir,
               $chapter,
               $wp,
               $wt_msg,
               $terms,
               $posts,
               $book;

        if (!isset($this->{$template}))
            throw new \Exception("Invalid template.");

//        $this->prepareTemplateData(); // TODO: prepare data for the template

        ob_start();
        require $wt_theme_dir . $this->{$template};
        return ob_get_clean();
    }

    /**
     * Run the procedure according to the Rule on the self::start();
     *
     * @param string $action
     * @param array $args
     * @return void
     */
    public function doAction(string $action)
    {
        global $wp,
               $wt_msg;

        switch ($action) {

            case "search-books":

                return $this->searchBooks();

                break;

            case "edit-book":

                return $this->editBook();

                break;

            case "search-chapters":

                return $this->searchChapters();

                break;

            case "edit-chapter":

                $version = \WTThemePlugin::getOceanWPVersion();

                // menu style (for the blogger oceanwp demo)
                wp_enqueue_style('child-style2', get_stylesheet_directory_uri() . '/style-blogger-myaccount.css', array('oceanwp-style'), $version);

                return $this->editChapter();

                break;

            case "persist-book":

                try {

                    $book_id = $this->persistBook();

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-book',
                        'book' => $book_id
                    ], $wp->request));

                    $wt_msg->success("Book Saved!");

                } catch (\Exception $e){

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-book',
                        'book' => $book_id
                    ], $wp->request));

                    $wt_msg->error("Problem on saving book! (error: " . $e->getMessage() . ")");

                }

                ob_start();
                echo "<script>window.location.href = '" . $redirect_location . "'</script>";
                return ob_get_clean();
                exit;

                break;

            case "persist-chapter":

                try {

                    $chapter_id = $this->persistChapter();

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-chapter',
                        'chapter' => $chapter_id
                    ], $wp->request));

                    $wt_msg->success("Text Saved!");

                } catch (\Exception $e){

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-chapter',
                        'chapter' => $chapter_id
                    ], $wp->request));

                    $wt_msg->error("Problem on saving text! (error: " . $e->getMessage() . ")");

                }

                ob_start();
                echo "<script>window.location.href = '" . $redirect_location . "'</script>";
                return ob_get_clean();

                break;

            case "delete-book":

                $book_id = sanitize_text_field($this->get_request['book']);

                if( !$book_id )
                    throw new \Exception("Problem on deleting the book: missing book id!");

                $term_result = wp_delete_term(
                    $book_id,
                    'book',
                    [
                        'author' => get_current_user_id()
                    ]
                );

                if( $term_result !== true )
                    throw new \Exception("Problem on deleting the book: process failed!");

                $redirect_location = home_url(add_query_arg([], $wp->request));

                ob_start();
                echo "<script>window.location.href = '" . $redirect_location . "'</script>";
                return ob_get_clean();

                break;

            case "delete-chapter":

                $chapter_id = sanitize_text_field($this->get_request['chapter']);

                $result = wp_delete_post($chapter_id);

                if( !$result )
                    $wt_msg->error("Problem on saving text! (error: " . $e->getMessage() . ")");
                else
                    $wt_msg->success("Item deleted!");

                $redirect_location = home_url(add_query_arg([
                    'action' => 'edit-chapter',
                    'chapter' => $chapter_id
                ], $wp->request));

                ob_start();
                echo "<script>window.location.href = '" . $redirect_location . "'</script>";
                return ob_get_clean();

                break;

        }

    }

    /**
     * Search by Books Taxonomy and print the summary template
     *
     * @param array $args
     * @return void
     */
    private function searchBooks()
    {
        global $terms;

        $statement = [
            'taxonomy' => 'book',
            'order'    => 'ASC',
            'hide_empty' => false,
            'parent'   => 0,
            'user_id'  => get_current_user_id()
        ];

        $terms = $this->executeSearchOnMainRepository($statement);

        return $this->returnTemplate('terms_summary_template');

    }

    /**
     * Search by Books Chapters and print the summary template
     *
     * @param array $args
     * @return void
     */
    private function searchChapters()
    {
        global $posts;

        if( !isset($this->get_request['book']) )
            throw new \Exception("Invalid Book ID!");

        $book_id = sanitize_text_field($this->get_request['book']);

        $statement = [
            'post_type' => 'post',
            'posts_per_page' => '10',
            'author' => get_current_user_id(),
            'order_by' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'book',
                    'field' => 'term_id',
                    'terms' => $book_id
                )
            )
        ];

        $posts = $this->executeSearchOnMainRepository($statement);

        return $this->returnTemplate('summary_template');
    }

    /**
     * Search the current Chapter and call the editor template.
     *
     * @return void
     */
    private function editChapter()
    {
        global $chapter;

        if( isset($this->get_request['chapter']) ) {

            $chapters_collection = $this->placeChapterSearchForEdition();

            $chapter = $chapters_collection->current();

        } else {

            $chapter = new \stdClass();

        }

        return $this->returnTemplate('editor_template');
    }

    /**
     * Search the current Chapter and call the editor template.
     *
     * @return void
     */
    private function editBook()
    {
        global $book;

        if( isset($this->get_request['book']) ) {

            $books_collection = $this->placeBookSearchForEdition();

            $book = $books_collection->current();

        } else {

            $book = new \stdClass();

        }

        return $this->returnTemplate('book_editor_template');
    }

    /**
     * Place the search for the edition of the chapter.
     *
     * @param int|null $chapter_id
     * @return CollectionInterface
     */
    private function placeChapterSearchForEdition( $chapter_id = null )
    {
        if( isset($this->get_request['chapter']) && is_null($chapter_id) )
            $chapter_id = sanitize_text_field($this->get_request['chapter']);

        $statement = [
            'p' => $chapter_id,
            'post_type' => 'post',
            'posts_per_page' => '1',
            'author' => get_current_user_id(),
            'order_by' => 'date',
            'order' => 'DESC'
        ];

        return $this->executeSearchOnMainRepository($statement);
    }

    /**
     * Place the search for the edition of the book.
     *
     * @param int|null $book_id
     * @return CollectionInterface
     */
    private function placeBookSearchForEdition( $book_id = null )
    {
        if( isset($this->get_request['book']) && !is_null($this->get_request['book']) )
            $book_id = sanitize_text_field($this->get_request['book']);

        $statement = [
            'taxonomy' => 'book',
            'term_id'  => $book_id,
            'order'    => 'ASC',
            'hide_empty' => false,
            'parent'   => 0,
            'user_id'  => get_current_user_id()
        ];

        return $this->executeSearchOnMainRepository($statement);
    }

    /**
     * This method executes the prepared search on this main Repository
     *
     * @param array $statement
     * @return CollectionInterface
     */
    private function executeSearchOnMainRepository( array $statement ) : CollectionInterface
    {
        $key = md5(json_encode($statement));

        if( !isset($this->statement_cache[ $key ]) )
            $this->statement_cache[$key] = $this->main_repository->search($statement);

        return $this->statement_cache[ $key ];
    }

    /**
     * Prepare the breadcrumbs according to the $action
     *
     * @param string $action
     * @return void
     */
    private function prepareBreacrumb(string $action)
    {
        global $breadcrumb,
               $wp,
               $terms;

        switch( $action ){

            case 'search-chapters':

                $books_list_url = home_url(add_query_arg([],$wp->request));

                $book_id = sanitize_text_field($this->get_request['book']);

                // TODO: centralize the place for get terms
                $statement = [
                    'taxonomy' => 'book',
                    'term_id'  => $book_id,
                    'order'    => 'ASC',
                    'hide_empty' => false,
                    'parent'   => 0,
                    'user_id'  => get_current_user_id()
                ];

                $terms = $this->executeSearchOnMainRepository($statement);

                if( !$terms->valid() )
                    throw new \Exception("Book not found!");

                $book_object = $terms->current();

                array_push($breadcrumb, [
                    'value' => 'Books',
                    'link' => $books_list_url
                ]);

                array_push($breadcrumb, [
                    'value' => $book_object->name . '\'s Chapters',
                    'link' => ''
                ]);

                break;

            case "edit-book":

                $books_list_url = home_url(add_query_arg([],$wp->request));

                array_push($breadcrumb, [
                    'value' => 'Books',
                    'link' => $books_list_url
                ]);

                array_push($breadcrumb, [
                    'value' => 'Book Creation',
                    'link' => ''
                ]);

                break;

            case 'edit-chapter':

                $books_list_url = home_url(add_query_arg([],$wp->request));

                if( isset($this->get_request['chapter']) ) {

                    $chapters_collection = $this->placeChapterSearchForEdition();

                    $book_array = wp_get_post_terms(
                        $chapters_collection->current()->id,
                        'book',
                        [
                            'parent' => 0
                        ]
                    );

                    $book_object = reset($book_array);

                    $current_breadcrumb_item = [
                        'value' => $chapters_collection->current()->post_title,
                        'link' => ''
                    ];

                } else if( isset($this->get_request['book']) ) {

                    $book_id = sanitize_text_field($this->get_request['book']);

                    $statement = [
                        'taxonomy' => 'book',
                        'term_id'  => $book_id,
                        'order'    => 'ASC',
                        'hide_empty' => false,
                        'parent'   => 0,
                        'user_id'  => get_current_user_id()
                    ];

                    $terms = $this->executeSearchOnMainRepository($statement);

                    if( !$terms->valid() )
                        throw new \Exception("Book not found!");

                    $book_object = $terms->current();

                    $current_breadcrumb_item = [
                        'value' => "New Chapter",
                        'link' => ''
                    ];

                } else {

                    throw new \Exception("Missing book or chapter parameters!");

                }

                if( !$book_object )
                    throw new \Exception("Book not found!");

                $chapters_list_url = home_url(add_query_arg([
                    'action' => 'search-chapters',
                    'book' => $book_object->term_id
                ],$wp->request));

                array_push($breadcrumb, [
                    'value' => 'Books',
                    'link' => $books_list_url
                ]);

                array_push($breadcrumb, [
                    'value' => $book_object->name . '\'s Chapters',
                    'link' => $chapters_list_url
                ]);

                array_push($breadcrumb, $current_breadcrumb_item);

                break;

        }
    }

    /**
     * Persist to the database
     *
     */
    private function persistChapter()
    {
        $book_id = sanitize_text_field($this->post_request['high_level_term']);

        $term_id = $this->checkTermExistence();

        $chapter_request = [
            'ID'           => $this->post_request['id'],
            'post_author'  => get_current_user_id(),
            'post_content' => $this->post_request['wt-editor-content'],
            'post_title'   => $this->post_request['post_title'],
            'post_status'  => 'publish',
            'tax_input'    => [
                'book' => [
                    $term_id,
                    $book_id
                ]
            ]
        ];
//        echo "<pre>";var_dump($chapter_request);exit;

        $chapter_request = sanitize_post($chapter_request);

        $persistence_result = wp_insert_post($chapter_request);

        if( !$persistence_result || is_wp_error($persistence_result) )
            throw new \Exception( (is_wp_error($persistence_result)?$persistence_result->get_error_message():'Problem on saving the Text.') );

        return $persistence_result;
    }

    /**
     * Persist to the database
     *
     */
    private function persistBook()
    {
        $name = sanitize_text_field($this->post_request['book_name']);
        $book_id = sanitize_text_field($this->post_request['book_id']);

        // TODO: check if the current user has permissions!

        if( empty($book_id) ) {

            $term_id = wp_insert_term(
                $name,
                'book',
                [
                    'user_id' => get_current_user_id()
                ]
            );

        } else {

            $term_id = wp_update_term(
                $book_id,
                'book',
                [
                    'name' => $name
                ]
            );

        }

        if( !$term_id || is_wp_error($term_id) )
            throw new \Exception( (is_wp_error($term_id)?$term_id->get_error_message():'Problem on saving the Text.') );

        return (int) $term_id['term_id'];
    }

    /**
     * Check the existence of a post, and compare the title with the term,
     * if the title changed from the original, it will remove it before procede,
     * so it gets created again with the new name.
     *
     * @return array|int|\WP_Error
     */
    private function removeExistentTermIfPostTitleChanged()
    {
        $book_id = sanitize_text_field($this->post_request['high_level_term']);
        $post_title = sanitize_text_field($this->post_request['post_title']);
        $post_id = sanitize_text_field($this->post_request['id']);

        $original_post = [];

        if( !empty($post_id) )
            $original_post = $this->placeChapterSearchForEdition( $post_id );

        $pre_existent_low_level_term = get_terms([
            'taxonomy' => 'book',
            'parent'   => $book_id,
            'order'    => 'ASC',
            'name'     => $post_title,
            'hide_empty' => false,
            'user_id'  => get_current_user_id()
        ]);

        if( empty($original_post) )
            return $pre_existent_low_level_term;

        if(
            $original_post->valid()
            && $original_post->current()->post_title != $post_title
        ) {
            $this->removeTerm($pre_existent_low_level_term[0]->term_id);
            return 0;
        }

        return $pre_existent_low_level_term;
    }

    private function removeTerm( $term_id )
    {
        throw new \Exception("Build this method!");
    }

    /**
     * Check the existence of a term of a post, if it doesn't exist,
     * create it.
     *
     * @return int|\WP_Error
     * @throws \Exception
     */
    private function checkTermExistence()
    {
        $pre_existent_low_level_term = $this->removeExistentTermIfPostTitleChanged();

        if( empty($pre_existent_low_level_term) ){

            $book_id = sanitize_text_field($this->post_request['high_level_term']);
            $post_title = sanitize_text_field($this->post_request['post_title']);

            $term_id = wp_insert_term(
                $post_title,
                'book',
                [
                    'parent' => $book_id,
                    'user_id' => get_current_user_id()
                ]
            );

            if( is_wp_error($term_id) ){
                throw new \Exception("Problem creating the chapter term.");
            }

            return (int) $term_id['term_id'];
        }

        return reset($pre_existent_low_level_term)->term_id;
    }

}