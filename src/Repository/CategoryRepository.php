<?php

namespace Piivo\Bundle\ConnectorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pim\Bundle\ApiBundle\Doctrine\ORM\Repository\ApiResourceRepository;
use Pim\Component\Api\Repository\PageableRepositoryInterface;
use Pim\Component\Catalog\Query\Filter\Operators;


/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class CategoryRepository extends ApiResourceRepository
{
    public function searchAfterOffset(array $criteria, array $orders, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('r');

        foreach ($criteria as $field => $criterion) {
            switch ($criterion['operator']) {
                case Operators::IS_EMPTY:
                    $qb->andWhere(
                        $qb->expr()->isNull(sprintf('r.%s', $field))
                    );
                    break;
                default:
                    $qb->andWhere($qb->expr()->eq(sprintf('r.%s', $field), $qb->expr()->literal($criterion)));
                    break;
            }
        }

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

    public function count(array $criteria = [])
    {
        // TODO: Implement count() method.
    }

}
