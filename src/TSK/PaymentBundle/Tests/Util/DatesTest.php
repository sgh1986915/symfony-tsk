<?php
namespace TSK\PaymentBundle\Tests\Util;

use TSK\PaymentBundle\Util\Dates as DatesUtil;

class DatesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetMonthaversary 
     * @dataProvider monthaversaryData
     * 
     * @param mixed $startDate 
     * @param mixed $numMonths 
     * @param mixed $answer 
     * @access public
     * @return void
     */
    public function testGetMonthaversary($startDate, $numMonths, $answer)
    {
        $datesUtil = new DatesUtil();
        $result = $datesUtil->getMonthaversary($startDate, $numMonths);
        $this->assertEquals($result->format('Y-m-d'), $answer);
    }

    public function monthaversaryData()
    {
        return array(
            array('2012-01-31', 1, '2012-02-29'),
            array('2012-01-31', 2, '2012-03-31'),
            array('2012-01-31', 3, '2012-04-30'),
            array('2012-01-31', 4, '2012-05-31'),
            array('2012-01-31', 5, '2012-06-30'),
            array('2012-01-31', 6, '2012-07-31'),
            array('2012-01-31', 7, '2012-08-31'),
            array('2012-01-31', 8, '2012-09-30'),
            array('2012-01-31', 9, '2012-10-31'),
            array('2012-01-31', 10, '2012-11-30'),
            array('2012-01-31', 11, '2012-12-31'),
            array('2012-01-31', 12, '2013-01-31'),
            array('2013-01-31', 13, '2014-02-28'),
            array('2013-03-31', 1, '2013-04-30'),
            array('2013-05-06', 1, '2013-06-06'),
            array('2013-05-06', 2, '2013-07-06'),
            array('2013-05-06', 3, '2013-08-06'),
            array('2013-05-06', 4, '2013-09-06'),
            array('2013-05-06', 5, '2013-10-06'),
            array('2013-05-06', 6, '2013-11-06'),
            array('2013-05-06', 7, '2013-12-06'),
            array('2013-05-06', 8, '2014-01-06'),
            array('2013-05-06', 9, '2014-02-06'),
            array('2013-05-06', 10, '2014-03-06'),
            array('2013-05-06', 11, '2014-04-06'),
            array('2013-05-06', 12, '2014-05-06'),
            array('2013-12-04', 1, '2014-01-04'),
            array('2013-12-31', 1, '2014-01-31'),
            array('2013-12-31', 2, '2014-02-28'),
            array('2015-12-31', 1, '2016-01-31'),
            array('2015-12-31', 2, '2016-02-29'),
        );
    }
}
