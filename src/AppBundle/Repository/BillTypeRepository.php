<?php

namespace AppBundle\Repository;

/**
 * BillTypeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BillTypeRepository extends \Doctrine\ORM\EntityRepository
{
    public function queryLatestForm()
    {
        return $this->createQueryBuilder('billType')
            ->orderBy('billType.description', 'ASC');
    }
}
