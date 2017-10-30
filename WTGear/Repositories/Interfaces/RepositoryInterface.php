<?php
/**
 * Created by PhpStorm.
 * User: savioresende
 * Date: 2017-10-08
 * Time: 4:55 PM
 */

namespace WTGear\Repositories\Interfaces;

interface RepositoryInterface
{
    /**
     * Find element by ID, but this will depend on the element.
     *
     * e.g.: for Books, this method search by the name of the
     *       taxonomy, returning a summary of posts
     *
     * @param int $id
     * @return \Repositories\Interfaces\EntityInterface
     */
    public function findById(int $id): EntityInterface;

    /**
     * Persist Item. This method is only useful for entities like "post",
     * "taxonomies", "categories", "user", etc...
     *
     * @param \Repositories\Interfaces\EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool;

    /**
     * Search elements based in an array args (dictionary like)
     *
     * @param array $args
     * @return \Repositories\Interfaces\CollectionInterface
     */
    public function search(array $args): CollectionInterface;
}