<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Entity\BillCategory;
use AppBundle\Entity\BillInstallments;
use AppBundle\Entity\BillStatus;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\BillGenerateInstallmentsType;
use AppBundle\Form\BillType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/bill")
 */
class BillController extends Controller
{
    /**
     * @Route("/", name="bill_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if (!$request->query->has('date_start') && !$request->query->has('date_end')) {
            $request->query->add(
                [
                    'date_start' => date('01-m-Y'),
                    'date_end' => date('t-m-Y')
                ]
            );
        }

        $paginationHelper = $this->get('app.helper.pagination')->handle($request, Bill::class);

        $bills = $this->getDoctrine()->getRepository(Bill::class)->findLatest($paginationHelper);

        $results = [];

        foreach ($bills->getCurrentPageResults() as $result) {
            $result->getBillInstallments()->clear();
            $results[$result->getId()] = $result;
        }

        $billInstallments = $this->getDoctrine()->getRepository(BillInstallments::class)->findAllByBills($results);

        foreach ($billInstallments as $billInstallment) {
            $results[$billInstallment->getBill()->getId()]->getBillInstallments()->add($billInstallment);
        }

        $billCategory = $this->getDoctrine()->getRepository(BillCategory::class)->findAll();
        $billstatus = $this->getDoctrine()->getRepository(BillStatus::class)->findAll();

        return $this->render('bill/index.html.twig',
            [
                'bills' => $bills,
                'results' => $results,
                'bill_category' => $billCategory,
                'bill_status' => $billstatus,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="bill_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $bill = new Bill();

        $form = $this->createForm(BillType::class, $bill)
            ->add(
                'buttons',
                SubmitActionsType::class,
                [
                    'mapped' => false,
                    'actions' => [
                        SubmitActions::SAVE_AND_KEEP, SubmitActions::SAVE_AND_NEW, SubmitActions::SAVE_AND_CLOSE
                    ]
                ]
            );

        $formGenerateInstallments = $this->createForm(BillGenerateInstallmentsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setAmount($bill);
            $this->setBillStatus($bill);

            $em = $this->getDoctrine()->getManager();
            $em->persist($bill);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_edit',
                    array_merge(
                        ['id' => $bill->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'bill/new.html.twig',
            [
                'bill' => $bill,
                'form' => $form->createView(),
                'form_generate_installments' => $formGenerateInstallments->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="bill_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $id = $request->attributes->get('id');

        $bill = $this->getDoctrine()->getRepository(Bill::class)->findOneById($id);

        $this->setAmountInverse($bill);

        $originalBillInstallments = new ArrayCollection();

        foreach ($bill->getBillInstallments() as $billInstallment) {
            $originalBillInstallments->add($billInstallment);
        }

        $originalBillFiles = new ArrayCollection();

        foreach ($bill->getBillFiles() as $billFile) {
            $originalBillFiles->add($billFile);
        }

        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(BillType::class, $bill)
            ->add(
                'buttons',
                SubmitActionsType::class,
                [
                    'mapped' => false,
                    'actions' => [
                        SubmitActions::SAVE_AND_KEEP, SubmitActions::SAVE_AND_NEW, SubmitActions::SAVE_AND_CLOSE
                    ]
                ]
            );

        $formGenerateInstallments = $this->createForm(BillGenerateInstallmentsType::class);

        $formDelete = $this->createDeleteForm($bill);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->setAmount($bill);
            $this->setBillStatus($bill);

            $em = $this->getDoctrine()->getManager();

            foreach ($originalBillInstallments as $billInstallment) {
                if (false === $bill->getBillInstallments()->contains($billInstallment)) {
                    $billInstallment->setBill(null);
                    $em->remove($billInstallment);
                }
            }

            foreach ($originalBillFiles as $billFile) {
                if (false === $bill->getBillFiles()->contains($billFile)) {
                    $billFile->setBill(null);
                    $em->remove($billFile);
                }
            }

            $em->persist($bill);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_edit',
                    array_merge(
                        ['id' => $bill->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'bill/edit.html.twig',
            [
                'bill' => $bill,
                'form' => $form->createView(),
                'form_generate_installments' => $formGenerateInstallments->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="bill_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Bill $bill)
    {
        $form = $this->createDeleteForm($bill);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bill);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('bill_index');
        }

    }

    /**
     * @param Bill $bill
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Bill $bill)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bill_delete', ['id' => $bill->getId()]))
            ->setMethod('DELETE')
            ->setData($bill)
            ->getForm();
    }

    /**
     * @param Bill $bill
     */
    private function setAmount(Bill $bill)
    {
        $billCategoryReference = $bill->getBillCategory()->getReferency();

        $operator = '';

        if ($billCategoryReference == BillCategory::BILL_CATEGORY_DESPESA) {
            $operator = '-';
        }

        $amountFull = 0;

        foreach ($bill->getBillInstallments() as $billInstallment) {

            $amountFull += (float)str_replace(['-', '.', ','], ['', '', '.'], $billInstallment->getAmount());

            $newInstallmentAmount = $operator . $billInstallment->getAmount();
            $billInstallment->setAmount($newInstallmentAmount);

            if (!is_null($billInstallment->getAmountPaid())) {
                $newInstallmentAmountPaid = $operator . $billInstallment->getAmountPaid();
                $billInstallment->setAmountPaid($newInstallmentAmountPaid);
            }
        }

        $newAmount = $operator . number_format($amountFull, 2, ',', '.');
        $bill->setAmount($newAmount);
    }

    /**
     * @param Bill $bill
     */
    private function setAmountInverse(Bill $bill)
    {
        $newAmount = str_replace('-', '', $bill->getAmount());
        $bill->setAmount($newAmount);

        foreach ($bill->getBillInstallments() as $billInstallment) {
            $newInstallmentAmount = str_replace('-', '', $billInstallment->getAmount());
            $billInstallment->setAmount($newInstallmentAmount);

            if (!is_null($billInstallment->getAmountPaid())) {
                $newInstallmentAmountPaid = str_replace('-', '', $billInstallment->getAmountPaid());
                $billInstallment->setAmountPaid($newInstallmentAmountPaid);
            }
        }
    }

    private function setBillStatus(Bill $bill)
    {
        $status = BillStatus::BILL_STATUS_PAGO;

        foreach ($bill->getBillInstallments() as $billInstallment) {
            if ($billInstallment->getPaymentDateAt() === null && $billInstallment->getAmountPaid() === null) {
                $status = BillStatus::BILL_STATUS_EM_ABERTO;
                break;
            }
        }
        $bill->setBillStatus($this->getDoctrine()->getRepository(BillStatus::class)->findOneByReferency($status));
    }
}
