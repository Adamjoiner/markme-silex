<?php

namespace MarkMe\Service;

use Doctrine\ORM\EntityRepository;
use MarkMe\Entity\Tag as TagEntity;

/**
 * Tag
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Tag extends EntityRepository implements TagInterface
{
    /**
     * find tag where user has bookmarks tagged
     * @param \MarkMe\Entity\User $user
     */
    function findWhereUserHasBookmark(\MarkMe\Entity\User $user, $tagName)
    {
        $query = $this->createQueryBuilder("t");
        if ($tagName) {
            $query->where('name LIKE = :tagName');
        }
        return $query->innerJoin("t.bookmarks", "b", "WITH", "b.user = :user")
            ->getQuery()
            ->execute(array("user" => $user, 'tagName' => $tagName));
    }

    function create(TagEntity $tag, $flush = true)
    {
        $this->getEntityManager()->persist($tag);
        $flush == TRUE AND $this->getEntityManager()->flush($tag);
        return $tag;
    }

    public function fromName($tagName,$flush=true)
    {
        $tag = $this->findOneBy(array('name' => $tagName));
        if (NULL == $tag) {
            $tag = new TagEntity();
            $tag->setName($tagName);
            $this->create($tag,$flush);
        }
        return $tag;
    }

}
