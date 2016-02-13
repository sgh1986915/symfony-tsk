<?php

namespace TSK\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ob\HighchartsBundle\Highcharts\Highchart;

use TSK\PaymentBundle\Util\Deferral;

/**
 * Deferral controller.
 *
 * @Route("/")
 */
class DeferralController extends Controller
{
    /**
     * Graph/Simulate Payment Deferrals
     *
     * @Route("/deferral/graph", name="deferral_graph", options={"expose"=true})
     * @Template()
     */
    public function graphAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $principal = (int)$request->request->get('principal');
            $rate = (float) $request->request->get('rate');
            $recognitionRate = (float) $request->request->get('recognitionRate');
            $recognitionCap = (float) $request->request->get('recognitionCap');
            $periods = (int) $request->request->get('periods');
            $payments = $request->request->get('payments');
            $strategy = $request->request->get('strategy');
        } else {
            $principal = (int)$request->query->get('principal');
            $rate = (float) $request->query->get('rate');
            $recognitionRate = (float) $request->query->get('recognitionRate');
            $recognitionCap = (float) $request->query->get('recognitionCap');
            $periods = (int) $request->query->get('periods');
            $payments = $request->query->get('payments');
            $strategy = $request->query->get('strategy');
        }
        
        if (!$principal) $principal = 1000;
        if (!$rate) $rate = 0.75;
        if (!$recognitionRate) $recognitionRate = 1 - $rate;
        if (!$recognitionCap) $recognitionCap = $principal;
        if (!$periods) $periods = 10;
        if (!$payments) $payments = array();
        if (!$strategy) $strategy = 'max';

        $function = ($strategy == 'even') ? 'distributePaymentEvenly' : 'distributePaymentMax';

        $d = new Deferral(
            $principal,
            $rate,
            $periods
        );

        $schedule = $d->getSchedule();

        $runningTotal = 0;
        $totals = array();
        foreach ($schedule as $s) {
            $runningTotal += $s;
            $totals[] = $runningTotal;
        }

        $inits = array_fill(0, $periods, 0);
        $idx = 0;
    
        // Sort the $payments array by order key
        usort($payments, function($a, $b) {
            return $a['period'] - $b['period'];
        });

        $paymentsTotal = 0;
        $paymentsTotal = array_reduce($payments, function($result, $item) {
            $result += $item['amount'];
            return $result;
        });

        foreach ($payments as $payment) {
        
            $d = new Deferral(
                $principal,
                $rate,
                $periods,
                $inits,
                null,
                $recognitionRate,
                $recognitionCap
            );
            $deferrals = $d->{$function}(
                $payment['amount'],
                $periods - $payment['period']
                // $periods, 
            );
            $chartArrays[] = array(
                'name' => "Payment $idx realized",
                'type' => 'column',
                'data' => $deferrals

            );
        
            $inits = $this->sumArraySlots($inits, $deferrals);
            $idx++;
        }

        // Chart
        $series = array(
            array(
                "name" => "Monthly Payment Realized", 
                "type" => 'column', 
                "data" => $schedule
            ),
            array(
                "name" => "Deferral Schedule", 
                "type" => 'spline', 
                "data" => $totals
            ),
        );


        $runningPaymentTotal = 0;
        $paymentTotals = array_fill(0, $periods, 0);
        if (!empty($chartArrays)) {
            foreach ($chartArrays as $ca) {
                array_push($series, $ca);
                $paymentTotals = $this->sumArraySlots($paymentTotals, $ca['data']);
            }

            $tally = 0;
            foreach ($paymentTotals as $idx => $pt) {
                $tally += $pt;
                $myTotals[] = $tally;
            }
            // Push payment totals onto series
            array_push($series, array('name' => 'Cumulative Payment Realized', 'type' => 'spline', 'data' => $myTotals));
        }

        $ob = new Highchart();
        $ob->chart->renderTo('linechart');  // The #id of the div where to render the chart
        $ob->title->text('Deferral Schedule');
        $ob->xAxis->title(array('text'  => "Payment Periods (months)"));
        $ob->yAxis->title(array('text'  => "Payment Realized (dollars)"));
        $ob->series($series);

        return $this->render('TSKPaymentBundle:Deferral:graph.html.twig', array(
            'chart' => $ob,
            'principal' => $principal,
            'rate' => $rate,
            'recognitionRate' => $recognitionRate,
            'recognitionCap' => $recognitionCap,
            'periods' => $periods,
            'payments' => $payments,
            'paymentsTotal' => $paymentsTotal,
            'strategy' => $strategy,
            'evenSelected' => ($strategy == 'even') ? 'selected' : '',
            'maxSelected' => ($strategy == 'max') ? 'selected' : '',
            'admin_pool' => $this->get('sonata.admin.pool')
        ));
    }

    public function sumArraySlots($arr1, $arr2)
    {
        $totals[] = array();
        for ($i=0; $i < count($arr1); $i++) {
            $totals[$i] = $arr1[$i] + $arr2[$i];
        }
        return $totals;
    }
}
