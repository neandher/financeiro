<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Entity\BillPlan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/cashFlow")
 */
class CashFlowController extends Controller
{

    const CF_PAID = 'PAID';
    const CF_NOT_PAID = 'NOT_PAID';
    const CF_PAID_AND_NOT_PAID = 'PAID_AND_NOT_PAID';

    /**
     * @Route("/", name="cash_flow_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $params['year'] = date('Y');

        if ($request->query->has('date') && !empty($request->query->get('date'))) {
            $params['year'] = $request->query->get('date');
        }

        $billPlans = $this->getDoctrine()->getRepository(BillPlan::class)->listBillPlans();

        $previousBalance = $this->getDoctrine()->getRepository(Bill::class)->balancePreviousMonth($params);
        $previousBalance = empty($previousBalance['previous_balance']) || is_null($previousBalance['previous_balance']) ? 0 : $previousBalance['previous_balance'];

        $cashFlow = $this->getDoctrine()->getRepository(Bill::class)->cashFlow($params);

        $cashFlowData = [];
        $cashFlowData['content'] = [];

        $billCategoryIds = array();
        $billPlanCategoryIds = array();
        $billPlanIds = array();

        foreach ($billPlans as $billPlanEntity) {

            /** @var BillPlan $billPlan */
            $billPlan = $billPlanEntity;

            $bill_category_id = $billPlan->getBillPlanCategory()->getBillCategory()->getId();

            if (!in_array($bill_category_id, $billCategoryIds)) {

                $billCategoryIds[] = $bill_category_id;

                $cashFlowData['content'][$bill_category_id] = [
                    "0" => [
                        'description' => $billPlan->getBillPlanCategory()->getBillCategory()->getDescription(),
                        'referency' => $billPlan->getBillPlanCategory()->getBillCategory()->getReferency(),
                        'id' => $billPlan->getBillPlanCategory()->getBillCategory()->getId(),
                    ]
                ];

                foreach ($billPlans as $bpc_billPlanEntity) {

                    /** @var BillPlan $bpc_billPlan */
                    $bpc_billPlan = $bpc_billPlanEntity;

                    $bill_plan_category_id = $bpc_billPlan->getBillPlanCategory()->getId();

                    if (!in_array($bill_plan_category_id, $billPlanCategoryIds) && $bpc_billPlan->getBillPlanCategory()->getBillCategory()->getId() == $bill_category_id) {

                        $billPlanCategoryIds[] = $bill_plan_category_id;

                        $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id] = [
                            "0" => [
                                'description' => $bpc_billPlan->getBillPlanCategory()->getDescription(),
                                'id' => $bpc_billPlan->getBillPlanCategory()->getId(),
                            ]
                        ];

                        for ($i = 1; $i < 13; $i++) {

                            $installmentPaid = 0;
                            $installmentNotPaid = 0;

                            foreach ($cashFlow as $ind => $val) {

                                if ($bill_plan_category_id == $val['biplc_id']
                                    && $i == date('n', strtotime($val['dueDateAt']))
                                    && date('Y', strtotime($val['dueDateAt'])) == $params['year']
                                ) {
                                    //if ($val['referency'] == 'pago') {
                                    if (!is_null($val['cf_amount_paid'])) {
                                        $installmentPaid += $val['cf_amount_paid'];
                                    } else {
                                        $installmentNotPaid += $val['cf_amount'];
                                    }
                                }
                            }

                            $checkCashFlow = $this->checkCashFlow($i, $params['year']);

                            if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID) {
                                $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id] += [$i => [
                                    'total_paid' => $installmentPaid,
                                    'total_not_paid' => $installmentNotPaid
                                ]];
                            } elseif ($checkCashFlow == $this::CF_PAID) {
                                $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id] += [$i => ['total_paid' => $installmentPaid]];
                            } elseif ($checkCashFlow == $this::CF_NOT_PAID) {
                                $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id] += [$i => ['total_not_paid' => $installmentNotPaid]];
                            }

                            if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_PAID) {
                                if (empty($cashFlowData['content'][$bill_category_id]['total_bill_category_paid'][$i])) {
                                    $cashFlowData['content'][$bill_category_id]['total_bill_category_paid'][$i] = 0;
                                }
                                $cashFlowData['content'][$bill_category_id]['total_bill_category_paid'][$i] += $installmentPaid;
                            }

                            if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_NOT_PAID) {
                                if (empty($cashFlowData['content'][$bill_category_id]['total_bill_category_not_paid'][$i])) {
                                    $cashFlowData['content'][$bill_category_id]['total_bill_category_not_paid'][$i] = 0;
                                }
                                $cashFlowData['content'][$bill_category_id]['total_bill_category_not_paid'][$i] += $installmentNotPaid;
                            }
                        }

                        foreach ($billPlans as $bpl_billPlanEntity) {

                            /** @var BillPlan $bpl_billPlan */
                            $bpl_billPlan = $bpl_billPlanEntity;

                            $bill_plan_id = $bpl_billPlan->getId();

                            if (!in_array($bill_plan_id, $billPlanIds) && $bpl_billPlan->getBillPlanCategory()->getId() == $bill_plan_category_id) {

                                $billPlanIds[] = $bill_plan_id;

                                $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id]['bill_plans'][$bill_plan_id] = [
                                    "0" => [
                                        'description' => $bpl_billPlan->getDescription(),
                                        'id' => $bpl_billPlan->getId(),
                                    ]
                                ];

                                for ($i = 1; $i < 13; $i++) {

                                    $installmentPaid = 0;
                                    $installmentNotPaid = 0;

                                    foreach ($cashFlow as $ind => $val) {

                                        if ($bill_plan_id == $val['bipl_id']
                                            && $i == date('n', strtotime($val['dueDateAt']))
                                            && date('Y', strtotime($val['dueDateAt'])) == $params['year']
                                        ) {
                                            //if ($val['referency'] == 'pago') {
                                            if (!is_null($val['cf_amount_paid'])) {
                                                $installmentPaid += $val['cf_amount_paid'];
                                            } else {
                                                $installmentNotPaid += $val['cf_amount'];
                                            }
                                        }
                                    }

                                    $checkCashFlow = $this->checkCashFlow($i, $params['year']);

                                    if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID) {
                                        $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id]['bill_plans'][$bill_plan_id] += [$i => [
                                            'total_paid' => $installmentPaid,
                                            'total_not_paid' => $installmentNotPaid
                                        ]];
                                    } elseif ($checkCashFlow == $this::CF_PAID) {
                                        $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id]['bill_plans'][$bill_plan_id] += [$i => ['total_paid' => $installmentPaid]];
                                    } elseif ($checkCashFlow == $this::CF_NOT_PAID) {
                                        $cashFlowData['content'][$bill_category_id]['bill_plan_categories'][$bill_plan_category_id]['bill_plans'][$bill_plan_id] += [$i => ['total_not_paid' => $installmentNotPaid]];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $total_paid = [];
        $total_not_paid = [];

        $saldoFinal = '';
        $saldoAnterior = '';
        $previsaoSaldoFinal = '';

        for ($i = 1; $i < 13; $i++) {

            $checkCashFlow = $this->checkCashFlow($i, $params['year']);

            if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_PAID) {
                foreach ($cashFlowData['content'] as $ind => $val) {
                    foreach ($val['total_bill_category_paid'] as $bill_category_paid_month => $bill_category_paid_amount) {
                        if ($i == $bill_category_paid_month) {
                            if (empty($total_paid[$i])) {
                                $total_paid[$i] = 0;
                            }
                            $total_paid[$i] += $bill_category_paid_amount;
                        }
                    }
                }
            }

            if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_NOT_PAID) {
                foreach ($cashFlowData['content'] as $ind => $val) {
                    foreach ($val['total_bill_category_not_paid'] as $bill_category_not_paid_month => $bill_category_not_paid_amount) {
                        if ($i == $bill_category_not_paid_month) {
                            if (empty($total_not_paid[$i])) {
                                $total_not_paid[$i] = 0;
                            }
                            $total_not_paid[$i] += $bill_category_not_paid_amount;
                        }
                    }
                }
            }

            $total_paid[$i] = empty($total_paid[$i]) ? 0 : $total_paid[$i];
            $total_not_paid[$i] = empty($total_not_paid[$i]) ? 0 : $total_not_paid[$i];

            if (($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_PAID) && $i == 1) {
                $saldoFinal = $previousBalance + $total_paid[$i];
                $previsaoSaldoFinal = $saldoFinal + $total_not_paid[$i];
                $saldoAnterior[$i] = $previsaoSaldoFinal;
            } else {
                if ($checkCashFlow == $this::CF_PAID_AND_NOT_PAID || $checkCashFlow == $this::CF_PAID) {
                    $saldoFinal = $saldoAnterior[($i - 1)] + $total_paid[$i];
                    $previsaoSaldoFinal = $saldoFinal + $total_not_paid[$i];
                    $saldoAnterior[$i] = $previsaoSaldoFinal;
                } else {
                    if ($checkCashFlow == $this::CF_NOT_PAID && $i == 1) {
                        $previsaoSaldoFinal = $previousBalance + $total_not_paid[$i];
                        $saldoAnterior[$i] = $previsaoSaldoFinal;
                    } else {
                        if ($checkCashFlow == $this::CF_NOT_PAID) {
                            $previsaoSaldoFinal = $saldoAnterior[($i - 1)] + $total_not_paid[$i];
                            $saldoAnterior[$i] = $previsaoSaldoFinal;
                        }
                    }
                }
            }

            $cashFlowData['saldo_final'][$i] = $saldoFinal;
            $cashFlowData['saldo_anterior'][$i] = $i == 1 ? $previousBalance : $saldoAnterior[($i - 1)];
            $cashFlowData['previsao_saldo_final'][$i] = $previsaoSaldoFinal;
        }

        $cashFlowData['total_paid'] = $total_paid;
        $cashFlowData['total_not_paid'] = $total_not_paid;

        return $this->render('cashFlow/index.html.twig',
            [
                'cash_flow_data' => $cashFlowData,
                'params' => $params
            ]
        );
    }

    private function checkCashFlow($i, $year)
    {
        if ((date('n') == $i && date('Y') == $year)) {
            return $this::CF_PAID_AND_NOT_PAID;
        } else if ($i > date('n') && date('Y') == $year) {
            return $this::CF_NOT_PAID;
        } else if ($i < date('n') && date('Y') == $year) {
            return $this::CF_PAID;
        } else if ($year < date('Y')) {
            return $this::CF_PAID;
        } else if ($year > date('Y')) {
            return $this::CF_NOT_PAID;
        }
        return false;
    }
}
