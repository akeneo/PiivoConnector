<?php

namespace Piivo\Bundle\ConnectorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnexpectedResultException;


/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class VersionRepository extends EntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function searchAfterOffset(array $criteria, array $orders, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('r');

        foreach ($criteria as $field => $criterion) {
            $qb->andWhere($qb->expr()->eq(sprintf('r.%s ', $field), $qb->expr()->literal($criterion)));
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $criteria = [])
    {
        try {
            $qb = $this->createQueryBuilder('r');

            foreach ($criteria as $field => $criterion) {
                $qb->andWhere($qb->expr()->eq(sprintf('r.%s ', $field), $qb->expr()->literal($criterion)));
            }

            return (int) $qb
                ->select('COUNT(r.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }
}
