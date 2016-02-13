<?php

namespace TSK\UserBundle;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\Expose;

/**
 * TSKUserCollection 
 * @ExclusionPolicy("all")
 * @XmlRoot("users")
 * 
 */
class TSKUserCollection
{
    /**
     * users 
     * 
     * @var array
     * @access private
     * @XmlList(entry = "user", inline = true)
     * @Expose
     */
    private $users;

    public function __construct(array $users)
    {
        $this->users = $users;
    }
}
