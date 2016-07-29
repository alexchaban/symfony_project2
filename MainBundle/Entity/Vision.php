<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="visions")
 */
class Vision
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
     * @ORM\OneToOne(targetEntity="Post", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", nullable=false)
     */
    protected $post;

    /**
     * @var date
     *
     * @ORM\Column(name="vision_timer", type="datetime", nullable=true)
     */
    protected $visionTimer;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userVisions", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $visionPerson;

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
     * Set visionTimer
     *
     * @param \DateTime $visionTimer
     *
     * @return Vision
     */
    public function setVisionTimer($visionTimer)
    {
        $this->visionTimer = $visionTimer;

        return $this;
    }

    /**
     * Get visionTimer
     *
     * @return \DateTime
     */
    public function getVisionTimer()
    {
        return $this->visionTimer;
    }

    /**
     * Set post
     *
     * @param \MainBundle\Entity\Post $post
     *
     * @return Vision
     */
    public function setPost(\MainBundle\Entity\Post $post)
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

    /**
     * Set visionPerson
     *
     * @param \MainBundle\Entity\User $visionPerson
     *
     * @return Vision
     */
    public function setVisionPerson(\MainBundle\Entity\User $visionPerson)
    {
        $this->visionPerson = $visionPerson;

        return $this;
    }

    /**
     * Get visionPerson
     *
     * @return \MainBundle\Entity\User
     */
    public function getVisionPerson()
    {
        return $this->visionPerson;
    }
}
