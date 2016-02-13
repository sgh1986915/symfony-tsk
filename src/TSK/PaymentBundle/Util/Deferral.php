<?php
namespace TSK\PaymentBundle\Util;

/**
 * Deferral 
 * This class holds logic for determining deferrals of payments against
 * a principal according to a deferral rate and deferral duration.
 * 
 * @package 
 * @version $id$
 * @copyright Malaney J. Hill <malaney@gmail.com> 
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license The MIT License
 */
class Deferral
{
    /**
     * principal 
     * The principal amount to be deferred
     * 
     * @var float
     * @access private
     */
    private $principal;

    /**
     * rate 
     * The rate at which payments are to be deferred
     *  
     * @var float
     * @access private
     */
    private $rate;

    /**
     * deferralDuration 
     * The number of periods (months) over which deferral
     * will take place.
     * 
     * @var int
     * @access private
     */
    private $deferralDuration;

    /**
     * schedule 
     * An array with keys representing deferral periods (i.e. 0, 1, 2 ...)
     * and values representing the amount of money to be realized during
     * that deferral period.
     * 
     * @var array
     * @access private
     */
    private $schedule;

    /**
     * initials 
     * An array with keys representing deferral periods (i.e. 0, 1, 2 ...)
     * and values representing the amount of money ALREADY set to be realized
     * by the system during that deferral period.
     * 
     * @var mixed
     * @access private
     */
    private $initials;

    private $contractStartDate;

    /**
     * recognitionRate 
     * Max. amount of a payment to recognize as revenue up front.
     * 
     * @var float
     * @access private
     */
    private $recognitionRate;

    /**
     * __construct 
     * 
     * @param mixed $principal - (float) total amount to be deferred (e.g. $2000)
     * @param mixed $rate - (float) deferral rate (e.g. 0.75)
     * @param mixed $deferralDuration - (int) num months to defer principal over 
     * @param mixed $initials  - array where keys are 0,1,2, etc ... representing
     *   payment months and the values are floats representing the amount of money
     *   already ready realized during that payment month
     * @param mixed $contractStartDate - (string or \DateTime) contract start date
     * @param mixed $recognitionRate - (float) max percentage of payment to 
     *   realize as revenue up front.
     * @access public
     * @return void
     */
    public function __construct($principal, $rate, $deferralDuration, $initials = null, $contractStartDate = null, $recognitionRate = null, $recognitionCap = null)
    {
        $this->rate = $rate;
        $this->principal = $principal;
        $this->deferralDuration = $deferralDuration;
        $this->initials = $initials;
        $this->contractStartDate = (is_null($contractStartDate)) ? new \DateTime() : $contractStartDate;
        if (is_null($recognitionRate)) {
            $this->recognitionRate = (1 - $rate);
        } else {
            $this->recognitionRate = $recognitionRate;
        }
        if (is_null($recognitionCap)) {
            $this->recognitionCap = $this->principal;
        } else {
            $this->recognitionCap = $recognitionCap;
        }

        $this->setSchedule(
            $this->generateSchedule(
                $this->principal,
                $this->rate,
                $this->deferralDuration
            )
        );
    }

    private function getRealizedTuition($payment, $remainingPeriods)
    {
        $results  = array_fill(0, $this->getDeferralDuration(), 0);
        $results[$this->getDeferralDuration() - $remainingPeriods] = round($payment * (1 - $this->getRate()), 2);
        return $results;
    }

    private function getDeferredPayments($payment, $remainingPeriods)
    {
        $payments = $this->evenlyDistribute(
            $payment,
            $remainingPeriods,
            $this->getInitials()
        );
        return $payments;
    }

    /**
     * datestampPayments 
     * Takes an array of payments and assigns datestamps to them
     * where the key is the date in $format format and the value
     * is the payment due that month.  The first payment is assigned
     * on the current day, every other payment is assigned the first
     * of the following month.
     * 
     * @param array $payments 
     * @param string $format
     * @access public
     * @return array
     */
    public function datestampPayments($payments, $format = 'Y-m-d')
    {
        $today = new \DateTime();
        $contractStartDate = $this->contractStartDate;
        $results[$contractStartDate->format($format)] = array_shift($payments);
        $month = $contractStartDate->format('m') + 1;
        $year = $contractStartDate->format('Y');
        foreach ($payments as $payment) {
            $month = $month % 13;
            if ($month == 0) {
                $month++;
                $year++;
            }
            // If we are in the same month / year as today then datestamp it for today
            if (($year == $today->format('Y')) && ($month == $today->format('m'))) {
                $day = $today->format('d');
            } else {
                $day = '01';
            }
            $newDate = new \DateTime($year.'-'.$month.'-'.$day);
            $results[$newDate->format($format)] = $payment;
            $month++;
        }
        return $results;
    }

    public function distributePaymentEvenly($payment, $remainingPeriods)
    {
        $tuition = $this->getRealizedTuition($payment, $remainingPeriods);
        $payments = $this->getDeferredPayments($payment - array_sum($tuition), $remainingPeriods - 1);
        
        for ($i=0; $i< $this->getDeferralDuration(); $i++) {
            $results[$i] = max($payments[$i], $tuition[$i]);
        }
        return $results;
    }

    public function distributePaymentMax($payment, $remainingPeriods)
    {
        $payments = $this->maxOutPayment($payment, $remainingPeriods);
        return $payments;
    }

    /**
     * Get principal.
     *
     * @return principal.
     */
    public function getPrincipal()
    {
        return $this->principal;
    }
 
    /**
     * Set principal.
     *
     * @param principal the value to set.
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;
        return $this;
    }
 
    /**
     * Get rate.
     *
     * @return rate.
     */
    public function getRate()
    {
        return $this->rate;
    }
 
    /**
     * Set rate.
     *
     * @param rate the value to set.
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    public function setDeferralDuration($deferralDuration)
    {
        $this->deferralDuration = $deferralDuration;
        return $this;
    }

    public function getDeferralDuration()
    {
        return $this->deferralDuration;
    }

    /**
     * evenlyDistribute
     * Divides $payment evenly into $num buckets to the nearest
     * $precision decimal places.  Returns an array filled with
     * totals of each bucket such that array_sum(bucketize(...)
     * will equal your original payment TO THE PENNY!  All with
     * no memory leaks!
     *
     * @param float $payment
     * @param integer $num
     * @param int $precision
     * @param int $init - an array initialized with existing payment amounts
     * @access public
     * @return array
     */
    private function evenlyDistribute($payment, $remainingPeriods, $init = null)
    {
        $precision = 2;
        $filled = array();
        $results = array_fill(0, $this->getDeferralDuration(), 0);

        $init = $this->getInitials();
        if (empty($init)) {
            $inits = array_fill(0, $this->getDeferralDuration(), 0);
        } else {
            $inits = (array) $init;
        }

        $start = $this->getDeferralDuration() - $remainingPeriods;
        while ($payment > 0) {
            for ($j=$start; $j < $this->getDeferralDuration(); $j++) {
                if (array_sum(array_slice($results, 0, $j+1)) +
                    array_sum(array_slice($inits, 0, $j+1)) >=
                    array_sum(array_slice($this->getSchedule(), 0, $j+1))
                ) {
                    // this means that we've filled up a slot for the
                    // given month.  We will skip adding to result
                    // and debiting from payment and add the slot
                    // number to a hash
                    $filled[$j] = 1;
                } else {
                    $results[$j] = round($results[$j] + 0.01, $precision);
                    $payment = round($payment - 0.01, $precision);
                    
                }

                // If we have filled every possible slot then break;
                if (count($filled) >= count($this->getSchedule())) {
                    break;
                }
            }
        }
        return $results;
    }

    /**
     * generateSchedule
     * This function generates a standard deferral schedule based
     * on a principal, rate and number of periods to defer across.
     * It starts by subtracting a percentage off the principal in
     * the first month and then spreads the balance out evenly over
     * the remaining months.
     * 
     * @param mixed $principal
     * @param mixed $rate 
     * @param mixed $periods
     * @access public
     * @return array
     */
    public function generateSchedule($principal, $rate, $periods)
    {
        $maxPayment = $principal;
        $periodsFilled = 0;
        $precision = 2;

        // Fill an array with zeros representing each payment
        // over $periods durations
        $results = array_fill(0, $periods, 0);

        // Fill the first array slot with non-deferred amount
        $results[0] = $principal * (1 - $rate);

        // Distribute the remainder over the balance of periods
        $principal = $principal - $results[0];
        while ($principal > 0) {
            for ($j=1; $j < $periods; $j++) {
                if ($results[$j] < $maxPayment) {
                    $results[$j] = round($results[$j] + 0.01, $precision);
                    $principal = round($principal - 0.01, $precision);
                } else {
                    $periodsFilled++;
                }

                if ($principal <= 0) {
                    break;
                }
            }
            if ($periodsFilled > $periods) {
                break;
            }
        }
        return $results;
    }

    /**
     * maxOutPayment 
     * Distributes a payment by applying as much of it as it can, as
     * early as it can, as defined by the deferral schedule.
     * 
     * @param mixed $payment 
     * @param mixed $remainingPeriods 
     * @access private
     * @return array
     */
    private function maxOutPayment($payment, $remainingPeriods)
    {
        $precision = 2;
        $init = $this->getInitials();
        if (empty($init)) {
            $inits = array_fill(0, $this->getDeferralDuration(), 0);
        } else {
            $inits = (array) $init;
        }
        $results = array_fill(0, $this->getDeferralDuration(), 0);

        $initialRealization = 0;
        // If we have not yet received recognitionCap in inits, then
        // we apply the recognitionRate to the original payment
        if (array_sum($inits) < $this->recognitionCap) {
            $initialRealization = ($this->recognitionRate) * $payment;
        }
        $maxes = $this->getSchedule();
        $j=0;

        $myStart = $this->getDeferralDuration() - $remainingPeriods;
        $myStart = 0;

        if ($initialRealization) {
            $payment -= $initialRealization;
            for ($j=$myStart; $j < $this->getDeferralDuration(); $j++) {
                while (array_sum(array_slice($maxes, 0, $j+1)) >
                    array_sum(array_slice($results, 0, $j+1)) +
                    array_sum(array_slice($inits, 0, $j+1))
                ) {
                    if ($initialRealization <= 0) {
                        break;
                    }

                    if ($results[$j] + $inits[$j] >= $maxes[$j]) {
                        $j = $j + 1;
                    }

                    $results[$j] = round($results[$j] + 0.01, $precision);
                    $initialRealization = round($initialRealization - 0.01, $precision);
                }
            }
        }
        $start = 0;
            
        for ($j=0; $j < $this->getDeferralDuration(); $j++) {
            while (array_sum(array_slice($maxes, 0, $j+1)) >
                array_sum(array_slice($results, 0, $j+1)) +
                array_sum(array_slice($inits, 0, $j+1))
            ) {
                if ($payment <= 0) {
                    break;
                }

                // If we have already overrun the max for this period, 
                // then advance to next period
                if ($results[$j] + $inits[$j] >= $maxes[$j]) {
                    $j = $j + 1;
                }

                // If this is the current period and we've already realized as much
                // as we can, then we should advance to the next period ...
                if (($j == $myStart) && ($results[$j] >= $initialRealization)) {
                    $j=$j+1;
                } else {
                    $results[$j] = round($results[$j] + 0.01, $precision);
                    $payment = round($payment - 0.01, $precision);
                }
            }
        }
        return $results;
    }

    private function _distributePayment($payment, $inits, $maxes, $start)
    {
        $results = array_fill(0, $this->getDeferralDuration(), 0);
    }
    
    public function getSchedule()
    {
        return $this->schedule;
    }

    protected function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }

    public function setInitials($initials)
    {
        $this->initials = $initials;
        return $this;
    }

    public function getInitials()
    {
        return $this->initials;
    }
    
    public function setTotalNumPayments($totalNumPayments)
    {
        $this->totalNumPayments = $totalNumPayments;
        return $this;
    }
    
    public function getTotalNumPayments()
    {
        return $this->totalNumPayments;
    }
 
    /**
     * Get distributionStrategy.
     *
     * @return distributionStrategy.
     */
    public function getDistributionStrategy()
    {
        return $this->distributionStrategy;
    }
 
    /**
     * Set distributionStrategy.
     *
     * @param distributionStrategy the value to set.
     */
    public function setDistributionStrategy($distributionStrategy)
    {
        $this->distributionStrategy = $distributionStrategy;
        return $this;
    }
}
