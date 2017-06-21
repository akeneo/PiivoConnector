<?php

namespace Piivo\Bundle\ConnectorBundle\Repository;

use Doctrine\ORM\UnexpectedResultException;
use Pim\Bundle\ApiBundle\Doctrine\ORM\Repository\ApiResourceRepository;
use Pim\Component\Catalog\Query\Filter\Operators;


/**
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class FamilyRepository extends ApiResourceRepository
{
    /**
     * {@inheritdoc}
     */
    public function searchAfterOffset(array $criteria, array $orders, $limit, $offset)
    {
        $qb = $this->buildQueryBuilder($criteria);

        foreach ($orders as $field => $sort) {
            $qb->addOrderBy(sprintf('r.%s', $field), $sort);
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
            $qb = $this->buildQueryBuilder($criteria);

            return (int) $qb
                ->select('COUNT(r.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            return 0;
        }
    }

    /**
     * Builds query builder from criteria
     *
     * @param array $criteria
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function buildQueryBuilder(array $criteria)
    {
        $qb = $this->createQueryBuilder('r');

        foreach ($criteria as $field => $criterion) {
            switch ($criterion['operator']) {
                case Operators::GREATER_THAN:
                    $qb->andWhere(
                        $qb->expr()->gt(sprintf('r.%s', $field), $qb->expr()->literal($criterion['value']))
                    );
                    break;
                default:
                    $qb->andWhere($qb->expr()->eq(sprintf('r.%s', $field), $qb->expr()->literal($criterion['value'])));
                    break;
            }
        }

        return $qb;
    }
}
