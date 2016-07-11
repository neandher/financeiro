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
    /**
     * @Route("/", name="cash_flow_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $billPlans = $this->getDoctrine()->getRepository(BillPlan::class)->listBillPlans();

        $previousBalance = $this->getDoctrine()->getRepository(Bill::class)->balancePreviousMonth();
        $previousBalance = empty($previousBalance['previous_balance']) || is_null($previousBalance['previous_balance'])?0:$previousBalance['previous_balance'];
        
        $cashFlow = $this->getDoctrine()->getRepository(Bill::class)->cashFlow();
        
        return $this->render('cashFlow/index.html.twig',
            [
                'bill_plans' => $billPlans,
                'previous_balance' => $previousBalance,
                'cash_flow' => $cashFlow
            ]
        );
    }
}
