<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="aims")
 */
class Aim
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
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="aim")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="aim")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="SupporterAim", mappedBy="aim", cascade={"persist", "remove"})
     */
    protected $supporterAims;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Aim
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add group
     *
     * @param \MainBundle\Entity\Group $group
     *
     * @return Aim
     */
    public function addGroup(\MainBundle\Entity\Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \MainBundle\Entity\Group $group
     */
    public function removeGroup(\MainBundle\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add post
     *
     * @param \MainBundle\Entity\Aim $post
     *
     * @return Aim
     */
    public function addPost(\MainBundle\Entity\Aim $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \MainBundle\Entity\Aim $post
     */
    public function removePost(\MainBundle\Entity\Aim $post)
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
     * Set slug
     *
     * @param string $slug
     *
     * @return Aim
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add supporterAim
     *
     * @param \MainBundle\Entity\SupporterAim $supporterAim
     *
     * @return Aim
     */
    public function addSupporterAim(\MainBundle\Entity\SupporterAim $supporterAim)
    {
        $this->supporterAims[] = $supporterAim;

        return $this;
    }

    /**
     * Remove supporterAim
     *
     * @param \MainBundle\Entity\SupporterAim $supporterAim
     */
    public function removeSupporterAim(\MainBundle\Entity\SupporterAim $supporterAim)
    {
        $this->supporterAims->removeElement($supporterAim);
    }

    /**
     * Get supporterAims
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupporterAims()
    {
        return $this->supporterAims;
    }
}
