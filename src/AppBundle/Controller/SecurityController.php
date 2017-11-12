<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="system_security_login")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        $utils = $this->get('security.authentication_utils');
        $form = $this->createForm(LoginType::class);

        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login_check", name="system_security_login_check")
     */
    public function loginCheckAction()
    {
        // TODO: Implement loginCheckAction() method.
    }

    /**
     * @Route("/logout", name="system_security_logout")
     */
    public function logoutAction()
    {
        // TODO: Implement logoutAction() method.
    }
}