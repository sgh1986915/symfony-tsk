<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use TSK\SchoolBundle\Entity\School;
use TSK\UserBundle\Model\Contact as BaseContact;
use TSK\UserBundle\Model\ContactInterface;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use TSK\UserBundle\Component\Validator\Constraints\PostalCode as AssertPostalCode;


/**
 * Contact
 *
 * @ORM\Table(name="tsk_contact")
 * @ORM\Entity(repositoryClass="TSK\UserBundle\Entity\ContactRepository")
 * @ExclusionPolicy("all")
 */
class Contact extends BaseContact implements ContactInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contact_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\SchoolBundle\Entity\School", inversedBy="schoolContacts", cascade={"persist"})
     * @ORM\JoinTable(name="tsk_contact_school",
     *      joinColumns={@ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id")}
     *      )
     */
    protected $schools;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\UserBundle\Entity\Corporation",  cascade={"persist"})
     * @ORM\JoinTable(name="tsk_contact_corporation",
     *      joinColumns={@ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_corporation_id", referencedColumnName="corporation_id")}
     *      )
     */
    protected $corporations;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Expose
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Expose
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=false, nullable=true)
     * @Assert\Email(checkMX=false, checkHost=false)
     * @Expose
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="email_canonical", type="string", length=255, unique=false, nullable=true)
     */
    protected $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="address1", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $address1;

    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=50, nullable=true)
     * @Expose
     */
    protected $city;

    /** 
     * @var string
     * @ORM\ManyToOne(targetEntity="States") 
     * @ORM\JoinColumn(name="state", referencedColumnName="state_id") 
     * @Expose
     */
    protected $state;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     * @AssertPostalCode()
     * @Expose
     */
    protected $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code_canonical", type="string", length=20, nullable=true)
     */
    protected $postalCodeCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     * @AssertPhoneNumber(defaultRegion="US")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_canonical", type="string", length=20, nullable=true)
     */
    protected $phoneCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=20, nullable=true)
     */
    protected $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_canonical", type="string", length=20, nullable=true)
     */
    protected $mobileCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=20, nullable=true)
     */
    protected $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(name="geocode", type="string", length=255, nullable=true)
     */
    protected $geocode;

    /**
     * @var string
     * @ORM\Column(name="img_path", type="string", length=255, nullable=true)
     * @Gedmo\UploadableFilePath 
     */
    protected $imgPath;

    /**
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    protected $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdDate;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    protected $updatedBy;

    /**
     * @var datetime $updatedDate
     *
     * @Gedmo\Timestampable(on="update")
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->corporations = new ArrayCollection();
    }

    /**
     * setId 
     * 
     * @param integer $id 
     * @access public
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * Set firstName
     *
     * @param string $firstName
     * @return Contact
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Contact
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;
        return $this;
    }

    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return Contact
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    
        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return Contact
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    
        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Contact
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Contact
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     * @return Contact
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    
        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function getFax()
    {
        return $this->fax;
    }
    public function setFax($fax)
    {
        $this->fax = $fax;
        return $this;
    }

    public function getMobile()
    {
        return $this->mobile;
    }
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function getWebsite()
    {
        return $this->website;
    }
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    public function getGeocode()
    {
        return $this->geocode;
    }

    public function setGeocode($geocode)
    {
        $this->geocode = $geocode;
        return $this;
    }

    public function getImgPath()
    {
        return $this->imgPath;
    }

    public function setImgPath($imgPath)
    {
        $this->imgPath = $imgPath;
        return $imgPath;
    }

    public function getLabel()
    {
        return $this->getLastName() . ', ' . $this->getFirstName();
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
 
    /**
     * Get dateOfBirth.
     *
     * @return dateOfBirth.
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }
    
    /**
     * Set dateOfBirth.
     *
     * @param dateOfBirth the value to set.
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }
 
    public function getSchools()
    {
        return $this->schools;
    }

    public function setSchools($schools)
    {
        foreach ($schools as $idx => $school) {
            $this->addSchool($school);
        }
    }

    public function addSchool(School $school)
    {
        if (!$this->schools->contains($school)) {
            $this->schools[] = $school;
        }
    }

    public function removeSchool(School $school)
    {
        return $this->schools->removeElement($school);
    }

    public function getCorporations()
    {
        return $this->corporations;
    }

    public function setCorporations($corporations)
    {
        foreach ($corporations as $idx => $corporation) {
            $this->addCorporation($corporation);
        }
    }

    public function addCorporation(Corporation $corporation)
    {
        if (!$this->getCorporations()->contains($corporation)) {
            $this->corporations[] = $corporation;
        }
    }

    public function removeCorporation(Corporation $corporation)
    {
        return $this->corporations->removeElement($corporation);
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Charge
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    
        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }



    /**
     * Get updatedDate.
     *
     * @return updatedDate.
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
 
    /**
     * Set updatedDate.
     *
     * @param updatedDate the value to set.
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }
 
    /**
     * Get createdBy.
     *
     * @return createdBy.
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
 
    /**
     * Set createdBy.
     *
     * @param createdBy the value to set.
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }
 
    /**
     * Get updatedBy.
     *
     * @return updatedBy.
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
 
    /**
     * Set updatedBy.
     *
     * @param updatedBy the value to set.
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function serialize()
    {
        $str = sprintf("%s-%s-%s-%s-%s-%s-%s-%s-%s", 
            strtolower($this->getFirstName()), 
            strtolower($this->getLastName()), 
            strtolower($this->getAddress1()), 
            strtolower($this->getAddress2()),
            strtolower($this->getCity()),
            strtolower($this->getState()->getStateName()),
            $this->getPostalCodeCanonical(),
            $this->getPhoneCanonical(),
            $this->getMobileCanonical()
        );
        return md5($str);
    }

    /**
     * Get phoneCanonical.
     *
     * @return phoneCanonical.
     */
    public function getPhoneCanonical()
    {
        return $this->phoneCanonical;
    }
 
    /**
     * Set phoneCanonical.
     *
     * @param phoneCanonical the value to set.
     */
    public function setPhoneCanonical($phoneCanonical)
    {
        $this->phoneCanonical = $phoneCanonical;
        return $this;
    }
 
    /**
     * Get mobileCanonical.
     *
     * @return mobileCanonical.
     */
    public function getMobileCanonical()
    {
        return $this->mobileCanonical;
    }
 
    /**
     * Set mobileCanonical.
     *
     * @param mobileCanonical the value to set.
     */
    public function setMobileCanonical($mobileCanonical)
    {
        $this->mobileCanonical = $mobileCanonical;
        return $this;
    }
 
    /**
     * Get postalCodeCanonical.
     *
     * @return postalCodeCanonical.
     */
    public function getPostalCodeCanonical()
    {
        return $this->postalCodeCanonical;
    }
 
    /**
     * Set postalCodeCanonical.
     *
     * @param postalCodeCanonical the value to set.
     */
    public function setPostalCodeCanonical($postalCodeCanonical)
    {
        $this->postalCodeCanonical = $postalCodeCanonical;
        return $this;
    }
}
