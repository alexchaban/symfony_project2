<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="MainBundle\Entity\Repository\SupporterAimRepository")
 * @ORM\Table(name="supporter_aims")
 */
class SupporterAim
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="supporterAimss", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Aim", inversedBy="supporterAims", cascade={"persist"})
     * @ORM\JoinColumn(name="aim_id", referencedColumnName="id")
     */
    protected $aim;



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
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return SupporterAim
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

    /**
     * Set aim
     *
     * @param \MainBundle\Entity\Aim $aim
     *
     * @return SupporterAim
     */
    public function setAim(\MainBundle\Entity\Aim $aim = null)
    {
        $this->aim = $aim;

        return $this;
    }

    /**
     * Get aim
     *
     * @return \MainBundle\Entity\Aim
     */
    public function getAim()
    {
        return $this->aim;
    }
}
