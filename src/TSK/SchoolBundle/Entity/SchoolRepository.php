<?php
namespace TSK\SchoolBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class SchoolRepository extends EntityRepository
{

    public function findDupe($school)
    {
        $dupeSchool = $this->findOneBy(array('legacyId' => $school->getLegacyId()));
        return $dupeSchool;
    }
}
