<?php

namespace AppBundle\Controller;

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
        $billRepository = $this->getDoctrine()->getRepository(Bill::class);

        $request->query->set('num_items', 30);
        $request->query->set('sorting', ['billInstallments.dueDateAt' => 'asc']);
        $request->query->set('group_by_false', true);

        //**************** RECEITAS **************

        $billCategoryReceita = $this->getDoctrine()->getRepository(BillCategory::class)->findOneBy(['referency' => BillCategory::BILL_CATEGORY_RECEITA]);
        $request->query->set('bill_category', $billCategoryReceita->getId());

        // TO RECEIVE
        $request->query->set('bills_status_desc', BillStatus::BILL_STATUS_EM_ABERTO);
        $request->query->set('date_start', date('d-m-Y'));
        $request->query->set('date_end', (new \DateTime())->add(new \DateInterval('P30D'))->format('d-m-Y'));

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $toReceive = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount', true);
        $toReceiveTotal = $billRepository->getAmountTotal($request->query->all());

        // RECEIVED
        /*$request->query->set('bills_status_desc', BillStatus::BILL_STATUS_PAGO);
        $request->query->set('date_start', (new \DateTime())->sub(new \DateInterval('P30D'))->format('d-m-Y'));
        $request->query->set('date_end', date('d-m-Y'));
        $request->query->set('sum_amount', false);

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $received = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount_paid', true);
        $receivedTotal = $billRepository->getAmountTotal($request->query->all());*/

        // OVERDUE
        $request->query->set('bills_status_desc', BillStatus::BILL_STATUS_EM_ABERTO);
        $request->query->set('sum_amount', false);
        $request->query->set('sum_amount_paid', false);
        $request->query->set('overdue', 'true');
        $request->query->remove('date_start');
        $request->query->remove('date_end');

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $overdue = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount', true);
        $overdueTotal = $billRepository->getAmountTotal($request->query->all());

        //*********** DESEPESAS *********************

        $billCategoryDespesa = $this->getDoctrine()->getRepository(BillCategory::class)->findOneBy(['referency' => BillCategory::BILL_CATEGORY_DESPESA]);
        $request->query->set('bill_category', $billCategoryDespesa->getId());

        $request->query->remove('sum_amount');
        $request->query->remove('sum_amount_paid');
        $request->query->remove('overdue');

        // TO PAY
        $request->query->set('bills_status_desc', BillStatus::BILL_STATUS_EM_ABERTO);
        $request->query->set('date_start', date('d-m-Y'));
        $request->query->set('date_end', (new \DateTime())->add(new \DateInterval('P30D'))->format('d-m-Y'));

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $toPay = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount', true);
        $toPayTotal = $billRepository->getAmountTotal($request->query->all());

        // PAID
        /*$request->query->set('bills_status_desc', BillStatus::BILL_STATUS_PAGO);
        $request->query->set('date_start', (new \DateTime())->sub(new \DateInterval('P30D'))->format('d-m-Y'));
        $request->query->set('date_end', date('d-m-Y'));
        $request->query->set('sum_amount', false);

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $paid = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount_paid', true);
        $paidTotal = $billRepository->getAmountTotal($request->query->all());*/

        // TO PAY OVERDUE
        $request->query->set('bills_status_desc', BillStatus::BILL_STATUS_EM_ABERTO);
        $request->query->set('sum_amount', false);
        $request->query->set('sum_amount_paid', false);
        $request->query->set('overdue', 'true');
        $request->query->remove('date_start');
        $request->query->remove('date_end');

        $pagination = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $toPayOverdue = $billRepository->findLatest($pagination);
        $request->query->set('sum_amount', true);
        $toPayOverdueTotal = $billRepository->getAmountTotal($request->query->all());

        return $this->render('default/index.html.twig', [
            'toReceive' => $toReceive,
            'toReceiveTotal' => $toReceiveTotal,
//            'received' => $received,
//            'receivedTotal' => $receivedTotal,
            'overdue' => $overdue,
            'overdueTotal' => $overdueTotal,
            'toPay' => $toPay,
            'toPayTotal' => $toPayTotal,
//            'paid' => $paid,
//            'paidTotal' => $paidTotal,
            'toPayOverdue' => $toPayOverdue,
            'toPayOverdueTotal' => $toPayOverdueTotal
        ]);
    }
}
