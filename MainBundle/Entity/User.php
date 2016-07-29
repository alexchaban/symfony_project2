<?php

namespace MainBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="MainBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
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
     * @ORM\Column(name="firstname", type="string", length=25, nullable=true)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=25, nullable=true)
     */
    protected $lastname;

    /**
     * @var date
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    protected $birthday;

    /**
     * @var enum
     *
     * @ORM\Column(name="status", type="string", columnDefinition="enum('student', 'unemployed', 'unemployed', 'employed', 'self-employed', 'other')", nullable=true, options={"default": null})
     */
    protected $status = null;

    /**
     * @var enum
     *
     * @ORM\Column(name="gender", type="string", columnDefinition="enum('male', 'female')", nullable=true, options={"default": null})
     */
    protected $gender = null;

    /**
     * @var string
     *
     * @ORM\Column(name="current_city", type="string", length=255, nullable=true)
     */
    protected $currentCity = null;

    /**
     * @var date
     *
     * @ORM\Column(name="last_activity", type="datetime", nullable=true)
     */
    protected $lastActivity;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_supporter", type="boolean", options={"default": false})
     */
    protected $supporter = false;

    /**
     * @var array
     *
     * @ORM\Column(name="supporter_aims", type="array", nullable=true)
     */
    protected $supporterAims = array();

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="user")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="PostComment", mappedBy="user")
     */
    protected $postComments;

    /**
     * @ORM\OneToMany(targetEntity="Vision", mappedBy="visionPerson")
     */
    protected $userVisions;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="administrator")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="UserEduLocation", mappedBy="user")
     */
    protected $userEduLocations;

    /**
     * @ORM\OneToMany(targetEntity="UserNotification", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $userNotifications;

    /**
     * @ORM\OneToMany(targetEntity="SupporterAim", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $supporterAimss;


    /**
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes = {"image/jpeg", "image/png", "image/tiff"},
     *     maxSizeMessage = "The maxmimum allowed file size is 10MB.",
     *     mimeTypesMessage = "Only the filetypes image are allowed."
     * )
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    private $temp;

    public function __construct()
    {
        parent::__construct();
        $this->setUsername(uniqid());
        $this->supporterAims = array();
    }


    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../private/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        $username = $this->getUsername();
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        //return 'uploads/images';
        return 'uploads/images';// . $username;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->path);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file && file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Set path
     *
     * @param string $path
     * @return DocumentImage
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        //$this->setUsername($email);

        return $this;
    }


    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Add post
     *
     * @param \MainBundle\Entity\Post $post
     *
     * @return User
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
     * @return User
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
     * Add userVision
     *
     * @param \MainBundle\Entity\Vision $userVision
     *
     * @return User
     */
    public function addUserVision(\MainBundle\Entity\Vision $userVision)
    {
        $this->userVisions[] = $userVision;

        return $this;
    }

    /**
     * Remove userVision
     *
     * @param \MainBundle\Entity\Vision $userVision
     */
    public function removeUserVision(\MainBundle\Entity\Vision $userVision)
    {
        $this->userVisions->removeElement($userVision);
    }

    /**
     * Get userVisions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserVisions()
    {
        return $this->userVisions;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set currentCity
     *
     * @param string $currentCity
     *
     * @return User
     */
    public function setCurrentCity($currentCity)
    {
        $this->currentCity = $currentCity;

        return $this;
    }

    /**
     * Get currentCity
     *
     * @return string
     */
    public function getCurrentCity()
    {
        return $this->currentCity;
    }

    /**
     * Add userEduLocation
     *
     * @param \MainBundle\Entity\UserEduLocation $userEduLocation
     *
     * @return User
     */
    public function addUserEduLocation(\MainBundle\Entity\UserEduLocation $userEduLocation)
    {
        $this->userEduLocations[] = $userEduLocation;

        return $this;
    }

    /**
     * Remove userEduLocation
     *
     * @param \MainBundle\Entity\UserEduLocation $userEduLocation
     */
    public function removeUserEduLocation(\MainBundle\Entity\UserEduLocation $userEduLocation)
    {
        $this->userEduLocations->removeElement($userEduLocation);
    }

    /**
     * Get userEduLocations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserEduLocations()
    {
        return $this->userEduLocations;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     *
     * @return User
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    public function isActiveNow()
    {
        $this->lastActivity = new \DateTime();

        return $this;
    }

    /**
     * Add userNotification
     *
     * @param \MainBundle\Entity\UserNotification $userNotification
     *
     * @return User
     */
    public function addUserNotification(\MainBundle\Entity\UserNotification $userNotification)
    {
        $this->userNotifications[] = $userNotification;

        return $this;
    }

    /**
     * Remove userNotification
     *
     * @param \MainBundle\Entity\UserNotification $userNotification
     */
    public function removeUserNotification(\MainBundle\Entity\UserNotification $userNotification)
    {
        $this->userNotifications->removeElement($userNotification);
    }

    /**
     * Get userNotifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

    /**
     * Set supporter
     *
     * @param boolean $supporter
     *
     * @return User
     */
    public function setSupporter($supporter)
    {
        $this->supporter = $supporter;

        return $this;
    }

    /**
     * Get supporter
     *
     * @return boolean
     */
    public function getSupporter()
    {
        return $this->supporter;
    }

    /**
     * Set supporterAims
     *
     * @param array $supporterAims
     *
     * @return User
     */
    public function setSupporterAims($supporterAims)
    {
        $this->supporterAims = $supporterAims;

        return $this;
    }

    /**
     * Get supporterAims
     *
     * @return array
     */
    public function getSupporterAims()
    {
        return $this->supporterAims;
    }

    public function addSupporterAim($supporterAim)
    {
        //$supporterAim = strtoupper($supporterAim);

        $this->supporterAims = (is_array($this->supporterAims)) ? $this->supporterAims : array();

        if (!in_array($supporterAim, $this->supporterAims, true))
        {
            $this->supporterAims[] = $supporterAim;
        }

        return $this;
    }

    public function removeSupporterAim($supporterAim)
    {
        //if (false !== $key = array_search(strtoupper($supporterAim), $this->supporterAims, true))
        if (false !== $key = array_search($supporterAim, $this->supporterAims, true))
        {
            unset($this->supporterAims[$key]);
            $this->supporterAims = array_values($this->supporterAims);
        }

        return $this;
    }



    /**
     * Add supporterAimss
     *
     * @param \MainBundle\Entity\SupporterAim $supporterAimss
     *
     * @return User
     */
    public function addSupporterAimss(\MainBundle\Entity\SupporterAim $supporterAimss)
    {
        $this->supporterAimss[] = $supporterAimss;

        return $this;
    }

    /**
     * Remove supporterAimss
     *
     * @param \MainBundle\Entity\SupporterAim $supporterAimss
     */
    public function removeSupporterAimss(\MainBundle\Entity\SupporterAim $supporterAimss)
    {
        $this->supporterAimss->removeElement($supporterAimss);
    }

    /**
     * Get supporterAimss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupporterAimss()
    {
        return $this->supporterAimss;
    }
}
