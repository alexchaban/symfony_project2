<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="MainBundle\Entity\Repository\UserNotificationRepository")
 * @ORM\Table(name="user_notifications")
 */
class UserNotification
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userNotifications", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="userNotifications", cascade={"persist"})
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    protected $post;

    /**
     * @var date
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    protected $createdOn;

    /**
     * @var date
     *
     * @ORM\Column(name="viewed_on", type="datetime", nullable=true)
     */
    protected $viewedOn;


    public function __construct()
    {
        $this->createdOn = new \DateTime();
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return UserNotification
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set viewedOn
     *
     * @param \DateTime $viewedOn
     *
     * @return UserNotification
     */
    public function setViewedOn($viewedOn)
    {
        $this->viewedOn = $viewedOn;

        return $this;
    }

    /**
     * Get viewedOn
     *
     * @return \DateTime
     */
    public function getViewedOn()
    {
        return $this->viewedOn;
    }

    /**
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return UserNotification
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
     * Set post
     *
     * @param \MainBundle\Entity\Post $post
     *
     * @return UserNotification
     */
    public function setPost(\MainBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \MainBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
