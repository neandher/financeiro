<?php

namespace AppBundle\Repository;

use AppBundle\Helper\PaginationHelper;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * BillPlanRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BillPlanRepository extends AbstractEntityRepository
{
    protected function queryLatest(PaginationHelper $paginationHelper)
    {
        $routeParams = $paginationHelper->getRouteParams();

        $qb = $this->createQueryBuilder('billPlan')
            ->innerJoin('billPlan.billPlanType', 'billPlanType')
            ->addSelect('billPlanType');

        if (isset($routeParams['search'])) {
            $qb->andWhere('billPlan.description LIKE :search')->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!isset($routeParams['sorting'])) {
            $qb->orderBy('billPlan.id', 'desc');
        } else {
            $qb = $this->addOrderingQueryBuilder($qb, $paginationHelper);
        }
        return $qb->getQuery();
    }

    public function findLatest(PaginationHelper $paginationHelper)
    {
        $routeParams = $paginationHelper->getRouteParams();

        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest($paginationHelper), false));

        $paginator->setMaxPerPage($routeParams['num_items']);
        $paginator->setCurrentPage($routeParams['page']);

        return $paginator;
    }

    public function queryLatestForm($billPlanId)
    {
        return $this->createQueryBuilder('billPlan')
            ->innerJoin('billPlan.billPlanType', 'billPlanType')
            ->addSelect('billPlanType')
            ->innerJoin('billPlanType.billType', 'billType')
            ->addSelect('billType')
            ->andWhere('billType.id = ' . $billPlanId . '')
            ->orderBy('billPlanType.description', 'ASC')
            ->addOrderBy('billPlan.description', 'ASC');
    }
}
