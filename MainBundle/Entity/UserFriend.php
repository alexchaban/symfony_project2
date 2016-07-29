<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_friends")
 */
class UserFriend
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
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id")
     */
    protected $friend;

    /**
     * @var date
     *
     * @ORM\Column(name="request_accepted", type="datetime", nullable=false)
     */
    protected $friendsSince;

    public function __construct()
    {
        $this->friendsSince = new \DateTime();
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
     * Set friendsSince
     *
     * @param \DateTime $friendsSince
     *
     * @return UserFriend
     */
    public function setFriendsSince($friendsSince)
    {
        $this->friendsSince = $friendsSince;

        return $this;
    }

    /**
     * Get friendsSince
     *
     * @return \DateTime
     */
    public function getFriendsSince()
    {
        return $this->friendsSince;
    }

    /**
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return UserFriend
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
     * Set friend
     *
     * @param \MainBundle\Entity\User $friend
     *
     * @return UserFriend
     */
    public function setFriend(\MainBundle\Entity\User $friend = null)
    {
        $this->friend = $friend;

        return $this;
    }

    /**
     * Get friend
     *
     * @return \MainBundle\Entity\User
     */
    public function getFriend()
    {
        return $this->friend;
    }
}
