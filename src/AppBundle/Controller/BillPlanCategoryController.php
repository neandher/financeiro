<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BillPlanCategory;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\BillPlanCategoryType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/billPlanCategory")
 */
class BillPlanCategoryController extends Controller
{
    /**
     * @Route("/", name="bill_plan_category_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request, BillPlanCategory::class);

        $billPlanCategorys = $this->getDoctrine()->getRepository('AppBundle:BillPlanCategory')->findLatest($paginationHelper);

        return $this->render('billPlanCategory/index.html.twig',
            [
                'billPlanCategorys' => $billPlanCategorys,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="bill_plan_category_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $billPlanCategory = new BillPlanCategory();

        $form = $this->createForm(BillPlanCategoryType::class, $billPlanCategory)
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
            $em->persist($billPlanCategory);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_category_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_category_edit',
                    array_merge(
                        ['id' => $billPlanCategory->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_category_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlanCategory/new.html.twig',
            [
                'billPlanCategory' => $billPlanCategory,
                'form' => $form->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="bill_plan_category_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BillPlanCategory $billPlanCategory)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(BillPlanCategoryType::class, $billPlanCategory)
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

        $formDelete = $this->createDeleteForm($billPlanCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bill_plan_category_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bill_plan_category_edit',
                    array_merge(
                        ['id' => $billPlanCategory->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bill_plan_category_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'billPlanCategory/edit.html.twig',
            [
                'billPlanCategory' => $billPlanCategory,
                'form' => $form->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="bill_plan_category_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, BillPlanCategory $billPlanCategory)
    {
        $form = $this->createDeleteForm($billPlanCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($billPlanCategory);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('bill_plan_category_index');
        }

    }

    /**
     * @param BillPlanCategory $billPlanCategory
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(BillPlanCategory $billPlanCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bill_plan_category_delete', ['id' => $billPlanCategory->getId()]))
            ->setMethod('DELETE')
            ->setData($billPlanCategory)
            ->getForm();
    }
}
