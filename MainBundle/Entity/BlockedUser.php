<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blocked_users")
 */
class BlockedUser
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="blocked_user_id", referencedColumnName="id")
     */
    protected $blockedUser;


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
     * @return BlockedUser
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
     * Set blockedUser
     *
     * @param \MainBundle\Entity\User $blockedUser
     *
     * @return BlockedUser
     */
    public function setBlockedUser(\MainBundle\Entity\User $blockedUser = null)
    {
        $this->blockedUser = $blockedUser;

        return $this;
    }

    /**
     * Get blockedUser
     *
     * @return \MainBundle\Entity\User
     */
    public function getBlockedUser()
    {
        return $this->blockedUser;
    }
}
