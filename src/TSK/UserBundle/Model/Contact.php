<?php
namespace TSK\UserBundle\Model;

abstract class Contact implements ContactInterface
{
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $address1;
    protected $address2;
    protected $city;
    protected $state;
    protected $postalCode;
    protected $postalCodeCanonical;
    protected $phone;
    protected $phoneCanonical;
    protected $mobile;
    protected $mobileCanonical;
    protected $dateOfBirth;
    protected $createdDate;
    protected $updatedDate;
 
    /**
     * Get id.
     *
     * @return id.
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * Set id.
     *
     * @param id the value to set.
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
 
    /**
     * Get firstName.
     *
     * @return firstName.
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
 
    /**
     * Set firstName.
     *
     * @param firstName the value to set.
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }
 
    /**
     * Get lastName.
     *
     * @return lastName.
     */
    public function getLastName()
    {
        return $this->lastName;
    }
 
    /**
     * Set lastName.
     *
     * @param lastName the value to set.
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }
 
    /**
     * Get address1.
     *
     * @return address1.
     */
    public function getAddress1()
    {
        return $this->address1;
    }
 
    /**
     * Set address1.
     *
     * @param address1 the value to set.
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }
 
    /**
     * Get address2.
     *
     * @return address2.
     */
    public function getAddress2()
    {
        return $this->address2;
    }
 
    /**
     * Set address2.
     *
     * @param address2 the value to set.
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }
 
    /**
     * Get city.
     *
     * @return city.
     */
    public function getCity()
    {
        return $this->city;
    }
 
    /**
     * Set city.
     *
     * @param city the value to set.
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
 
    /**
     * Get state.
     *
     * @return state.
     */
    public function getState()
    {
        return $this->state;
    }
 
    /**
     * Set state.
     *
     * @param state the value to set.
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }
 
    /**
     * Get postalCode.
     *
     * @return postalCode.
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }
 
    /**
     * Set postalCode.
     *
     * @param postalCode the value to set.
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
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
 
    /**
     * Get phone.
     *
     * @return phone.
     */
    public function getPhone()
    {
        return $this->phone;
    }
 
    /**
     * Set phone.
     *
     * @param phone the value to set.
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
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
     * Get mobile.
     *
     * @return mobile.
     */
    public function getMobile()
    {
        return $this->mobile;
    }
 
    /**
     * Set mobile.
     *
     * @param mobile the value to set.
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
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
 
    /**
     * Get createdDate.
     *
     * @return createdDate.
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }
 
    /**
     * Set createdDate.
     *
     * @param createdDate the value to set.
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
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
}
?>
