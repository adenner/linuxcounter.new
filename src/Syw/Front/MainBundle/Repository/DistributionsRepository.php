<?php

namespace Syw\Front\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class DistributionsRepository
 *
 * @author Alexander Löhner <alex.loehner@linux.com>
 */
class DistributionsRepository extends EntityRepository
{
    public function findByNot($field, $value)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where($qb->expr()->not($qb->expr()->eq('a.'.$field, '?1')));
        $qb->setParameter(1, $value);

        return $qb->getQuery()
            ->getResult();
    }
}
