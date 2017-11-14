<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Entity\Bill;
use AppBundle\Entity\BillCategory;
use AppBundle\Entity\BillStatus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $params = $request->query->all();
        
        $billRepository = $this->getDoctrine()->getRepository(Bill::class);

        $banks = $this->getDoctrine()->getRepository(Bank::class)->findAll();

        //**************** RECEITAS **************

        $billCategoryReceita = $this->getDoctrine()->getRepository(BillCategory::class)->findOneBy(['referency' => BillCategory::BILL_CATEGORY_RECEITA]);
        $params['bill_category'] = $billCategoryReceita->getId();

        // TO RECEIVE
        $params['bills_status_desc'] = BillStatus::BILL_STATUS_EM_ABERTO;
        $params['date_start'] = date('d-m-Y');
        $params['date_end'] = (new \DateTime())->add(new \DateInterval('P30D'))->format('d-m-Y');

        $toReceive = $billRepository->dashboard($params);
        $params['sum_amount'] = true;
        $toReceiveTotal = $billRepository->getAmountTotal($params);

        // OVERDUE
        $params['bills_status_desc'] = BillStatus::BILL_STATUS_EM_ABERTO;
        $params['sum_amount'] = false;
        $params['sum_amount_paid'] = false;
        $params['overdue'] = 'true';
        unset($params['date_start']);
        unset($params['date_end']);

        $overdue = $billRepository->dashboard($params);
        $params['sum_amount'] = true;
        $overdueTotal = $billRepository->getAmountTotal($params);

        //*********** DESEPESAS *********************

        $params = $request->query->all();

        $billCategoryDespesa = $this->getDoctrine()->getRepository(BillCategory::class)->findOneBy(['referency' => BillCategory::BILL_CATEGORY_DESPESA]);
        $params['bill_category'] = $billCategoryDespesa->getId();

        // TO PAY
        $params['bills_status_desc'] = BillStatus::BILL_STATUS_EM_ABERTO;
        $params['date_start'] = date('d-m-Y');
        $params['date_end'] = (new \DateTime())->add(new \DateInterval('P30D'))->format('d-m-Y');

        $toPay = $billRepository->dashboard($params);
        $params['sum_amount'] = true;
        $toPayTotal = $billRepository->getAmountTotal($params);

        // TO PAY OVERDUE
        $params['bills_status_desc'] = BillStatus::BILL_STATUS_EM_ABERTO;
        $params['sum_amount'] = false;
        $params['sum_amount_paid'] = false;
        $params['overdue'] = 'true';
        unset($params['date_start']);
        unset($params['date_end']);

        $toPayOverdue = $billRepository->dashboard($params);
        $params['sum_amount'] = true;
        $toPayOverdueTotal = $billRepository->getAmountTotal($params);

        return $this->render('default/index.html.twig', [
            'toReceive' => $toReceive,
            'toReceiveTotal' => $toReceiveTotal,
            'overdue' => $overdue,
            'overdueTotal' => $overdueTotal,
            'toPay' => $toPay,
            'toPayTotal' => $toPayTotal,
            'toPayOverdue' => $toPayOverdue,
            'toPayOverdueTotal' => $toPayOverdueTotal,
            'banks' => $banks
        ]);
    }
}
