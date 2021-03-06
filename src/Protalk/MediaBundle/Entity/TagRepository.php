<?php

namespace Protalk\MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    /**
     * Get the most used tags
     *
     * @param int $max
     * @return Doctrine Collection
     */
    public function getMostUsedTags($max = 20)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('t.slug', 't.name', 'COUNT(m.id) as mediaCount');
        $qb->from('\Protalk\MediaBundle\Entity\Tag', 't');
        $qb->join('t.medias', 'm');
        $qb->where('m.isPublished = 1');
        $qb->groupBy('t.slug');
        $qb->orderBy('mediaCount', 'DESC');
        $qb->setMaxResults($max);

        $query = $qb->getQuery();
        return $query->execute();
    }
}