<?php

namespace Piivo\Bundle\ConnectorBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class VersionRepository extends EntityRepository
{
    /**
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        parent::__construct($em, new ClassMetadata($class));
    }

    /**
     * {@inheritdoc}
     */
    public function searchAfterOffset($entityClass, array $criteria = [])
    {
        $qb = $this->buildQueryBuilder($entityClass, $criteria);

        return $qb
            ->getQuery()
            ->execute();
    }

    /**
     * @param array $criteria
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function buildQueryBuilder($entityClass, array $criteria)
    {
        $qb = $this->createQueryBuilder('r');
        $qb
            ->select('r.snapshot')
            ->andWhere(
                $qb->expr()->eq('r.resourceName', $qb->expr()->literal($entityClass))
            )
            ->andWhere(
                $qb->expr()->eq('r.context', $qb->expr()->literal('Deleted'))
            )
        ;

        foreach ($criteria as $key => $value) {
            $qb->andWhere(
                $qb->expr()->gt(
                    sprintf('r.%s', $key),
                    $qb->expr()->literal($value)
                )
            );
        }

        return $qb;
    }

    /**
     * Returns an array containing the name of the unique identifier properties
     *
     * @return array
     */
    public function getIdentifierProperties()
    {
        return ['id'];
    }
}
