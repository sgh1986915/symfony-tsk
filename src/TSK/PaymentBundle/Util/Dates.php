<?php
namespace TSK\PaymentBundle\Util;

/**
 * Dates 
 * 
 * @author    Malaney J. Hill <malaney@gmail.com> 
 * @copyright 2012 Obverse Dev
 * @license   PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 * @version   Release: 1.0
 */
class Dates
{
    /**
     * getMonthaversary
     * Computes the "monthaversary" (month anniversary) for a given $startDate
     * $num months in advance. For example, the first monthaversary of July 4, 2013
     * will be August 4, 2013. If the month does not have a true monthaversary (i.e.
     * Jan 30, 2013) then the last day of the month is returned (i.e. Feb 28, 2013)
     * 
     * @param string $startDate (YYYY-mm-dd)
     * @param int    $num       (which monthaversary to compute? first? second?)
     *
     * @access public
     * @return \DateTime
     */
    public function getMonthaversary($startDate, $num = 1)
    {
        $start = new \DateTime($startDate);
        $next = clone($start);
        $monthDateInterval = new \DateInterval('P' . $num . 'M');
        $next->add($monthDateInterval);

        if ($next->format('m') % 12 == (($start->format('m') + $num) % 12) ) {
            return $next;
        } else {
            //  Take the first day of month in question ...
            $next = new \DateTime($start->format('Y-m-1'));
            //  ... add the months to it ...
            $next->add($monthDateInterval);
            //  ... return the last day of that month
            return new \DateTime($next->format('Y-m-t'));
        }
    }
}
