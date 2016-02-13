<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LoginHistory
 *
 * @ORM\Table(name="tsk_login_history")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class LoginHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="login_history_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="user_id", nullable=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="login_ip", type="string", nullable=true, length=100)
     */
    private $login_ip;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", nullable=true)
     */
    private $user_agent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_date", type="datetime")
     */
    private $login_date;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set login_date
     *
     * @param \DateTime $loginDate
     * @return LoginHistory
     */
    public function setLoginDate($loginDate)
    {
        $this->login_date = $loginDate;
    
        return $this;
    }

    /**
     * Get login_date
     *
     * @return \DateTime 
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }

    /**
     * getUser 
     * 
     * @access public
     * @return void
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * setUser 
     * 
     * @param User $user 
     * @access public
     * @return void
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function setLoginIp($ip)
    {
        $this->login_ip = $ip;
        return $this;
    }

    public function getLoginIp()
    {
        return $this->login_ip;
    }

    public function setUserAgent($ua)
    {
        $this->user_agent = $ua;
        return $this;
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * onPrePersist 
     * 
     * @ORM\PrePersist
     * @access public
     * @return void
     */
    public function onPrePersist()
    {
        $this->setLoginDate(new \DateTime());
    }
}
