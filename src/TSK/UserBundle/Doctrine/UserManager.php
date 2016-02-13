<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TSK\UserBundle\Doctrine;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserManagerInterface;

class UserManager extends BaseUserManager implements UserManagerInterface
{
   /**
     * Finds a user by email
     *
     * @param string $email
     *
     * @return UserInterface
     */
    public function findUserByEmail($email)
    {
        $result = $this->objectManager->createQuery("SELECT u FROM TSK\UserBundle\Entity\User u JOIN u.contact c WHERE c.emailCanonical = :email")->setParameter('email', $email)->getOneOrNullResult();
        return $result;

/*
        $em = $this->objectManager->getRepository('TSKUserBundle:Contact');
*/
        // $dbh = $em->getCurrentConnection();
        // $foo = $em->findBy(array('emailCanonical' => $this->canonicalizeEmail($email)));
        // $dql = 'select u, c from User u JOIN u.contact WHERE c.email_canonical = "malaney@gmail.com"';
        // $sql = 'select c.email_canonical from contact c join user u on u.fk_contact_id=c.contact_id where c.email_canonical="malaney@gmail.com"';

        // return $this->findUserBy(array('fkContact' => $foo->contactId));
    }


}
