<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_edu_locations")
 */
class UserEduLocation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userEduLocations", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="institution", type="string", length=255, nullable=false)
     */
    protected $institution;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    protected $city;

    /**
     * @var date
     *
     * @ORM\Column(name="studied_from", type="datetime", nullable=true)
     */
    protected $studiedFrom;

    /**
     * @var date
     *
     * @ORM\Column(name="studied_till", type="datetime", nullable=true)
     */
    protected $studiedTill;

    /**
     * @var enum
     *
     * @ORM\Column(name="institution_status", type="string", columnDefinition="enum('school', 'high-school', 'college', 'university', 'other')", nullable=true, options={"default": null})
     */
    protected $institutionStatus = null;



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
     * Set institution
     *
     * @param string $institution
     *
     * @return UserEduLocation
     */
    public function setInstitution($institution)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return UserEduLocation
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
     * Set studiedFrom
     *
     * @param \DateTime $studiedFrom
     *
     * @return UserEduLocation
     */
    public function setStudiedFrom($studiedFrom)
    {
        $this->studiedFrom = $studiedFrom;

        return $this;
    }

    /**
     * Get studiedFrom
     *
     * @return \DateTime
     */
    public function getStudiedFrom()
    {
        return $this->studiedFrom;
    }

    /**
     * Set studiedTill
     *
     * @param \DateTime $studiedTill
     *
     * @return UserEduLocation
     */
    public function setStudiedTill($studiedTill)
    {
        $this->studiedTill = $studiedTill;

        return $this;
    }

    /**
     * Get studiedTill
     *
     * @return \DateTime
     */
    public function getStudiedTill()
    {
        return $this->studiedTill;
    }

    /**
     * Set institutionStatus
     *
     * @param string $institutionStatus
     *
     * @return UserEduLocation
     */
    public function setInstitutionStatus($institutionStatus)
    {
        $this->institutionStatus = $institutionStatus;

        return $this;
    }

    /**
     * Get institutionStatus
     *
     * @return string
     */
    public function getInstitutionStatus()
    {
        return $this->institutionStatus;
    }

    /**
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return UserEduLocation
     */
    public function setUser(\MainBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MainBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
