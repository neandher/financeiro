<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Event\FlashBagEvents;
use AppBundle\Form\BankType;
use AppBundle\Form\SubmitActions;
use AppBundle\Form\SubmitActionsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/bank")
 */
class BankController extends Controller
{
    /**
     * @Route("/", name="bank_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request, Bank::class);

        $banks = $this->getDoctrine()->getRepository('AppBundle:Bank')->findLatest($paginationHelper);

        return $this->render('bank/index.html.twig',
            [
                'banks' => $banks,
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/new", name="bank_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $bank = new Bank();

        $form = $this->createForm(BankType::class, $bank)
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
            $em->persist($bank);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_INSERTED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bank_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bank_edit',
                    array_merge(
                        ['id' => $bank->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bank_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'bank/new.html.twig',
            [
                'bank' => $bank,
                'form' => $form->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", requirements={"id" : "\d+"}, name="bank_edit")
     * @Method({"GET","POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Bank $bank)
    {
        $paginationHelper = $this->get('app.helper.pagination')->handle($request);

        $form = $this->createForm(BankType::class, $bank)
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

        $formDelete = $this->createDeleteForm($bank);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_UPDATED);

            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_NEW)->isClicked()) {
                return $this->redirectToRoute('bank_new', $paginationHelper->getRouteParams());
            }
            if ($form->get('buttons')->get(SubmitActions::SAVE_AND_KEEP)->isClicked()) {
                return $this->redirectToRoute(
                    'bank_edit',
                    array_merge(
                        ['id' => $bank->getId()],
                        $paginationHelper->getRouteParams()
                    )
                );
            }

            return $this->redirectToRoute('bank_index', $paginationHelper->getRouteParams());
        }

        return $this->render(
            'bank/edit.html.twig',
            [
                'bank' => $bank,
                'form' => $form->createView(),
                'form_delete' => $formDelete->createView(),
                'pagination_helper' => $paginationHelper
            ]
        );
    }

    /**
     * @Route("/{id}/", name="bank_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Bank $bank)
    {
        $form = $this->createDeleteForm($bank);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bank);
            $em->flush();

            $this->get('app.helper.flash_bag')->newMessage(FlashBagEvents::MESSAGE_TYPE_SUCCESS, FlashBagEvents::MESSAGE_SUCCESS_DELETED);

            return $this->redirectToRoute('bank_index');
        }

    }

    /**
     * @param Bank $bank
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm(Bank $bank)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bank_delete', ['id' => $bank->getId()]))
            ->setMethod('DELETE')
            ->setData($bank)
            ->getForm();
    }
}
