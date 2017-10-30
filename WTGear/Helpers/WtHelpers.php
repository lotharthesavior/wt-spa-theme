<?php
/**
 * Words Tree Helpers
 *
 * @author Savio Resende <savio@savioresende.com.br>
 *
 */

namespace WTGear\Helpers;

class WtHelpers
{

    /**
     * @param string $string
     * @return string
     */
    public static function slugFromString(string $string): string
    {
        $string = str_replace(" ", "-", strtolower($string));
        return $string;
    }

    /**
     * Get book taxonomy hierarchically for a post
     *
     * @internal The book hierarchy will only accepts one element per level
     * @param int $post_id
     * @param \WP_Term|0 $parent
     */
    public static function showPostBooksTaxonomiesHierarchical( $parent = 0 )
    {
        if( is_int($parent) ) {

            $book_parent = wp_get_post_terms(
                get_the_ID(),
                'book',
                [
                    'parent' => 0
                ]
            );
            $book_parent = reset($book_parent);

            $url = get_term_link($book_parent->term_id);

            echo "<a href='" . $url . "'>" . $book_parent->name . "</a> > ";

            self::showPostBooksTaxonomiesHierarchical($book_parent);

        } else {

            // specify if there is another level
            $next = false;

            $url = "";

            $book_children = wp_get_post_terms(
                get_the_ID(),
                'book',
                [
                    'parent' => $parent->term_id
                ]
            );
            $book_children = reset($book_children);

            $book_children_children = wp_get_post_terms(
                get_the_ID(),
                'book',
                [
                    'parent' => $book_children->term_id
                ]
            );

            if (!empty($book_children_children)) {
                $url = get_term_link($parent->term_id);
                $next = true;
            }

            // if there is a url, there is a next level, so there is a link
            // and an arrow
            if (!empty($url)) {

                echo "<a href='" . $url . "'>" . $book_children->name . "</a> > ";

            } else {

                echo $book_children->name;

            }

            if ($next) {

                self::showPostBooksTaxonomiesHierarchical($book_children);

            }

        }
    }

    /**
     * Show only the book taxonomy link
     *
     * @param int $post_id
     */
    public static function showBookTaxonomyForPost()
    {
        $book_parent = wp_get_post_terms(
            get_the_ID(),
            'book',
            [
                'parent' => 0
            ]
        );

        if( empty($book_parent) )
            return;

        $book_parent = reset($book_parent);

        $url = get_term_link($book_parent->term_id);

        echo "<a href='" . $url . "'>" . $book_parent->name . "</a>";
    }

}