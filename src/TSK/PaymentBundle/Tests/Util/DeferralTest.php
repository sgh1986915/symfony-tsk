<?php
namespace TSK\PaymentBundle\Tests\Util;

use TSK\PaymentBundle\Util\Deferral;

class DeferralTest extends \PHPUnit_Framework_TestCase
{
    private $deferral;
    private $principal=1000;
    private $rate=0.75;
    private $duration=10;
    public function setup()
    {
        $this->deferral = new Deferral($this->principal, $this->rate, $this->duration, null, null);
    }

    /**
     * testDistributePaymentEvenly 
     * @dataProvider evenPaymentDistributions
     * 
     * @param mixed $amount 
     * @param mixed $deferralRate 
     * @param mixed $paymentsRemaining 
     * @param mixed $answer 
     * @access public
     * @return void
     */
    public function testDistributePaymentEvenly($amount, $deferralRate, $paymentsRemaining, $init = array(), $answer)
    {
        $this->assertEquals($this->deferral->distributePaymentEvenly($amount, $paymentsRemaining, $init), $answer);
    }

    public function testDateStamps()
    {
        $deferralPayments = $this->deferral->distributePaymentEvenly(1000, 10, array());
        $result = $this->deferral->datestampPayments($deferralPayments);
        for ($i=0; $i < 10; $i++) {
            if ($i == 0) {
                $date = new \DateTime();
            } else {
                $date->modify('first day of next month');
            }
            $expectedResult[$date->format('Y-m-d')] = $deferralPayments[$i];
        }
        $this->assertEquals($expectedResult, $result);
    }

    public function evenPaymentDistributions()
    {
        return array(
            array(1000, 0.75, 10, array(), array(
                250.0,
                83.34,
                83.34,
                83.34,
                83.33,
                83.33,
                83.33,
                83.33,
                83.33,
                83.33,
            )),
            array(500, 0.75, 10, array(), array(
                125.0,
                41.67,
                41.67,
                41.67,
                41.67,
                41.67,
                41.67,
                41.66,
                41.66,
                41.66,
            )),
        );
    }

    /**
     * testDistributePaymentEvenly 
     * @dataProvider maxedPaymentDistributions
     * 
     * @param mixed $amount 
     * @param mixed $deferralRate 
     * @param mixed $paymentsRemaining 
     * @param mixed $answer 
     * @access public
     * @return void
     */
    public function testDistributePaymentMax($amount, $deferralRate, $paymentsRemaining, $init = array(), $answer)
    {
        $this->assertEquals($this->deferral->distributePaymentMax($amount, $paymentsRemaining), $answer);
    }

    public function maxedPaymentDistributions()
    {
        return array(
            array(1000, 0.75, 10, array(), array(
                250.0,
                83.34,
                83.34,
                83.34,
                83.33,
                83.33,
                83.33,
                83.33,
                83.33,
                83.33,
            )),
            array(500, 0.75, 10, array(), array(
                250.0,
                83.34,
                83.34,
                83.32,
                0,
                0,
                0,
                0,
                0,
                0
            )),
        );

    }
}
