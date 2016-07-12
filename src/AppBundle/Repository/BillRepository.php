<?php

namespace AppBundle\Repository;

use AppBundle\Helper\PaginationHelper;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * BillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BillRepository extends AbstractEntityRepository
{
    protected function queryLatest(PaginationHelper $paginationHelper)
    {
        $routeParams = $paginationHelper->getRouteParams();

        $qb = $this->createQueryBuilder('bill')
            ->innerJoin('bill.billPlan', 'billPlan')
            ->addSelect('billPlan')
            ->innerJoin('billPlan.billPlanCategory', 'billPlanCategory')
            ->addSelect('billPlanCategory')
            ->innerJoin('bill.billStatus', 'billStatus')
            ->addSelect('billStatus')
            ->leftJoin('bill.bank', 'bank')
            ->addSelect('bank')
            ->innerJoin('bill.billCategory', 'billCategory')
            ->addSelect('billCategory')
            ->leftJoin('bill.billInstallments', 'billInstallments')
            ->addSelect('billInstallments');

        if (!empty($routeParams['search'])) {
            $qb->andWhere('bill.description LIKE :search')->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!empty($routeParams['bill_category'])) {
            $qb->andWhere('billCategory.id = :bill_category')->setParameter('bill_category', $routeParams['bill_category']);
        }

        if (!empty($routeParams['bill_status'])) {
            $qb->andWhere('billStatus.id = :bill_status')->setParameter('bill_status', $routeParams['bill_status']);
        }

        if ((isset($routeParams['date_start']) && !empty($routeParams['date_start'])) && (isset($routeParams['date_end']) && !empty($routeParams['date_end']))) {

            $date_start = \DateTime::createFromFormat('d-m-Y', $routeParams['date_start'])->format('Y-m-d');
            $date_end = \DateTime::createFromFormat('d-m-Y', $routeParams['date_end'])->format('Y-m-d');

            $qb->andWhere('billInstallments.dueDateAt >= :date_start')->setParameter('date_start', $date_start);
            $qb->andWhere('billInstallments.dueDateAt <= :date_end')->setParameter('date_end', $date_end);
        }

        if (!isset($routeParams['sorting'])) {
            $qb->orderBy('bill.id', 'desc');
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

    public function balancePreviousMonth($params)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "SELECT SUM( CAST( replace( replace( bins.amountPaid,'.','' ),',','.' )  AS DECIMAL( 13,2 ) ) ) as previous_balance
                FROM bill 
                inner join bill_installments as bins
                WHERE YEAR(bins.dueDateAt) < '".$params['year']."' AND bins.amountPaid IS NOT NULL ORDER BY bins.dueDateAt ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function cashFlow($params)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "SELECT bill.id as bill_id,bill.description as bill_description,bipl.id as bipl_id,bipl.description as bipl_description,
                biplc.id as biplc_id,bica.id as bica_id,bins.dueDateAt,bist.referency,
                SUM( CAST( replace( replace( bins.amount,'.','' ),',','.' )  AS DECIMAL( 13,2 ) ) ) as cf_amount, 
                SUM( CAST( replace( replace( bins.amountPaid,'.','' ),',','.' )  AS DECIMAL( 13,2 ) ) ) as cf_amount_paid
                FROM bill
                inner join bill_plan as bipl on bipl.id = bill.bill_plan_id 
                inner join bill_plan_category as biplc on biplc.id = bipl.bill_plan_category_id
                inner join bill_category as bica on bica.id = bill.bill_category_id
                inner join bill_status as bist on bist.id = bill.bill_status_id
                inner join bill_installments as bins on bins.bill_id = bill.id
                WHERE YEAR(bins.dueDateAt) = '".$params['year']."'
                GROUP BY bill.id,bill.description,bipl.id,bipl.description,biplc.id,bica.id,bins.dueDateAt,bist.referency
                ORDER BY bins.dueDateAt ASC,biplc.description ASC,bipl.description ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
