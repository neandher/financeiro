<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PaymentMethod;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\PaymentMethodType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/paymentMethod")
 */
class PaymentMethodController extends Controller
{
    /**
     * @Route("/", name="payment_method_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request, PaymentMethod::class);

        $paymentMethods = $this->getDoctrine()->getRepository('AppBundle:PaymentMethod')->findLatest($paginationHelper);

        return $this->render('paymentMethod/index.html.twig',
            [
                'paymentMethods' => $paymentMethods,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="payment_method_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $paymentMethod = new PaymentMethod();

        $form = $this->createForm(PaymentMethodType::class, $paymentMethod)
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
            $em->persist($paymentMethod);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('payment_method_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'payment_method_edit',
                    array_merge(
                        ['id' => $paymentMethod->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('payment_method_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'paymentMethod/new.html.twig',
            [
                'paymentMethod' => $paymentMethod,
                'form' => $form->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="payment_method_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, PaymentMethod $paymentMethod)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(PaymentMethodType::class, $paymentMethod)
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

        $formDelete = $this->createDeleteForm($paymentMethod);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('payment_method_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'payment_method_edit',
                    array_merge(
                        ['id' => $paymentMethod->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('payment_method_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'paymentMethod/edit.html.twig',
            [
                'paymentMethod' => $paymentMethod,
                'form' => $form->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="payment_method_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, PaymentMethod $paymentMethod)
    {
        $form = $this->createDeleteForm($paymentMethod);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($paymentMethod);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('payment_method_index');
        }

    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(PaymentMethod $paymentMethod)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('payment_method_delete', ['id' => $paymentMethod->getId()]))
            ->setMethod('DELETE')
            ->setData($paymentMethod)
            ->getForm();
    }
}
