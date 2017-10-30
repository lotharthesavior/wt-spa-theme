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

use Repositories\Notes;
use Repositories\Interfaces\CollectionInterface;
use Repositories\Interfaces\RepositoryInterface;

global $wt_theme_dir;
global $terms;
global $posts;
global $breadcrumb;
global $note;
global $wt_msg;

$breadcrumb = [];

$wt_msg = new \Plasticbrain\FlashMessages\FlashMessages();

class WTNotesamaShortcode implements Interfaces\WTShortcodeInterface
{
    protected $statement_cache = [];

    // templates

    protected $summary_template = "templates/notesama/summary.php";

    protected $terms_summary_template = "templates/notesama/terms_summary.php";

    protected $book_editor_template = "templates/notesama/book_form.php";

    protected $editor_template = "templates/notesama/writer.php";

    protected $note_template = "templates/notesama/partials/note.php";

    protected $term_template = "templates/notesama/partials/term.php";

    protected $breadcrumb_template = "templates/notesama/partials/breadcrumb.php";

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
        $this->main_repository = new Notes();
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

                case 'search-note':

                    $this->prepareBreacrumb($action);

                    break;

                // TODO: do this recursive!!!
                case 'edit-note':

                    $this->prepareBreacrumb($action);

                    break;

                case 'persist-note':

                    break;

                case "delete-note":

                    break;

                default:

                    return $this->doAction("search-note");

                    return;

                    break;

            }

            return $this->doAction($action);

        } else {

            return $this->doAction("search-note");

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
               $wp,
               $wt_msg,
               $terms,
               $posts,
               $note;

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

            case "search-note":

                return $this->searchNotesama();

                break;

            case "edit-note":

                $version = \WTThemePlugin::getOceanWPVersion();

                // menu style (for the blogger oceanwp demo)
                wp_enqueue_style('child-style2', get_stylesheet_directory_uri() . '/style-blogger-myaccount.css', array('oceanwp-style'), $version);

                return $this->editNote();

                break;

            case "persist-note":

                try {

                    $note_id = $this->persistNote();

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-note',
                        'notesama' => $note_id
                    ], $wp->request));

                    $wt_msg->success("Text Saved!");

                } catch (\Exception $e){

                    $redirect_location = home_url(add_query_arg([
                        'action' => 'edit-note',
                        'notesama' => $note_id
                    ], $wp->request));

                    $wt_msg->error("Problem on saving text! (error: " . $e->getMessage() . ")");

                }

                ob_start();
                echo "<script>window.location.href = '" . $redirect_location . "'</script>";
                return ob_get_clean();

                break;

            case "delete-note":

                $note_id = sanitize_text_field($this->get_request['notesama']);

                $result = wp_delete_post($note_id);

                if( !$result )
                    $wt_msg->error("Problem on saving text! (error: " . $e->getMessage() . ")");
                else
                    $wt_msg->success("Item deleted!");

                $redirect_location = home_url(add_query_arg([
                    'action' => 'search-note'
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
    private function searchNotesama()
    {
        global $posts;

        $statement = [
            'post_type' => 'note',
            'posts_per_page' => '10',
            'author' => get_current_user_id(),
            'order_by' => 'date',
            'order' => 'DESC',
//            'tax_query' => array(
//                array(
//                    'taxonomy' => 'notesama',
//                    'field' => 'term_id',
//                    'terms' => $book_id
//                )
//            )
        ];

        $posts = $this->executeSearchOnMainRepository($statement);

        return $this->returnTemplate('summary_template');
    }

    /**
     * Search the current Chapter and call the editor template.
     *
     * @return void
     */
    private function editNote()
    {
        global $note;

        if( isset($this->get_request['notesama']) ) {

            $notes_collection = $this->placeNoteSearchForEdition();

            $note = $notes_collection->current();

        } else {

            $note = new \stdClass();

        }

        return $this->returnTemplate('editor_template');
    }

    /**
     * Place the search for the edition of the chapter.
     *
     * @param int|null $note_id
     * @return CollectionInterface
     */
    private function placeNoteSearchForEdition( $note_id = null )
    {
        if( isset($this->get_request['notesama']) && is_null($note_id) )
            $note_id = sanitize_text_field($this->get_request['notesama']);

        $statement = [
            'p' => $note_id,
            'post_type' => 'note',
            'posts_per_page' => '1',
            'author' => get_current_user_id(),
            'order_by' => 'date',
            'order' => 'DESC'
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

            case 'search-notes':

                $notes_list_url = home_url(add_query_arg([],$wp->request));

                array_push($breadcrumb, [
                    'value' => 'Notes',
                    'link' => $notes_list_url
                ]);

                break;

            case 'edit-note':

                $notes_list_url = home_url(add_query_arg([],$wp->request));

                if( isset($this->get_request['notesama']) ) {

                    $notes_collection = $this->placeNoteSearchForEdition();

                    $current_breadcrumb_item = [
                        'value' => $notes_collection->current()->post_title,
                        'link' => ''
                    ];

                } else {

                    $current_breadcrumb_item = [
                        'value' => 'New Note',
                        'link' => ''
                    ];

                }

                array_push($breadcrumb, [
                    'value' => 'Notes',
                    'link' => $notes_list_url
                ]);

                array_push($breadcrumb, $current_breadcrumb_item);

                break;

        }
    }

    /**
     * Persist to the database
     *
     */
    private function persistNote()
    {
        $note_request = [
            'ID'           => $this->post_request['id'],
            'post_author'  => get_current_user_id(),
            'post_content' => $this->post_request['wt-editor-content'],
            'post_title'   => $this->post_request['post_title'],
            'post_status'  => 'publish',
            'post_type'    => 'note',
        ];

        $note_request = sanitize_post($note_request);

        $persistence_result = wp_insert_post($note_request);

        if( !$persistence_result || is_wp_error($persistence_result) )
            throw new \Exception( (is_wp_error($persistence_result)?$persistence_result->get_error_message():'Problem on saving the Text.') );

        return $persistence_result;
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
            $original_post = $this->placeNoteSearchForEdition( $post_id );

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