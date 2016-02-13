<?php
namespace TSK\RankBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ContractBundle\Entity\ContractTemplate;

use Keboola\Csv\CsvFile;

class LoadContractTemplateData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $templateString = <<<'HEREX'
<pdf>
    <dynamic-page>
        <div id="section_header" font-style="bold" font-size="14px" color="black">
                <stylesheet>
                    <attribute name="text-align" value="center"></attribute>
                </stylesheet>
            - AGREEMENT -
        </div>

        <div>
            <p margin-bottom="2px">
                <stylesheet>
                    <attribute name="margin-top" value="10px"></attribute>
                    <attribute name="padding-top" value="1px"></attribute>
                    <attribute name="padding-bottom" value="1px"></attribute>
                    <attribute name="margin-bottom" value="19px"></attribute>
                </stylesheet>
                {{ schoolLegalName }}
                <br />{{ schoolAddress1 }}
                {% if schoolAddress2 %}<br />{{schoolAddress2 }}{% endif %}
                <br />{{ schoolCity }}, {{ schoolState }} {{ schoolPostalCode }}
                <br />{{ schoolPhone}}
            </p>
        </div>
        <div extends="section_header">
            - INTRODUCTION -
        </div>
        <div id="section" margin-bottom="10px" text-align="justify">
This agreement is made between the above named corporation d/b/a Tiger Schulmann's Mixed Martial Arts Center ("TSMMA") and {{student}} or the undersigned legal guardian if he/she is a minor ("Student").  In consideration of the martial arts instruction to be provided by TSMMA as described below Student agrees to pay TSMMA tuition as follows: 1 installment of $289.00, due 5/30/13; thereafter $239 per month due on the 5th day of each month for the next 11 consecutive months, commencing on 07/05/13.  Student agrees to pay a late charge of $12.00 for each monthly payment not paid within 5 days of its due date.  Student acknowledges that pre-authorized credit card charges or automatic bank account debits are the only methods by which monthly payments can be made.  Student hereby expressly authorizes Student's bank/credit card co. to automatically make these payments on his/her bahelf, and to accept the signature on this agreement as authorization for such charges to the same extent as if it were on a withdrawal slip or credit card sales draft.  If any 2 installments remain unpaid past their due dates(a "default"), the entire balance owing under this agreement shall be immediately due and payable, along with the costs of collection and reasonable attorney's fees.  In the event of a default, TSMMA is hereby authorized to charge the full outstanding balance due (including attorney's fees and costs of collection) to the credit card or bank account furnished to TSMMA hereunder.  Tuition charges do not include required protective gear, which must be purchased prior to participation in class.
        </div>

        <div extends="section_header">
            - {{ abbrOrgName }} PROGRAM AND PHILOSOPHY -
        </div>
        <div margin-bottom="10px" text-align="justify">
{{ abbrOrgName }} 

The martial arts program purchased under this agreement is a non-severable course of instruction which includes all testing, promotion, and belt fees except for Black Belt or any degree of Black Belt.  There is a one-time test fee for each degree of Black Belt.  The current fee is ${{ blackBeltFee|number_format(2) }} (subject to increases of not more than ten percent per annum.) 
        </div>

        <div extends="section_header">
            - REFUND POLICY -
        </div>
        <div  margin-bottom="10px" text-align="justify">
The tuition set forth above is for an entire course of pre-arranged instruction and is non-refundable.  This agreement may be cancelled at any time for the reasons expressly set forth herein.  Student may cancel this agreement due to a significant physical disability or a relocation of his/her residence more than 25 miles from a TSMMA center.  To be valid, a cancellation request must be in writing accompanied by adequate documentation verifiying the reason for hte request.  A cancellation request for medical reasons must be accompanied by a doctors note which establishes a significant physical disability that will prevent Student from taking class for at least six months.  A cancellation request due to relocation must be accompanied by a lease, deed, utility bill or other sufficient proof establishing that Student has moved his/her residence more than 25 miles from a TSMMA center.  Notice of cancellation must be provided before the first of a month in order to avoid the tuition charges for that month.  Any refunds due upon cancellation shall be calculated with the student being charged the regular non-discounted monthly tuition fee for all months that have elapsed from the commencement of this membership to the date of cancellation.
        </div>

        <div extends="section_header">
            - WAIVER OF LIABILITY AND MODEL RELEASE -
        </div>
        <div extends="section">
Student understands that participation in martial arts and martial artis instruction involves physical exertion and contact.  Student acknowledges that participation in martial arts and martial arts instruction is dangerous and that there is a risk of injury involved in the activity.  Student agrees to waive any claim, and to release {{ abbrOrgName }} and its employees and agents, from any claim for injuries sustained as a result of participation in martial arts and martial arts classes, including injuries claimed to have been caused by the negligence of {{ abbrOrgName }}, its agents and employees.  This release and waiver does not apply to any act of willful misconduct or gross negligence.  Student hereby consents to the use of his/her likeness in any photographs, film, or video tape in connection with the advertising or promotion of {{ orgName }}s.
        </div>

            <table border.type="none">
                <tr margin-bottom="25px">
                    <td border.type="top" padding="5px" margin-right="5px" font-style="bold">{{ abbrOrgName }}</td>
                    <td border.type="top" padding="5px" margin-left="5px" font-style="bold">STUDENT</td>
                </tr>
                <tr>
                    <td border.type="top" padding="5px" margin-right="5px" font-style="bold">DATE</td>
                    <td border.type="top" padding="5px" margin-left="5px" font-style="bold">PARENT IF MINOR</td>
                </tr>
            </table>
    

</dynamic-page>
</pdf>



HEREX;
            $contractTemplate = new ContractTemplate();
            $contractTemplate->setOrganization($this->getReference('tsk-org'));
            $contractTemplate->setName('Martial Arts Template');
            $contractTemplate->setDescription('Martial Arts Template');
            $contractTemplate->setTemplate($templateString);
            $manager->persist($contractTemplate);

            $membershipType = $this->getReference('tsk-membership_type-martial arts');
            $membershipType->setContractTemplate($contractTemplate);
            $manager->persist($membershipType);

            $manager->flush();
    }

    public function getClauses()
    {
        $clauses = array();
        $clauseCsvFile = $this->container->getParameter('tsk_contract.imports.clause_file');
        $csvFile = new CsvFile($clauseCsvFile, ';');
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $clauses[] = $row;
            }
        }
        return $clauses;
    }

    public function getOrder()
    {
        return 8;
    }
}
