<?php

namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="MainBundle\Entity\Repository\PostRepository")
 * @ORM\Table(name="posts")
 */
class Post
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
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    protected $content;

    /**
     * @var enum
     *
     * @ORM\Column(name="type", type="string", columnDefinition="enum('note', 'vision', 'question', 'article', 'group', 'reply')", nullable=false, options={"default": "note"})
     */
    protected $type = 'note';

    /**
     * @var enum
     *
     * @ORM\Column(name="public", type="string", columnDefinition="enum('true', 'false')", nullable=false, options={"default": "false"})
     */
    protected $public = 'false';

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Aim", inversedBy="posts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="aim_id", referencedColumnName="id")
     */
    protected $aim;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="posts", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;

    /**
     * @var date
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    protected $createdOn;

    /**
     * @var date
     *
     * @ORM\Column(name="modified_on", type="datetime", nullable=true)
     */
    protected $modifiedOn;

    /**
     * @ORM\OneToMany(targetEntity="PostComment", mappedBy="post")
     */
    protected $postComments;

    /**
     * @ORM\OneToOne(targetEntity="PostImage", mappedBy="post")
     */
    protected $postImage;

    /**
     * @ORM\OneToMany(targetEntity="UserNotification", mappedBy="post", cascade={"persist", "remove"})
     */
    protected $userNotifications;



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
     * Set title
     *
     * @param string $title
     *
     * @return Post
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
     * Set content
     *
     * @param string $content
     *
     * @return Post
     */
    public function setContent($content)
    {
		$pattern = '~(?#!js YouTubeId Rev:20160125_1800)
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://          # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)?  # Optional subdomain.
        (?:                # Group host alternatives.
          youtu\.be/       # Either youtu.be,
        | youtube          # or youtube.com or
          (?:-nocookie)?   # youtube-nocookie.com
          \.com            # followed by
          \S*?             # Allow anything up to VIDEO_ID,
          [^\w\s-]         # but char before ID is non-ID char.
        )                  # End host alternatives.
        ([\w-]{11})        # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
        (?!                # Assert URL is not pre-linked.
          [?=&+%\w.-]*     # Allow URL (query) remainder.
          (?:              # Group pre-linked alternatives.
            [\'"][^<>]*>   # Either inside a start tag,
          | </a>           # or inside <a> element text contents.
          )                # End recognized pre-linked alts.
        )                  # End negative lookahead assertion.
        [?=&+%\w.-]*       # Consume any URL (query) remainder.
        ~ix';
		$content = preg_replace($pattern, '<br /><iframe width="480" height="270" src="https://www.youtube.com/embed/$1?feature=oembed" frameborder="0" allowfullscreen></iframe><br />', $content);
		
		$pattern = '~(?)http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?~ix';
		$content = preg_replace($pattern, '<br /><iframe width="400" height="224" src="https://www.facebook.com/video/embed?video_id=$2" frameborder="0" allowfullscreen></iframe><br />', $content);
		
		$pattern = '~
			# Match Vimeo link and embed code
			(?:&lt;iframe [^&gt;]*src=")?		# If iframe match up to first quote of src
			(?:							# Group vimeo url
				https?:\/\/				# Either http or https
				(?:[\w]+\.)*			# Optional subdomains
				vimeo\.com				# Match vimeo.com
				(?:[\/\w]*\/videos?)?	# Optional video sub directory this handles groups links also
				\/						# Slash before Id
				([0-9]+)				# $1: VIDEO_ID is numeric
				[^\s]*					# Not a space
			)							# End group
			"?							# Match end quote if part of src
			(?:[^&gt;]*&gt;&lt;/iframe&gt;)?		# Match the end of the iframe
			(?:&lt;p&gt;.*&lt;/p&gt;)?		        # Match any title information stuff
			~ix';
			
		$content = preg_replace($pattern, '<br /><iframe src="//player.vimeo.com/video/$1" width="480" height="270" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe><br />', $content);
		
		$this->content = $content;

        return $this;
    }
	
	function fbvideoid($url) {
		// delete space in url
		$url =  str_replace(' ', '', $url);
		// parse url
		$pars = parse_url($url);
		$path = $pars['path'];

		// delete end slashe
		if($path[strlen($path) - 1] == '/'):
			$path = rtrim($path, '/');
		endif;

		$count = count(explode("/", $path));
		// type url
		$urltype = "";
		if ($pars['path'] == "/photo.php" || $pars['path'] == "/video.php" || $pars['path'] == "/"):
			$urltype = 2;
		elseif($count == 4):
		  $urltype = 3;
		elseif($count == 5):
			$urltype = 1;
		endif;

	   // get id
		if ($urltype == 1) {

			$ex = explode("/", $path);
			$videoid = $ex[4];

		} else if ($urltype == 2) {

			parse_str($pars['query'], $e);
			$videoid = $e['v'];

		} else if ($urltype == 3) {

			$ex = explode("/", $path);
			$videoid = $ex[3];

		} else {
			$videoid = null;
		}

	   return $videoid;
	}
	 
    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set public
     *
     * @param string $public
     *
     * @return Post
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Post
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
     * Set modifiedOn
     *
     * @param \DateTime $modifiedOn
     *
     * @return Post
     */
    public function setModifiedOn($modifiedOn)
    {
        $this->modifiedOn = $modifiedOn;

        return $this;
    }

    /**
     * Get modifiedOn
     *
     * @return \DateTime
     */
    public function getModifiedOn()
    {
        return $this->modifiedOn;
    }

    /**
     * Set user
     *
     * @param \MainBundle\Entity\User $user
     *
     * @return Post
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
     * Set group
     *
     * @param \MainBundle\Entity\Group $group
     *
     * @return Post
     */
    public function setGroup(\MainBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \MainBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add postComment
     *
     * @param \MainBundle\Entity\PostComment $postComment
     *
     * @return Post
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
     * @return Post
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
     * Set postImage
     *
     * @param \MainBundle\Entity\PostImage $postImage
     *
     * @return Post
     */
    public function setPostImage(\MainBundle\Entity\PostImage $postImage = null)
    {
        $this->postImage = $postImage;

        return $this;
    }

    /**
     * Get postImage
     *
     * @return \MainBundle\Entity\PostImage
     */
    public function getPostImage()
    {
        return $this->postImage;
    }

    /**
     * Add userNotification
     *
     * @param \MainBundle\Entity\UserNotification $userNotification
     *
     * @return Post
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
}
