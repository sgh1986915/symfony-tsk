<?php
namespace TSK\ContractBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use TSK\ContractBundle\Entity\Contract;

class ContractFreezeRepository extends EntityRepository
{
    public function getByContract(Contract $contract)
    {
        $query = $this->_em->createQuery('SELECT u FROM TSK\ContractBundle\Entity\ContractFreeze u WHERE u.contract=:contract');
        $query->setParameter(':contract', $contract);
        try {
            $results = $query->getResult();
            return $results;
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function sumByContract(Contract $contract)
    {
        $result = 0;
        $freezes = $this->getByContract($contract);
        foreach ($freezes as $freeze) {
            $result += $freeze->getFreezeDays();
        }
        return $result;
    }

    public function datesOverlapForContract(Contract $contract, \DateTime $startDate, \DateTime $endDate)
    {
        $query = $this->_em->createQuery('SELECT u FROM TSK\ContractBundle\Entity\ContractFreeze u WHERE (u.contract=:contract AND (u.startDate between :newStartDate and :newEndDate)) OR (u.contract=:contract AND (:newStartDate BETWEEN u.startDate and u.endDate))');
        $query->setParameter(':contract', $contract);
        $query->setParameter(':newStartDate', $startDate->format('Y-m-d'));
        $query->setParameter(':newEndDate', $endDate->format('Y-m-d'));
        try {
            $results = $query->getResult();
            return count($results) > 0;
        } catch (NoResultException $e) {
            return false;
        }
    }
}
?>
