<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="groups")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var enum
     *
     * @ORM\Column(name="public", type="string", columnDefinition="enum('true', 'false')", nullable=false, options={"default": "false"})
     */
    protected $public = 'false';

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="group")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="PostComment", mappedBy="group")
     */
    protected $postComments;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Aim", inversedBy="groups")
     * @ORM\JoinColumn(name="aim_id", referencedColumnName="id")
     */
    protected $aim;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="groups")
     * @ORM\JoinColumn(name="administrator_id", referencedColumnName="id")
     */
    protected $administrator;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->postComments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set public
     *
     * @param string $public
     *
     * @return Group
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return string
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Add post
     *
     * @param \MainBundle\Entity\Post $post
     *
     * @return Group
     */
    public function addPost(\MainBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \MainBundle\Entity\Post $post
     */
    public function removePost(\MainBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add postComment
     *
     * @param \MainBundle\Entity\PostComment $postComment
     *
     * @return Group
     */
    public function addPostComment(\MainBundle\Entity\PostComment $postComment)
    {
        $this->postComments[] = $postComment;

        return $this;
    }

    /**
     * Remove postComment
     *
     * @param \MainBundle\Entity\PostComment $postComment
     */
    public function removePostComment(\MainBundle\Entity\PostComment $postComment)
    {
        $this->postComments->removeElement($postComment);
    }

    /**
     * Get postComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostComments()
    {
        return $this->postComments;
    }

    /**
     * Set aim
     *
     * @param \MainBundle\Entity\Aim $aim
     *
     * @return Group
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

    /**
     * Set administrator
     *
     * @param \MainBundle\Entity\User $administrator
     *
     * @return Group
     */
    public function setAdministrator(\MainBundle\Entity\User $administrator = null)
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * Get administrator
     *
     * @return \MainBundle\Entity\User
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }
}
