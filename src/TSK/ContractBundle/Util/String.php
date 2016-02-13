<?php

namespace TSK\ContractBundle\Util;

class String
{
    /**
     * stringifyPayments 
     * Takes an array of payments and generates a legal phrasing of those payments
     * 
     * @param mixed $payments 
     * @access public
     * @return void
     */
    public function stringifyPayments($payments)
    {
        if (!count($payments)) {
            return null;
        }
        rsort($payments);
        $uniquePayments = array_unique($payments);
        $seen = array_combine(array_values($uniquePayments), array_fill(0, count($uniquePayments), 0));
        foreach ($payments as $payment) {
            $seen[$payment]++;
        }
        $count = 0;
        $numTotal = 0;
        foreach ($seen as $paymentAmount => $num) {
            $today = $myToday = new \DateTime();
            $myToday = new \DateTime();
            $commenceDate = (!empty($dueDate)) ?
                $dueDate->add(new \DateInterval('P1M'))->format('Y-m-d') : 
                $myToday->add(new \DateInterval('P1M'))->format('Y-m-d') 
            ;
            $dueDate = $today->add(new \DateInterval('P'.$numTotal.'M'));
            
            $numTotal += $num;
            $dueDateStr = $dueDate->format('m/d/y');
            $dueString = ($num > 1) ? 'due on the '. $today->format('jS'). ' day of the month, commencing on' : 'due';
            $str = sprintf('%s %s of $%5.2f %s %s',
                $num,
                ($num < 2) ? 'installment' : 'monthly installments',
                $paymentAmount,
                $dueString,
                $dueDateStr
            );

            $clauses[] = $str;
        }

        $joinStr = (count($clauses) == 2) ? '; thereafter ' : '; ';
        return join('; ', $clauses);
    }
}

// $s = new String();
// print $s->stringifyPayments(array(42,42,42,42, 521, 521, 818,212));
