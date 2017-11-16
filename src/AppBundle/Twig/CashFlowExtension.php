<?php

namespace AppBundle\Twig;

use AppBundle\Controller\CashFlowController;

class CashFlowExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('paidOption', [
                $this,
                'paidOptionFunction'
            ]),
            new \Twig_SimpleFunction('billCategoryStyle', [
                $this,
                'billCategoryStyleFunction'
            ]),
            new \Twig_SimpleFunction('hiddenMonthsXs', [
                $this,
                'hiddenMonthsXsFunction'
            ])
        ];
    }

    public function paidOptionFunction($i, $year)
    {
        if ((date('n') == $i && date('Y') == $year)) {
            return CashFlowController::CF_PAID_AND_NOT_PAID;
        } else if ($i > date('n') && date('Y') == $year) {
            return CashFlowController::CF_NOT_PAID;
        } else if ($i < date('n') && date('Y') == $year) {
            return CashFlowController::CF_PAID;
        } else if ($year < date('Y')) {
            return CashFlowController::CF_PAID;
        } else if ($year > date('Y')) {
            return CashFlowController::CF_NOT_PAID;
        }
        return false;
    }

    public function billCategoryStyleFunction($amount)
    {
        if (strstr($amount, '-')) {
            return '_text-danger';
        } else {
            return '_text-success';
        }
    }

    public function hiddenMonthsXsFunction($i, $year)
    {
        $hidden = '';
        if (date('Y') == $year) {
            $hidden = 'hidden-xs';
            if (date('n') == $i || (date('n') + 1) == $i || (date('n') - 1) == $i) {
                $hidden = '';
            }
        }
        return $hidden;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'cash_flow_extension';
    }
}