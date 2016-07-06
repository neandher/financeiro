<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BillPlanType;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\BillPlanTypeType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/billPlanType")
 */
class BillPlanTypeController extends Controller
{
    /**
     * @Route("/", name="bill_plan_type_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request, BillPlanType::class);

        $billPlanTypes = $this->getDoctrine()->getRepository('AppBundle:BillPlanType')->findLatest($paginationHelper);

        return $this->render('billPlanType/index.html.twig',
            [
                'billPlanTypes' => $billPlanTypes,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="bill_plan_type_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $billPlanType = new BillPlanType();

        $form = $this->createForm(BillPlanTypeType::class, $billPlanType)
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
            $em->persist($billPlanType);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_type_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_type_edit',
                    array_merge(
                        ['id' => $billPlanType->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_type_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlanType/new.html.twig',
            [
                'billPlanType' => $billPlanType,
                'form' => $form->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="bill_plan_type_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BillPlanType $billPlanType)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(BillPlanTypeType::class, $billPlanType)
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

        $formDelete = $this->createDeleteForm($billPlanType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_type_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_type_edit',
                    array_merge(
                        ['id' => $billPlanType->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_type_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlanType/edit.html.twig',
            [
                'billPlanType' => $billPlanType,
                'form' => $form->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="bill_plan_type_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, BillPlanType $billPlanType)
    {
        $form = $this->createDeleteForm($billPlanType);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($billPlanType);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('bill_plan_type_index');
        }

    }

    /**
     * @param BillPlanType $billPlanType
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(BillPlanType $billPlanType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bill_plan_type_delete', ['id' => $billPlanType->getId()]))
            ->setMethod('DELETE')
            ->setData($billPlanType)
            ->getForm();
    }
}
