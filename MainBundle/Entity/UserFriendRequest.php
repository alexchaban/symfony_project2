<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_friend_requests")
 */
class UserFriendRequest
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
     * @ORM\Column(name="requested_on", type="datetime", nullable=false)
     */
    protected $requestedOn;

    public function __construct()
    {
        $this->requestedOn = new \DateTime();
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
     * Set requestedOn
     *
     * @param \DateTime $requestedOn
     *
     * @return UserFriendRequest
     */
    public function setRequestedOn($requestedOn)
    {
        $this->requestedOn = $requestedOn;

        return $this;
    }

    /**
     * Get requestedOn
     *
     * @return \DateTime
     */
    public function getRequestedOn()
    {
        return $this->requestedOn;
    }

    /**
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return UserFriendRequest
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
     * @return UserFriendRequest
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
