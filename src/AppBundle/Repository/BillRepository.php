<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BillStatus;
use AppBundle\Helper\PaginationHelper;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
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
            ->innerJoin('bill.bank', 'bank')
            ->addSelect('bank')
            ->innerJoin('bill.billCategory', 'billCategory')
            ->addSelect('billCategory')
            ->innerJoin('bill.billInstallments', 'billInstallments')
            ->addSelect('billInstallments')
            ->groupBy('bill.id');

        $qb = $this->filters($qb, $routeParams);

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

    private function filters(QueryBuilder $qb, $routeParams = [])
    {
        if (!empty($routeParams['search'])) {
            $qb->andWhere('bill.description LIKE :search')->setParameter('search', '%' . $routeParams['search'] . '%');
        }

        if (!empty($routeParams['bill_category'])) {
            $qb->andWhere('billCategory.id = :bill_category')->setParameter('bill_category', $routeParams['bill_category']);
        }

        if (!empty($routeParams['bill_status'])) {
            $qb->andWhere('billStatus.id = :bill_status')->setParameter('bill_status', $routeParams['bill_status']);
        }

        if (!empty($routeParams['bank'])) {
            $qb->andWhere('bank.id = :bank')->setParameter('bank', $routeParams['bank']);
        }

        if (!empty($routeParams['bill_status_desc'])) {

            if ($routeParams['bill_status_desc'] == BillStatus::BILL_STATUS_PAGO) {

                $qb->andWhere('billInstallments.amountPaid is not null');

                if (!empty($routeParams['billYear'])) {
                    $qb->andWhere('year(billInstallments.paymentDateAt) = :year')->setParameter(':year', $routeParams['billYear']);
                }

                if (!empty($routeParams['billMonth'])) {
                    $qb->andWhere('month(billInstallments.paymentDateAt) = :month')->setParameter(':month', $routeParams['billMonth']);
                }
            }

            if ($routeParams['bill_status_desc'] == BillStatus::BILL_STATUS_EM_ABERTO) {

                $qb->andWhere('billInstallments.amountPaid is null');

                if (!empty($routeParams['billYear'])) {
                    $qb->andWhere('year(billInstallments.dueDateAt) = :year')->setParameter(':year', $routeParams['billYear']);
                }

                if (!empty($routeParams['billMonth'])) {
                    $qb->andWhere('month(billInstallments.dueDateAt) = :month')->setParameter(':month', $routeParams['billMonth']);
                }
            }
        }

        if (!empty($routeParams['date_start']) && !empty($routeParams['date_end'])) {

            $date_start = \DateTime::createFromFormat('d-m-Y', $routeParams['date_start'])->format('Y-m-d');
            $date_end = \DateTime::createFromFormat('d-m-Y', $routeParams['date_end'])->format('Y-m-d');

            $qb->andWhere('billInstallments.dueDateAt >= :date_start')->setParameter('date_start', $date_start);
            $qb->andWhere('billInstallments.dueDateAt <= :date_end')->setParameter('date_end', $date_end);
        }

        if (!empty($routeParams['overdue'])) {
            $qb->andWhere('billInstallments.dueDateAt <= :now')->setParameter('now', new \DateTime())
                ->andWhere('billInstallments.amountPaid IS NULL');
        }

        if (!empty($routeParams['sum_amount']) && $routeParams['sum_amount'] === true) {
            $qb->select("SUM(CAST( replace( replace( billInstallments.amount,'.','' ),',','.' )  AS DECIMAL( 13,2 ) )) as amountTotal");
        }

        if (!empty($routeParams['sum_amount_paid']) && $routeParams['sum_amount_paid'] === true) {
            $qb->select("SUM(CAST( replace( replace( billInstallments.amountPaid,'.','' ),',','.' )  AS DECIMAL( 13,2 ) )) as amountPaidTotal");
        }

        return $qb;
    }

    public function dashboard($routeParams = [])
    {
        $qb = $this->createQueryBuilder('bill')
            ->innerJoin('bill.billPlan', 'billPlan')
            ->addSelect('billPlan')
            ->innerJoin('billPlan.billPlanCategory', 'billPlanCategory')
            ->addSelect('billPlanCategory')
            ->innerJoin('bill.billStatus', 'billStatus')
            ->addSelect('billStatus')
            ->innerJoin('bill.bank', 'bank')
            ->addSelect('bank')
            ->innerJoin('bill.billCategory', 'billCategory')
            ->addSelect('billCategory')
            ->innerJoin('bill.billInstallments', 'billInstallments')
            ->addSelect('billInstallments')
            ->addOrderBy('billInstallments.dueDateAt', 'ASC');

        $qb = $this->filters($qb, $routeParams);

        return $qb->getQuery()->getArrayResult();
    }

    public function balancePreviousMonth($params)
    {
        $conn = $this->getEntityManager()
            ->getConnection();

        $sql = "SELECT SUM(
                case bins.amountPaid when NULL then 
                     CAST( replace( replace( bins.amount,'.','' ),',','.' )  AS DECIMAL( 13,2 ) )
                else 
                    CAST( replace( replace( bins.amountPaid,'.','' ),',','.' )  AS DECIMAL( 13,2 ) ) 
                end
                ) as previous_balance
                FROM bill 
                inner join bill_installments as bins on bins.bill_id = bill.id
                WHERE YEAR(bins.dueDateAt) < '" . $params['year'] . "' AND bins.amountPaid IS NOT NULL ORDER BY bins.dueDateAt ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function cashFlow($params)
    {
        $where = "bill.id > 0 ";

        if(!empty($params['y'])){
            $where .= " and YEAR(bins.dueDateAt) = '".$params['y']."' ";
        }

        if(!empty($params['bank'])){
            $where .= " and bank.id = '".$params['bank']."' ";
        }

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
                inner join bank on bank.id = bill.bank_id
                inner join bill_installments as bins on bins.bill_id = bill.id
                WHERE ".$where."
                GROUP BY bill.id,bill.description,bipl.id,bipl.description,biplc.id,bica.id,bins.dueDateAt,bist.referency
                ORDER BY bins.dueDateAt ASC,biplc.description ASC,bipl.description ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findOneById($id)
    {
        return $this->createQueryBuilder('bill')
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
            ->innerJoin('bill.billInstallments', 'billInstallments')
            ->addSelect('billInstallments')
            ->leftJoin('bill.billFiles', 'billFiles')
            ->addSelect()
            ->where('bill.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByParams($params)
    {
        $qb = $this->createQueryBuilder('bill')
            ->innerJoin('bill.billCategory', 'billCategory')
            ->addSelect('billCategory')
            ->innerJoin('bill.billPlan', 'billPlan')
            ->addSelect('billPlan')
            ->innerJoin('bill.bank', 'bank')
            ->addSelect('bank')
            ->innerJoin('bill.billInstallments', 'billInstallments')
            ->addSelect('billInstallments');

        if (!empty($params['billCategory'])) {
            $qb->where('billCategory.id = :category')->setParameter(':category', $params['billCategory']);
        }

        if (!empty($params['billPlan'])) {
            $qb->andWhere('billPlan.id = :plan')->setParameter(':plan', $params['billPlan']);
        }

        if (!empty($params['billBank'])) {
            $qb->andWhere('bank.id = :bank')->setParameter(':bank', $params['billBank']);
        }

        if (!empty($params['billStatus'])) {

            if ($params['billStatus'] == 'paid') {

                $qb->andWhere('billInstallments.amountPaid is not null');

                if (!empty($params['billYear'])) {
                    $qb->andWhere('year(billInstallments.paymentDateAt) = :year')->setParameter(':year', $params['billYear']);
                }

                if (!empty($params['billMonth'])) {
                    $qb->andWhere('month(billInstallments.paymentDateAt) = :month')->setParameter(':month', $params['billMonth']);
                }
            }

            if ($params['billStatus'] == 'not_paid') {

                $qb->andWhere('billInstallments.amountPaid is null');

                if (!empty($params['billYear'])) {
                    $qb->andWhere('year(billInstallments.dueDateAt) = :year')->setParameter(':year', $params['billYear']);
                }

                if (!empty($params['billMonth'])) {
                    $qb->andWhere('month(billInstallments.dueDateAt) = :month')->setParameter(':month', $params['billMonth']);
                }
            }
        }

        if (!empty($params['datePaymentIsNull'])) {
            $qb->andWhere('billInstallments.paymentDateAt is null');
        }

        if (!empty($params['latest_days'])) {
            $qb->andWhere("billInstallments.dueDateAt <= NOW() and billInstallments.dueDateAt >= DATE_SUB(CURRENT_TIME(), :latest_days, 'DAY')")->setParameter('latest_days', $params['latest_days']);
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function getAmountTotal($params = [])
    {
        $qb = $this->createQueryBuilder('bill')
            ->innerJoin('bill.billStatus', 'billStatus')
            ->innerJoin('bill.billCategory', 'billCategory')
            ->innerJoin('bill.billInstallments', 'billInstallments')
            ->innerJoin('bill.bank', 'bank');

        $qb = $this->filters($qb, $params);

        return $qb->getQuery()->getSingleScalarResult();
    }
}