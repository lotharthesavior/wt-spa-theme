<?php
/**
 * Created by PhpStorm.
 * User: savioresende
 * Date: 2017-10-08
 * Time: 4:42 PM
 */

namespace WTGear\Repositories;

use Interfaces\EntityInterface;
use Interfaces\CollectionInterface;

class Notes implements Interfaces\RepositoryInterface
{

    /**
     * Find element by ID, but this will depend on the element.
     *
     * e.g.: for Books, this method search by the name of the
     *       taxonomy, returning a summary of posts
     *
     * @param int $id
     * @return EntityInterface
     */
    public function findById(int $id): EntityInterface
    {
//        $arrayData = $this->persistence->retrieve($id);
//
//        if (is_null($arrayData)) {
//            throw new \InvalidArgumentException(sprintf('Post with ID %d does not exist', $id));
//        }
//
//        return Post::fromState($arrayData);
    }

    /**
     * Persist Item. This method is only useful for entities like "post",
     * "taxonomies", "categories", "user", etc...
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $post): bool
    {
//        $id = $this->persistence->persist([
//            'text' => $post->getText(),
//            'title' => $post->getTitle(),
//        ]);
//
//        $post->setId($id);
    }

    /**
     * Search elements based in an array args (dictionary like)
     *
     * This method will choose between taxonomy or posts depending ont
     * he arguments.
     *
     * @param array $args
     * @return CollectionInterface
     */
    public function search(array $args) : CollectionInterface
    {
        return $this->searchPosts( $args );
    }

    /**
     * Execute the search for posts
     *
     * @param array $args
     * @return CollectionInterface
     */
    private function searchPosts( array $args ) : CollectionInterface
    {
        $note_collection = new Collections\NoteCollection();

        $query = new \WP_Query($args);

        if (!$query->have_posts()) {

            $note_collection->loadTraversable([]);

        } else {

            $posts_result = $query->get_posts();

            $note_collection->loadTraversable($posts_result);

        }

        return $note_collection;
    }

}