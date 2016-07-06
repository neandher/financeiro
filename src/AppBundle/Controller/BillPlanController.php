<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BillPlan;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\BillPlanType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/billPlan")
 */
class BillPlanController extends Controller
{
    /**
     * @Route("/", name="bill_plan_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request, BillPlan::class);

        $billPlans = $this->getDoctrine()->getRepository('AppBundle:BillPlan')->findLatest($paginationHelper);

        return $this->render('billPlan/index.html.twig',
            [
                'billPlans' => $billPlans,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="bill_plan_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $billPlan = new BillPlan();

        $form = $this->createForm(BillPlanType::class, $billPlan)
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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($billPlan);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_edit',
                    array_merge(
                        ['id' => $billPlan->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlan/new.html.twig',
            [
                'billPlan' => $billPlan,
                'form' => $form->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="bill_plan_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BillPlan $billPlan)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(BillPlanType::class, $billPlan)
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

        $formDelete = $this->createDeleteForm($billPlan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_edit',
                    array_merge(
                        ['id' => $billPlan->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlan/edit.html.twig',
            [
                'billPlan' => $billPlan,
                'form' => $form->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="bill_plan_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, BillPlan $billPlan)
    {
        $form = $this->createDeleteForm($billPlan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($billPlan);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('bill_plan_index');
        }

    }

    /**
     * @param BillPlan $billPlan
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(BillPlan $billPlan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bill_plan_delete', ['id' => $billPlan->getId()]))
            ->setMethod('DELETE')
            ->setData($billPlan)
            ->getForm();
    }
}
