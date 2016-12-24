<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use Proxies\__CG__\AppBundle\Entity\BillCategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $billCategory = $this->getDoctrine()->getRepository(BillCategory::class)->findOneBy(['referency' => 'despesa']);

        $bills = $this->getDoctrine()->getRepository(Bill::class)->findByParams(
          [
              'billCategory' => $billCategory->getId(),
              'datePaymentIsNull' => true,
              'latest_days' => 30
          ]
        );

        return $this->render('default/index.html.twig');
    }
}
