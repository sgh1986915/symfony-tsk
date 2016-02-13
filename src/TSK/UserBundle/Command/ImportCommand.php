<?php
namespace TSK\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Finder\Finder;
use TSK\UserBundle\Entity\Contact;
use TSK\UserBundle\Entity\Corporation;
use TSK\StudentBundle\Entity\Student;
use TSK\StudentBundle\Entity\StudentRank;
use TSK\SchoolBundle\Entity\School;
use TSK\ProgramBundle\Entity\Program;
use TSK\ProgramBundle\Entity\ProgramPaymentPlan;
use TSK\ContractBundle\Entity\Contract;
use TSK\PaymentBundle\Entity\Charge;
use TSK\PaymentBundle\Entity\Payment;
use TSK\PaymentBundle\Entity\Journal;
use TSK\PaymentBundle\Entity\ChargePayment;
use TSK\ScheduleBundle\Entity\ScheduleAttendance;
use TSK\StudentBundle\Entity\TrainingFamilyMembers;
use Doctrine\DBAL\DBALException;
use Keboola\Csv\CsvFile;

class ImportCommand extends ContainerAwareCommand
{

    private $org;
    private $orgId;
    private $school;
    private $schoolLegacyId;
    public function __construct($name = null)
    {
        parent::__construct($name);
        // $this->manager = $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    public $orgCol = 0;
    // public $manager;
    // public function __construct()
    // {
    //     $this->manager = $this->getContainer()->get('doctrine.orm.entity_manager');
    // }

    protected function configure()
    {
        $this->setName('tsk:user:import_data')
        ->setDefinition(array(
            new InputOption('org', 'o', InputOption::VALUE_REQUIRED, 'Organization ID', 1),
            new InputOption('school', null, InputOption::VALUE_REQUIRED, 'School Legacy ID', 110),
        ))
        // ->addOption('org', 'o', InputOption::VALUE_REQUIRED, 'Org ID')
        ->addArgument('dir', InputArgument::REQUIRED, 'Directory where import files are stored')
        ->setDescription('Import data')
        ->setHelp(<<<EOT
The <info>tsk:user:import_data</info> command imports data from a zip file or from a directory of files into the db.

EOT
);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'TSK User Import Data');   
        $output->writeln(array(
            '', 
            'This script will import data from a directory of files',
            '' 
        )); 
    }
    
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {        
            $dialog = $this->getDialogHelper();
 
            $dir = $input->getArgument('dir');
            $this->orgId = $input->getOption('org');
            $this->schoolLegacyId = $input->getOption('school');
            $this->manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->org = $this->manager->getRepository('TSKUserBundle:Organization')->find($this->orgId);
            // $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $accountRepo = $this->manager->getRepository('TSK\PaymentBundle\Entity\Account');
            $this->incomeAccount = $accountRepo->findOneBy(array('name' => 'Inc Fm Students', 'organization' => $this->org));
            $this->deferralAccount = $accountRepo->findOneBy(array('name' => 'Deferred Income', 'organization' => $this->org));

            $incomeTypeRepo = $this->manager->getRepository('TSK\PaymentBundle\Entity\IncomeType');
            $this->tuitionIncomeType = $incomeTypeRepo->findOneBy(array('name' => 'TUITION', 'organization' => $this->org));
            $school = $this->manager->getRepository('TSKSchoolBundle:School')->findOneBy(array('legacyId' => $this->schoolLegacyId));

            $scheduleEntityRepo = $this->manager->getRepository('TSK\ScheduleBundle\Entity\ScheduleEntity');
            $this->dummyScheduleEntity = $scheduleEntityRepo->findOneBy(array('title' => 'Dummy Entity'));
            if (!$school) {
                print 'Invalid school with legacy id ' . $this->schoolLegacyId;
                exit;
                throw new \Exception('Invalid school with legacy id ' . $this->schoolLegacyId);
            } else {
                $this->school = $school;
            }

            if (!is_readable($dir)) {
                throw new \Exception('Unable to read directory ' . $dir);
            }
            $finder = new Finder();
            $finder->files()->in($dir);
            $finder->sortByName();
            $schoolProcessed = 0;
            $programsProcessed = 0;
            $paymentPlansProcessed = 0;
            $contractsProcessed = 0;
            $usersProcessed = 0;
            $loops = 0;
            foreach ($finder as $file) {
                if (!$schoolProcessed && ($file->getFilename() == 'TSK_01.CSV')) { // schools
                    try {
                        $dialog->writeSection($output, 'Processing School Data ...');   
                        $school = $this->processSchool($file);
                        $schoolProcessed = 1;
                        $dialog->writeSection($output, 'School Data Complete ...');   
                
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }
                if ($schoolProcessed && ($file->getFilename() == 'TSK_02.CSV')) { // programs
                    try {
                        $dialog->writeSection($output, 'Processing Program Data ...');   
                        $programs = $this->processPrograms($file);
                        $programsProcessed = 1;
                        $dialog->writeSection($output, 'Program Data Complete ...');   
                        // attach programs to schools
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_03.CSV') { // program payment plans
                    try {
                        $dialog->writeSection($output, 'Processing Program Payment Plan Data ...');   
                        $paymentPlans = $this->processProgramPaymentPlans($file);
                        $paymentPlansProcessed = 1;
                        $dialog->writeSection($output, 'Program Payment Plan Data Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_04.CSV') { // students
                    try {
                        $dialog->writeSection($output, 'Processing Student Data ...');   
                        $contracts = $this->processStudents($file, $dialog, $output);
                        $studentsProcessed = 1;
                        $dialog->writeSection($output, 'Student Data Complete ...');   
                    } catch (\Exception $e) {

                        $dialog = $this->getDialogHelper();
                        $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');   
                        // throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_05.CSV') { // contracts
                    try {
                        $dialog->writeSection($output, 'Processing Contract Data ...');   
                        $contracts = $this->processContracts($file, $dialog, $output);
                        $contractsProcessed = 1;
                        $dialog->writeSection($output, 'Contract Data Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_06.CSV') { // charges
                    try {
                        $dialog->writeSection($output, 'Processing Charge Data ...');   
                        $charges = $this->processCharges($file, $dialog, $output);
                        $chargesProcessed = 1;
                        $dialog->writeSection($output, 'Charge Data Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_07.CSV') { // payments
                    try {
                        $dialog->writeSection($output, 'Processing Payment Data ...');   
                        $payments = $this->processPayments($file, $dialog, $output);
                        $paymentsProcessed = 1;
                        $dialog->writeSection($output, 'Charge Payment Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_08.CSV') { // deferred payments
                    try {
                        $dialog->writeSection($output, 'Processing Deferred Payments Data ...');   
                        $attendances = $this->processDeferrals($file, $dialog, $output);
                        $dialog->writeSection($output, 'Deferred Payments Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_09.CSV') { // attendance
                    try {
                        $dialog->writeSection($output, 'Processing Attendance Data ...');   
                        $attendances = $this->processAttendances($file, $dialog, $output);
                        $dialog->writeSection($output, 'Attendance Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                if ($file->getFilename() == 'TSK_10.CSV') { // attendance
                    try {
                        $dialog->writeSection($output, 'Processing Training Family Member Data ...');   
                        $attendances = $this->processFamilies($file, $dialog, $output);
                        $dialog->writeSection($output, 'Training Family Member Data Complete ...');   
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }


                // print $file->getFilename() . "\n";
                $loops++;
            }
        } catch (\Exception $e) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');   
        }

        if (!empty($results['errors'])) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $results['errors'][0], 'bg=yellow;fg=white');   
        }
    }

    protected function processDeferrals($file, $dialog, $output)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $results = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    try {
                        $results[] = $this->processDeferral($row);
                    } catch (\Exception $e) {
                        $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');
                    }
                }
            }
            $count++;
        }
        return $results;
    }

    protected function processFamilies($file, $dialog, $output)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $results = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    try {
                        $results[] = $this->processFamily($row);
                    } catch (\Exception $e) {
                        $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');
                    }
                }
            }
            $count++;
        }
        return $results;
    }

    protected function processAttendances($file, $dialog, $output)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $attendances = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    try {
                        $attendances[] = $this->processAttendance($row);
                    } catch (\Exception $e) {
                        $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');
                    }
                }
            }
            $count++;
        }
        return $attendances;
    }


    protected function processStudents($file, $dialog, $output)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $contracts = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    try {
                        $contracts[] = $this->processStudent($row);
                    } catch (\Exception $e) {
                        $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');
                    }
                }
            }
            $count++;
        }
        return $contracts;
    }

    protected function processPayments($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $contracts = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    $contracts[] = $this->processPayment($row);
                }
            }
            $count++;
        }
        return $contracts;
    }

    protected function processCharges($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $contracts = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    $contracts[] = $this->processCharge($row);
                }
            }
            $count++;
        }
        return $contracts;
    }

    protected function processContracts($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $contracts = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                if ($row[0][0] != '#') {
                    $contracts[] = $this->processContract($row);
                    // print "$count - ";
                }
            }
            $count++;
        }
        return $contracts;
    }

    protected function processStudent($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $studentRepo = $manager->getRepository('TSK\StudentBundle\Entity\Student');
            $statesRepo = $manager->getRepository('TSK\UserBundle\Entity\States');
            $studentStatusRepo = $manager->getRepository('TSK\StudentBundle\Entity\StudentStatus');
            $rankRepo = $manager->getRepository('TSK\RankBundle\Entity\Rank');
            $schoolRepo = $manager->getRepository('TSK\SchoolBundle\Entity\School');
            $row[0] = preg_replace('/-/', '', $row[0]);
            $student = $studentRepo->findOneBy(array('legacyStudentId' => $row[0]));
            if (!$student) {
                $student = new Student();
                $contactManager = $this->getContainer()->get('tsk_user.contact_manager');
                $contact = $contactManager->createContact();
                $contact->setOrganization($this->org);
                $contact->setFirstName($row[1]);
                $contact->setLastName($row[2]);
                $contact->setEmail($row[13]);
                $contact->setAddress1($row[3]);
                $contact->setAddress2($row[4]);
                $contact->setCity($row[5]);
                $contact->setState($statesRepo->find($row[6]));
                $contact->setPostalCode($row[7]);
                $contact->setPhone($row[8]);
                $contact->setMobile($row[9]);
                $contact->setDateOfBirth(new \DateTime($row[10]));
                $contact->addSchool($this->school);
                $contactManager->updateCanonicalFields($contact);
                $student->setContact($contact);
                $student->setLegacyStudentId($row[0]);
                $student->setStudentStatus($studentStatusRepo->findOneBy(array('name' => 'active')));
                $rank = $rankRepo->findOneBy(array('fullDescription' => $row[12]));
                if (!$rank) {
                    print 'Bad rank [' . $row[12] . ']' . "\n";

                    try {
                    } catch (\Exception $e) {
                    // throw new \Exception('Bad Rank [' . $row[12] . ']');
                    }
                } else {
                    $student->setRank($rank);
                    $studentRank = new StudentRank();
                    $studentRank->setStudent($student);
                    $studentRank->setRank($rank);
                    $studentRank->setRankType($rank->getRankType());
                    $studentRank->setAwardedDate(new \DateTime($row[14]));
                    $manager->persist($studentRank);
                }

                $manager->persist($student);
                $manager->flush();

            }
        }
    }


    protected function processDeferral($row)
    {
        if ($row) {
            $paymentRepo = $this->manager->getRepository('TSK\PaymentBundle\Entity\Payment');
            $payment = $paymentRepo->findOneBy(array('legacyPaymentId' => $row[0]));
            if (!$payment) {
                print "Can't find payment " . $row[0] . "\n"; 
                return null;
            }

            $chargePaymentRepo = $this->manager->getRepository('TSK\PaymentBundle\Entity\ChargePayment');
            $cps = $chargePaymentRepo->findBy(array('payment' => $payment));

            foreach ($cps as $cp) {
                $journal = new Journal();
                $journal->setSchool($this->school);
                $journal->setCharge($cp->getCharge());
                $journal->setPayment($cp->getPayment());
                $journal->setJournalDate(new \DateTime($row[3]));
                $journal->setDebitAmount($row[2]);
                $journal->setDebitAccount($this->deferralAccount);
                $journal->setCreditAmount($row[2]);
                $journal->setCreditAccount($this->incomeAccount);
                $this->manager->persist($journal);
                $this->manager->flush();
            }

        }
    }

    protected function processFamily($row)
    {
        if ($row) {
            $studentRepo = $this->manager->getRepository('TSK\StudentBundle\Entity\Student');
            $primaryStudent = $studentRepo->findOneBy(array('legacyStudentId' => $row[0]));
            $student = $studentRepo->findOneBy(array('legacyStudentId' => $row[1]));
            if (!$primaryStudent) {
                print "Can't find primary student " . $row[0] . "\n"; exit;
            }
            if (!$student) {
                print "Can't find student " . $row[1] . "\n"; exit;
            }

            $tfmRepo = $this->manager->getRepository('TSK\StudentBundle\Entity\TrainingFamilyMembers');
            $oldTfm = $tfmRepo->findOneBy(
                    array(
                        'primaryStudent' => $primaryStudent,
                        'student' => $student
                        ));

            if (!$oldTfm) {
                $tfm = new TrainingFamilyMembers();
                $tfm->setPrimaryStudent($primaryStudent);
                $tfm->setStudent($student);
                $tfm->setDateAdded(new \DateTime($row[2]));
                $tfm->setOrdinalPosition($row[3]);
                try {
                    $this->manager->persist($tfm);
                    $this->manager->flush();
                } catch (DBALException $e) {
                } catch (\Exception $e) {
                    ld($e);
                    print $e->getMessage(); exit;
                }
            }
        }
    }

    protected function processAttendance($row)
    {
        if ($row) {
            $attendanceRepo = $this->manager->getRepository('TSK\ScheduleBundle\Entity\ScheduleAttendance');
            $attDate = new \DateTime($row[3]);
                $classRepo = $this->manager->getRepository('TSK\ClassBundle\Entity\Classes');
                $studentRepo = $this->manager->getRepository('TSK\StudentBundle\Entity\Student');
                $class = $classRepo->find($row[1]);
                if (!$class) {
                    print "Can't find class " . $row[1] . "\n"; exit;
                }
                $student = $studentRepo->findOneBy(array('legacyStudentId' => $row[2]));
                if (!$student) {
                    print "Can't find student " . $row[2] . "\n"; exit;
                }

            $oldAttendance = $attendanceRepo->findOneBy(
                array(
                    'attDate' => $attDate,
                    'class' => $class,
                    'student' => $student
            ));
           
            if (!$oldAttendance) {
                $attendance = new ScheduleAttendance();
                $attendance->setSchool($this->school);
                $attendance->setSchedule($this->dummyScheduleEntity);
                $attendance->setClass($class);
                $attendance->setStudent($student);
                $attendance->setAttDate($attDate);
                $attendance->setStatus('present');
                $attendance->setNotes($row[4]);
                try {
                    $this->manager->persist($attendance);
                    $this->manager->flush();
                } catch (DBALException $e) {
                } catch (\Exception $e) {
                    ld($e);
                    print $e->getMessage(); exit;
                }
            }
        }
    }

    protected function processPayment($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            // $accountRepo = $manager->getRepository('TSK\PaymentBundle\Entity\Account');
            // $account = $accountRepo->findOneBy(array('name' => 'Inc Fm Students', 'organization' => $this->org));
            $paymentTypeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\PaymentType');
            $paymentMethodRepo = $manager->getRepository('TSK\PaymentBundle\Entity\PaymentMethod');
            // $incomeTypeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\IncomeType');
            // $incomeType = $incomeTypeRepo->findOneBy(array('name' => 'TUITION', 'organization' => $this->org));
            $paymentRepo = $manager->getRepository('TSK\PaymentBundle\Entity\Payment');
            $oldPayment = $paymentRepo->findOneBy(array('legacyPaymentId' => $row[1]));
            $isCash = true;
            if (!$oldPayment) {
                switch ($row[4]) {
                    case 'VISA':
                        $row[4] = 'VISA';
                    break;
    
                    case 'MC':
                        $row[4] = 'MASTERCARD';
                    break;

                    case 'SCHOOL CREDIT':
                    case 'CREDIT':
                        $row[4] = 'CREDIT';
                        $isCash = false;
                    break;

                    case 'AMEX':
                        $row[4] = 'AMERICAN EXPRESS';
                    break;

                    case 'CHECK':
                        $row[4] = 'CHECK';
                    break;

                    case 'DISCOVER':
                        $row[4] = 'DISCOVER';
                    break;

                    case 'CASH':
                        $row[4] = 'CASH';
                    break;
                }
                $payment = new Payment();
                $payment->setLegacyPaymentId($row[1]);
                $payment->setSchool($this->school);
                $payment->setPaymentType($paymentTypeRepo->findOneBy(array('name' => $row[3], 'organization' => $this->org)));
                $paymentMethod = $paymentMethodRepo->findOneBy(array('name' => $row[4], 'organization' => $this->org));
                if (!$paymentMethod) {
                    print "cannot find paymentMethod for " . $row[4]; exit;
                }
                $payment->setPaymentMethod($paymentMethod);
                $payment->setPaymentAmount($row[5]);
                $payment->setCreatedDate(new \DateTime($row[6]));
                $payment->setCreatedUser('mhill');
                $payment->setDescription($row[7]);
                $payment->setRefNumber($row[8]);
                $payment->setIsVoided($row[9]);
                $payment->setIsCash($isCash);
                $chargeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\Charge');
                $charge = $chargeRepo->findOneBy(array('legacyChargeId' => $row[2]));
                if ($charge) {
                    $cp = new ChargePayment();
                    $cp->setCharge($charge);
                    $cp->setPayment($payment);
                    $cp->setAmount(min($charge->getAmount(), $payment->getPaymentAmount()));
                    $manager->persist($cp);
                }
                $manager->persist($payment);
                $manager->flush();
            }
        }
    }
    protected function processCharge($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $chargeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\Charge');
            // $accountRepo = $manager->getRepository('TSK\PaymentBundle\Entity\Account');
            // $incomeAccount = $accountRepo->findOneBy(array('name' => 'Inc Fm Students', 'organization' => $this->org));
            // $deferralAccount = $accountRepo->findOneBy(array('name' => 'Deferred Income', 'organization' => $this->org));
            // $incomeTypeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\IncomeType');
            // $incomeType = $incomeTypeRepo->findOneBy(array('name' => 'TUITION', 'organization' => $this->org));
            $oldCharge = $chargeRepo->findOneBy(array('legacyChargeId' => $row[1]));
            $contractRepo = $manager->getRepository('TSK\ContractBundle\Entity\Contract');
            if (!$oldCharge) {
                $charge = new Charge();
                $charge->setLegacyChargeId($row[1]);
                $charge->setSchool($this->school);
                $charge->setAccount($this->incomeAccount);
                $charge->setDeferralAccount($this->deferralAccount);
                $charge->setIncomeType($this->tuitionIncomeType);
                $charge->setDueDate(new \DateTime($row[4]));
                $charge->setAmount($row[3]);
                $charge->setDescription($row[5]);
                $manager->persist($charge);
                $contract = $contractRepo->findOneBy(array('legacyContractId' => $row[0]));
                if ($contract) {
                    $contract->addCharge($charge);
                    $manager->persist($contract);
                }
                $manager->flush();
            }
        }
    }

    protected function processContract($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $contractRepo = $manager->getRepository('TSK\ContractBundle\Entity\Contract');
            $programRepo = $manager->getRepository('TSK\ProgramBundle\Entity\Program');
            $schoolRepo = $manager->getRepository('TSK\SchoolBundle\Entity\School');
            $oldContract = $contractRepo->findOneBy(array('legacyContractId' => $row[0]));
            if (!$oldContract) {
                $contract = new Contract();
                $contract->setOrganization($this->org);
                $contract->setLegacyContractId($row[0]);
                $students = $this->parseStudents($row[1]);
                $today = new \DateTime();
                $contractExpiry = new \DateTime($row[4]);
                $studentStatusRepo = $manager->getRepository('TSK\StudentBundle\Entity\StudentStatus');
                $expiredStatus = $studentStatusRepo->findOneBy(array('name' => 'expired'));

                foreach ($students as $student) {
                    $contract->addStudent($student);
                    // If contract has already expired, then mark student as expired
                    if ($contractExpiry < $today) {
                        $student->setStudentStatus($expiredStatus);
                        $manager->persist($student);
                    }
                }
                $program = $programRepo->findOneBy(array('programName' => $row[2]));
                if (!$program) {
                    throw new \Exception('Invalid program ' . $row[2]);
                }
                $contract->setProgram($program);
                $contract->setContractExpiry(new \DateTime($row[4]));
                $contract->setContractNumTokens($row[5]);
                $contract->setDeferralRate($row[6]);
                $contract->setDeferralDistributionStrategy($row[7]);
                $contract->setContractStartDate(new \DateTime($row[11]));
                $contract->setSchool($this->school);
                $contract->setIsActive(true);
                if ($row[12] < 9999) {
                    $payments = $this->distributeInt($row[10], $row[9]);
                    $paymentTerms = array(
                        'paymentFrequency' => 'monthly',
                        'summary' => $this->generateSummary($payments),
                        'principal' => $row[10],
                        'payments' => $payments
                    );
                } else {
                    $paymentTerms = array(
                        'paymentFrequency' => 'monthly',
                        'summary' => 'summary',
                        'principal' => $row[10],
                        'payments' => array()
                    );
                }
                $contract->setPaymentTerms(array('paymentsData' => json_encode($paymentTerms)));
                $doc = $contract->renderContractVersion();
                $manager->persist($contract);
                $manager->persist($doc);
                $manager->flush();
            }
        }
    }

    public function parseStudents($str)
    {
        $students = array();
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $studentRepo = $manager->getRepository('TSK\StudentBundle\Entity\Student');
        $studentStatusRepo = $manager->getRepository('TSK\StudentBundle\Entity\StudentStatus');
        $activeStatus = $studentStatusRepo->findOneBy(array('name' => 'active'));
        $studentLegacyIds = preg_split('/;/', $str);
        foreach ($studentLegacyIds as $studentLegacyId) {
            $studentLegacyId = preg_replace('/-/', '', $studentLegacyId);
            $student = $studentRepo->findOneBy(array('legacyStudentId' => $studentLegacyId));
            if (!$student) {
                $student = new Student();
                $student->setLegacyStudentId($studentLegacyId);
                $contact = new Contact();
                $contact->setFirstName('dummy');
                $contact->setLastName('record');
                $contact->setOrganization($this->org);
                $student->setContact($contact);
                $student->setStudentStatus($activeStatus);
                $manager->persist($student);
                $manager->flush();
            }
            $students[] = $student;
        }
        return $students;
    }


    protected function processProgramPaymentPlans($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $paymentPlans = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                $paymentPlans[] = $this->processProgramPaymentPlan($row);
            }
            $count++;
        }
        return $paymentPlans;
    }

    protected function processProgramPaymentPlan($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $programPaymentPlanRepo = $manager->getRepository('TSK\ProgramBundle\Entity\ProgramPaymentPlan');
            $programRepo = $manager->getRepository('TSK\ProgramBundle\Entity\Program');
            $program = $programRepo->findOneBy(array('legacyProgramId' => $row[0]));
            if ($program) {
                $programPaymentPlan = $programPaymentPlanRepo->findOneBy(array('program' => $program, 'name' => $row[1]));
                if (!$programPaymentPlan) {
                    $programPaymentPlan = new ProgramPaymentPlan();
                    $programPaymentPlan->setProgram($program);
                    $programPaymentPlan->setName($row[1]);
                    $programPaymentPlan->setPrice($row[2]);
                    $programPaymentPlan->setPaymentsData(array('paymentsData' => json_encode($this->processPaymentsData($row[3]))));
                    $programPaymentPlan->setDeferralDurationMonths($row[4]);
                    $programPaymentPlan->setDeferralDistributionStrategy($row[5]);
                    $programPaymentPlan->setDeferralRate($row[6]);
                    $programPaymentPlan->setIsActive(true);
                    $manager->persist($programPaymentPlan);
                    $manager->flush();
                }
            } else {
                throw new \Exception('Unable to find program ' . $row[0]);
            }
        }
    }

    public function processPaymentsData($str)
    {
        $arr = preg_split('/;/', $str);
        $arr = array_map('intval', $arr);
        $result['principal'] = array_sum($arr);
        $result['payments'] = $arr;
        $result['paymentFrequency'] = "monthly";
        $result['summary'] = $this->generateSummary($arr);
        return $result;
    }

    protected function generateSummary($payments)
    {
        $result = '';
        if ($payments) {
            foreach ($payments as $payment) {
                if (empty($pjs[$payment])) {
                    $pjs[$payment] = 1;
                } else {
                    $pjs[$payment]++;
                }
            }
            $result = count($payments) . ' payment(s) totalling $' . array_sum($payments);

            foreach ($pjs as $payment => $count) {
                $vars[] = "$count @ \$$payment";
            }
            $result .= ' (' . join(', ', $vars) . ')';
        }
        return $result;
    }

    protected function processPrograms($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        $programs = array();
        foreach ($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                $programs[] = $this->processProgram($row);
            }
            $count++;
        }
        return $programs;
    }

    protected function processProgram($row)
    {
        if ($row) {
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $programRepo = $manager->getRepository('TSK\ProgramBundle\Entity\Program');
            // check for dupe
            $oldProgram = $programRepo->findOneBy(array('legacyProgramId' => $row[9]));
            if (!$oldProgram) {
                $programTypeRepo = $manager->getRepository('TSK\ProgramBundle\Entity\ProgramType');
                $membershipTypeRepo = $manager->getRepository('TSK\ProgramBundle\Entity\MembershipType');
                $discountTypeRepo = $manager->getRepository('TSK\PaymentBundle\Entity\DiscountType');

                $program = new Program();
                $program->addSchool($this->school);
                $program->setOrganization($this->org);
                $program->setLegacyProgramId($row[9]);
                $programType = $programTypeRepo->findOneBy(array('name' => $row[0]));
                $program->setProgramType($programType);
                $membershipType = $membershipTypeRepo->findOneBy(array('name' => $row[1]));
                $program->setMembershipType($membershipType);
                $discountType = $discountTypeRepo->findOneBy(array('name' => $this->lookupDiscountTypes($row[2])));
                $program->setDiscountType($discountType);
                $program->setProgramName($row[3]);
                $program->setDescription($row[3]);
                $program->setLegalDescription($row[4]);
                $program->setNumTokens($row[5]);
                $program->setDurationDays($row[6]);
    
                // In the program table expiration date is stored as either integer or null
                $expirationDate = (int)$row[7];
                if ($expirationDate > 0) {
                    $expirationDate = strtotime($expirationDate);
                } else {
                    $expirationDate = null;
                }
                $program->setExpirationDate($expirationDate);
                $program->setIsActive(true);
                $manager->persist($program);
                $manager->flush();
                return $program;
            } else {
                return $oldProgram;
            }
        }
    }

    protected function lookupDiscountTypes($str)
    {
        $result = null;
        switch ($str)
        {
            case 'none':
                $result = 'regular';
            break;

            case '2nd family member':
                $result = $str;
            break;

            case '3rd+ family member':
                $result = '3rd family member';
            break;
        }
        return $result;
    }

    protected function processSchool($file)
    {
        $csvFile = new CsvFile($file);
        $count = 0;
        foreach($csvFile as $row) {
            if ($count > 0) {
                $row = array_map('trim', $row);
                // try {

                    $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
                    // check for school dupe
                    $schoolRepo = $manager->getRepository('TSKSchoolBundle:School');
                    $oldSchool = $schoolRepo->findOneBy(array('legacyId' => $row[0]));
                    if (!$oldSchool) {
                        $statesRepo = $manager->getRepository('TSKUserBundle:States');

                        $contactManager = $this->getContainer()->get('tsk_user.contact_manager');
                        $contact = $contactManager->createContact();
                        $contact->setOrganization($this->org);
                        $contact->setFirstName($row[1]);
                        $contact->setLastName($row[2]);
                        $contact->setEmail($row[3]);
                        $contact->setAddress1($row[4]);
                        $contact->setAddress2($row[5]);
                        $contact->setCity($row[6]);
                        $contact->setState($statesRepo->find($row[7]));
                        $contact->setPostalCode($row[8]);
                        $contact->setPhone($row[9]);
                        $contact->setMobile($row[10]);
                        $contact->setWebsite($row[11]);

                        $contactManager->updateCanonicalFields($contact);
                        $corporation = new Corporation();
                        $corporation->setTaxId($row[14]);
                        $corporation->setLegalName($row[12]);
                        $corporation->setDba($row[13]);
                        $corporation->setAccountNum($row[17]);
                        $corporation->setRoutingNum($row[16]);
                        $contact->addCorporation($corporation);
                        $school = new School();
                        $school->setName($row[1] .' ' .$row[2]);
                        $school->setLegacyId($row[0]);
                        $school->setDeferralRate(0.75);
                        $school->setDistributionStrategy('accelerated');
                        $school->setPaymentMax(1000);
                        $school->setLateGraceDays(5);
                        $school->setLatePaymentCharge(25);
                        $school->setContact($contact);
                        // $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
                        $manager->persist($school);
                        $manager->flush();
                        $this->school = $school;
                        // $this->processRow($row);
                        // $count++;
                    } else {
                        // print 'we already have this school!';
                    }
                // } catch (DBALException $e) {
                //     throw $e;
                //     print 'caught it';
                // } catch (\Exception $e) {
                //     throw $e;
                // }

            }
            $count++;
        }
    }

    protected function processRow($row)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $statesRepo = $manager->getRepository('TSKUserBundle:States');
        $schoolRepo = $manager->getRepository('TSKSchoolBundle:School');
        $studStatusRepo = $manager->getRepository('TSKStudentBundle:StudentStatus');
        $studStatus = $studStatusRepo->find(1);
        $contact = new Contact();
        $contact->setId($row[0]);
        $contact->setFirstName($row[2]);
        $contact->setLastName($row[3]);
        $contact->setOrganization($this->org);
        $contact->setEmail($row[4]);
        $contact->setAddress1($row[6]);
        $contact->setAddress2($row[7]);
        $contact->setCity($row[8]);
        $contact->setState($statesRepo->find($row[9]));
        $contact->setPostalCode($row[10]);
        $contact->setPhone($row[11]);
        $contact->setMobile($row[12]);
        $contact->setFax($row[13]);
        $contact->setWebsite($row[14]);
        $contact->setGeocode($row[15]);
        $contact->setImgPath($row[16]);
        $contact->setDateOfBirth(new \DateTime($row[17]));
        $contact->addSchool($this->school);

        $student = new Student();
        $student->setContact($contact);
        $student->setStudentStatus($studStatus);

        $metadata = $manager->getClassMetaData(get_class($contact));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        // $manager->persist($contact);
        $manager->persist($student);
        $manager->flush();
    }

    protected function importFiles($mappingFile, $importFiles)
    {
        $tables = array('tsk_contact', 'tsk_corporation', 'tsk_contact_corporation', 'tsk_contact_school', 'tsk_contract', 'tsk_contract_charge', 'tsk_contract_school', 'tsk_corporation_contact', 'tsk_prospective', 'tsk_school', 'tsk_student', 'tsk_student_contract', 'tsk_contract_token');
        $fileCount = 0;
        foreach ($importFiles as $importFile) {
            $this->importFile($tables[$fileCount++], $importFile);
            if ($fileCount > 5) {
                break;
            }
        }
    }

    protected function importFile($tableName, $importFile)
    {
        if (!is_readable($importFile)) {
            throw new \Exception('Unable to read importFile ' . $importFile);
        }
        $data = str_getcsv(file_get_contents($importFile));
        $rowCount = 0;
        $colCount = 0;
        foreach ($data as $d => $rest) {
            if ($rest == 'gecode')  {
                $rest = 'geocode';
            }
            if (preg_match('/\r/', $rest))  {
                list($last, $first)  = preg_split('/\r\n/', $rest);
                $rows[$rowCount++][] = trim($last);
                $rows[$rowCount][] = trim($first);
            } else {
                $rows[$rowCount][] = trim($rest);
            }

            if ($rest == 'fk_org_id') {
                $this->orgCol = $colCount;
            }
            $colCount++;
        }
        $cols = array_shift($rows);
        $this->importRow($tableName, $cols, $rows);
    }

    protected function importRow($tableName, $cols, $rows)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $imported=0;
        foreach ($rows as $values) {
            $values[1] = 17;
            $values = array_map(function($val) { return "'" . addslashes($val) . "'"; }, $values);
            $sql = 'INSERT IGNORE INTO ' .$tableName . ' ('.join($cols, ', ').') VALUES ('.join($values, ', ').')';
            try {
                $em->getConnection()->exec($sql);
                $imported++;
            } catch (\Exception $e) {
                print_r($sql);
                throw $e;
                exit;
            }
        }

        print "imported $imported rows";
    }

    /**
     * distributeInt 
     * Distributes integer $int evenly into an array containing
     * $slots buckets with any extra going to final bucket
     * 
     * @param mixed $int 
     * @param mixed $slots 
     * @access public
     * @return void
     */
    public function distributeInt($int, $slots)
    {
        $int = (int) $int;
        $slots = (int)$slots;
        if ($slots) {
            $results = array_fill(0, $slots, floor($int / $slots));
            for ($i=0; $i < $int % $slots; $i++) {
                $results[count($results) - 1] += 1;
            }
            return $results;
        } else {
            return null;
        }
    }
}
