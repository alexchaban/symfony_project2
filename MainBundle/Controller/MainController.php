<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use CoreBundle\Controller\CoreController;
use MainBundle\Entity\Post;
use MainBundle\Entity\PostImage;
use MainBundle\Entity\PostComment;
use MainBundle\Entity\Vision;
use MainBundle\Entity\UserNotification;
use MainBundle\Entity\UserFavorite;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MainController extends CoreController
{
	public function startpageAction()
	{
		$view_vars = array();

		return $this->render('MainBundle:Main:startpage.html.twig', $view_vars);
	}

	public function viewUserPostAction($username, $postId)
	{
		$params = array(
			'username' => $username,
			'aim' => 'home',
			'type' => 'all',
			'postId' => $postId,
		);
		return $this->redirectToRoute('main_dashboard_index', $params, 301);
	}

	public function dashboardAction(Request $request, $username = null, $aim = 'home', $type = 'all', $postId = null)
	{
		if (!is_null($username))
		{
			$u_rp = $this->getDoctrine()->getManager()->getRepository('MainBundle:User');
			$user = $u_rp->findOneBy(array('username' => $username));
		}
		else
		{
			$user = $this->_user;
		}

		if (!$user)
		{
			throw new HttpException(404, "404 Error. User not found.");
		}

		if ($aim == 'home' && $type == 'all')
		{
			$posts = $this->getDashboardPosts($user, $aim);
		}
		else
		{
			$posts['notes'] = $this->getDashboardPosts($user, $aim, 'note');
			$posts['visions'] = $this->getDashboardPosts($user, $aim, 'vision');
			$posts['questions'] = $this->getDashboardPosts($user, $aim, 'question');
		}

		if (!is_null($postId))
		{
			$em = $this->getDoctrine()->getManager();
			$post_rp = $em->getRepository('MainBundle:Post');

			$post = $post_rp->getPostData($postId);
			$post['url'] = $this->generateUrl('main_user_post', array('username' => $post['username'], 'postId' => $post['id']));
			$post['user_pic'] = $this->generateUrl('main_user_profile_pic', array('username' => $post['username']));
			$post['createdAgo'] = $this->timeElapsedString($post['created_on']);
			//$post['favorite_url'] = $this->generateUrl('main_favorite_post', array('postId' => $post['id']));
			if ($post['vision_timer'])
			{
				$post['vision_timer_remaining'] = $this->timeElapsedString($post['vision_timer'], true);
			}
			if ($post['image'])
			{
				$post['image'] = $this->generateUrl('main_dashboard_view_image', array('path' => $post['image']));
			}

			$view_vars['selected_post'] = $post;
		}

		$view_vars['posts'] = $posts;
		$view_vars['online'] = $this->whoIsOnlineAction();
		$view_vars = array_merge($this->_view_vars, $view_vars);

		return $this->render('MainBundle:Main:dashboard.html.twig', $view_vars);
	}

	public function getDashboardPosts($user, $aim = 'home', $type = 'all', $start = 0, $lenght = 5, $filters = array())
	{
		$params = array('aim' => $aim, 'type' => $type, 'start' => $start, 'lenght' => $lenght);
		$params = array_merge($params, $filters);
		
		$em = $this->getDoctrine()->getManager();
		$post_rp = $em->getRepository('MainBundle:Post');
		$posts = $post_rp->retrieveDashboardPosts($user, $this->_user, $params);

		if (!empty($posts))
		{
			foreach ($posts as &$post)
			{
				$post['url'] = $this->generateUrl('main_user_post', array('username' => $post['username'], 'postId' => $post['id']));
				$post['user_pic'] = $this->generateUrl('main_user_profile_pic', array('username' => $post['username']));
				$post['createdAgo'] = $this->timeElapsedString($post['created_on']);
				//$post['favorite_url'] = $this->generateUrl('main_favorite_post', array('postId' => $post['id']));
				if ($post['vision_timer'])
				{
					$post['vision_timer_remaining'] = $this->timeElapsedString($post['vision_timer'], true);
				}
				if ($post['vision_person_id'])
				{
					$post['vision_person_url'] = $this->generateUrl('main_dashboard_index', array('username' => $post['vision_person_username']));
				}
				if ($post['image'])
				{
					$post['image'] = $this->generateUrl('main_dashboard_view_image', array('path' => $post['image']));
				}
			}
		}

		return $posts;
	}

	public function getUserNotificationsAction($lastId = null, $limit = 5)
	{
		$un_rp = $this->getDoctrine()->getManager()->getRepository('MainBundle:UserNotification');
		$notifications = $un_rp->getUserNotifications($this->_user, $lastId, $limit);

		if ($notifications)
		{
		    foreach ($notifications as &$row)
		    {
		        $row['user_pic'] = $this->generateUrl('main_user_profile_pic', array('username' => $row['username']));
		        if ($row['created_on'])
		        {
		            $row['created_on_d'] = $this->timeElapsedString($row['created_on']);
		        }
		        if ($row['vision_timer'])
		        {
		            $row['vision_timer_d'] = $this->timeElapsedString($row['vision_timer'], true);
		        }
		    }
		}

		return new Response(json_encode($notifications), 200, array('Content-Type' => 'application/json')); 
	}

	public function deleteUserNotificationAction(Request $request, $notificationId)
	{
		if ($request->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$notification = $em->getRepository('MainBundle:UserNotification')->findOneById($notificationId);

			if ($notification)
			{
				$em->remove($notification);
				$em->flush();

				$response = array('status' => 'success', 'msg' => 'Succesfully removed.');
			}
			else
			{
				$response = array('status' => 'error', 'msg' => 'No such notification');
			}

			return new Response(json_encode($response), 200, array('Content-Type' => 'application/json')); 
		}
		else
		{
			throw new HttpException(406, "You can't access this page directly.");
		}
	}

	public function getUserPostCommentsAction(Request $request, $postId)
	{
		if ($request->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getManager();
			$post = $em->getRepository('MainBundle:Post')->findOneById($postId);

			if ($post)
			{
				$comments = $em->getRepository('MainBundle:PostComment')->findBy(array('post' => $post));
				$response = array('comments_count' => count($comments));

				if (count($comments) > 0)
				{
					foreach ($comments as $comment)
					{
						$user = $comment->getUser();
						$response['comments'][] = array(
							'id' => $comment->getId(),
							'fullname' => $user->getFirstName() . ' ' . $user->getLastName(),
							'profile_pic' => $this->generateUrl('main_user_profile_pic', array('username' => $user->getUsername())),
							'comment' => $comment->getContent(),
						);
					}
				}
			}
			else
			{
				$response = array('comments_count' => '0');
			}

			return new Response(json_encode($response), 200, array('Content-Type' => 'application/json')); 
		}
		else
		{
			throw new HttpException(406, "You can't access this page directly.");
		}
	}

	public function saveUserPostCommentAction(Request $request, $postId)
	{
		if ($request->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getEntityManager();
			$post = $em->getRepository('MainBundle:Post')->findOneById($postId);

			if ($post)
			{
				$content = $request->request->get('content');

				if ($content)
				{
					$comment = new PostComment();
					$comment->setPost($post);
					$comment->setContent($content);
					$comment->setUser($this->_user);

					$response = array('status' => 'success', 'msg' => 'Succesfully saved.');

					try {
						$em->persist($comment);
						$em->flush();

						$response['comment'] = array(
							'id' => $comment->getId(),
							'fullname' => $this->_user->getFirstName() . ' ' . $this->_user->getLastName(),
							'profile_pic' => $this->generateUrl('main_user_profile_pic', array('username' => $this->_user->getUsername())),
							'comment' => $comment->getContent(),

						);
					} catch (\Exception $e) {
						error_log($e->getMessage());
						$response = array('status' => 'error', 'msg' => 'Oops. Something went wrong.');
					}
				}
				else
				{
					$response = array('status' => 'error', 'msg' => 'Comment field is mandatory.');
				}
			}
			else
			{
				$response = array('status' => 'error', 'msg' => 'No such post');
			}

			return new Response(json_encode($response), 200, array('Content-Type' => 'application/json')); 
		}
		else
		{
			throw new HttpException(406, "You can't access this page directly.");
		}
	}

	public function userProfileAction(Request $request)
	{
		$view_vars = array();
		$view_vars = array_merge($this->_view_vars, $view_vars);

		return $this->render('MainBundle:Main:profile.html.twig', $view_vars);
	}

	public function ajaxLoadMorePostsAction(Request $request, $aim = 'home', $type = 'all')
	{
		$lastId = $request->get('lastId');
		$filter = $request->get('dash');
		$posts = $this->getDashboardPosts($this->_user, $aim, $type, $lastId, 5,$filter);

		return new Response(json_encode($posts), 200, array('Content-Type' => 'application/json')); 
	}

	public function ajaxUserFriendsJsonAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$rp = $em->getRepository('MainBundle:User');

		$phrase = $request->get('phrase');
		$user_friends = $rp->getUserFriends($this->_user, $phrase);

		return new Response(json_encode($user_friends), 200, array('Content-Type' => 'application/json'));
	}

	public function ajaxPostAction(Request $request)
	{
		if ($request->isXmlHttpRequest())
		{
			$data = $request->request->get('form');

			$em = $this->getDoctrine()->getEntityManager();
			$user_rp = $em->getRepository('MainBundle:User');

			// TODO: validation

			$post = new Post();
			$post->setTitle($data['title']);
			$post->setContent($data['content']);

			$aim_rp = $em->getRepository('MainBundle:Aim');
			$aim = $aim_rp->findOneBy(array('slug' => $data['aim']));

			$post->setAim($aim);
			$post->setPublic((isset($data['public']) && $data['public'] != 'false') ? 'true' : 'false');
			$post->setUser($this->_user);

			switch ($data['type']) {
				case 'vision':
					$vision = new Vision();
					$vision->setPost($post);
					if (isset($data['vision_timer']) && !empty($data['vision_timer']))
					{
						$date = \DateTime::createFromFormat("d.m.Y", $data['vision_timer']);
						$vision->setVisionTimer($date);
					}
					if (isset($data['vision_person_id']) && !empty($data['vision_person_id']))
					{
						$user = $user_rp->findOneById($data['vision_person_id']);

						if ($user)
						{
							$this->addUserNotification($user, $post);
						}
					}
					else
					{
						$user = $this->_user;
					}

					$this->addSupporterNotification($aim, $post);

					$user = ($user) ? $user : $this->_user;
					$vision->setVisionPerson($user);
					$post->setType('vision');
					$em->persist($vision);
					break;

				case 'reply':
					//$aim = $aim_rp->findOneBy(array('slug' => $data['aim']));
					//$post->setAim($aim);

					$vision = new Vision();
					$vision->setPost($post);

					$user = $user_rp->findOneById($data['userId']);
					$this->addUserNotification($user, $post);
					$this->addSupporterNotification($aim, $post);
					$vision->setVisionPerson($user);

					$post->setType('vision');
					$em->persist($vision);

					break;
				
				case 'group':
					$post->setType('group');
					break;

				case 'question':
					$post->setType('question');
					break;

				case 'note':
				default:
					$post->setType('note');
					break;
			}

			// Upload post image file
			$file = $request->files->get('file');

			if (!is_null($file)) {
				if ($file instanceof UploadedFile && $file->getError() == '0')
				{
					$originalName = $file->getClientOriginalName();
					$originalExtension = $file->getClientOriginalExtension();
					$validFileTypes = array('jpeg', 'jpg', 'png');

					if (in_array(strtolower($originalExtension), $validFileTypes))
					{
						$image = new PostImage();
						$image->setFile($file);
						$image->setUser($this->_user);
						$image->setPost($post);
						
						$em->persist($image);
					}
					else
					{
						$errors[] = array('status' => 'error', 'msg' => 'Invalid File Type');
					}
				}
				else
				{
					$errors[] = array('status' => 'error', 'msg' => 'Invalid File Format');
				}

				if (!empty($errors))
				{
					return new Response(json_encode($errors), 200, array('Content-Type' => 'application/json'));
				}
			}

			$em->persist($post);
			$em->flush();

			$message = array('status' => 'success', 'msg' => 'Succesfully posted.');

			if ($post)
			{
				//$em = $this->getDoctrine()->getManager();
				$post_rp = $em->getRepository('MainBundle:Post');
				$post = $post_rp->getPostData($post->getId());

				$post['url'] = $this->generateUrl('main_user_post', array('username' => $post['username'], 'postId' => $post['id']));
				$post['user_pic'] = $this->generateUrl('main_user_profile_pic', array('username' => $post['username']));
				$post['createdAgo'] = $this->timeElapsedString($post['created_on']);
				if ($post['vision_timer'])
				{
					$post['vision_timer_remaining'] = $this->timeElapsedString($post['vision_timer'], true);
				}
				if ($post['image'])
				{
					$post['image'] = $this->generateUrl('main_dashboard_view_image', array('path' => $post['image']));
				}

				$message['post'] = $post;
			}

			return new Response(json_encode($message), 200, array('Content-Type' => 'application/json'));
		}
		else
		{
			throw new HttpException(406, "You can't access this page directly.");
		}
	}

	public function ajaxPostFavoriteAction(Request $request, $postId)
	{
		if ($request->isXmlHttpRequest())
		{
			$em = $this->getDoctrine()->getEntityManager();
			$post = $em->getRepository('MainBundle:Post')->findOneById($postId);
			$userFavorite = $em->getRepository('MainBundle:UserFavorite')->findOneBy(array('post' => $post, 'user' => $this->_user));

			if ($userFavorite)
			{
				$em->remove($userFavorite);
				$em->flush();

				$response = array('status' => 'success', 'msg' => 'Successfully removed from favorites', 'removed' => true);
			}
			else
			{
				if ($post)
				{
					$userFavorite = new UserFavorite();
					$userFavorite->setPost($post);
					$userFavorite->setUser($this->_user);

					$em->persist($userFavorite);
					$em->flush();

					$response = array('status' => 'success', 'msg' => 'Successfully added to favorites', 'removed' => false);
				}
				else
				{
					$response = array('status' => 'error', 'msg' => 'No such post');
				}
			}

			return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
		}
		else
		{
			throw new HttpException(406, "You can't access this page directly.");
		}
	}

	public function viewUserProfilePicAction(Request $request, $username = 'me')
	{
		if ($username == 'me')
		{
			$user = $this->_user;
		}
		else
		{
			$em = $this->getDoctrine()->getEntityManager();
			$user_rp = $em->getRepository('MainBundle:User');
			$user = $user_rp->findOneBy(array('username' => $username));
		}

		if ($user)
		{
			$filename = $user->getAbsolutePath();

			if (!$filename)
			{
				$filename = $this->getDefaultProfilePicPath();
			}

			$response = new Response();
			$response->headers->set('Cache-Control', 'private');
			$response->headers->set('Content-type', mime_content_type($filename));
			$response->headers->set('Content-Disposition', 'inline; filename="' . basename($filename) . '";');
			$response->headers->set('Content-length', filesize($filename));
			$response->sendHeaders();
			$response->setContent(file_get_contents($filename));

			return $response;
		}
		else
		{
			throw new HttpException(404, "User not found");
		}
	}

	public function uploadUserProfilePicAction(Request $request)
	{
		//$user = $this->_user;

		$file = $request->files->get('file');

		//var_dump($request, $file);die;

		if ($file instanceof UploadedFile && $file->getError() == '0')
		{
			if ($file->getSize() < 150000000)
			{
				$originalName = $file->getClientOriginalName();
				$originalExtension = $file->getClientOriginalExtension();
				$validFileTypes = array('jpeg', 'jpg', 'png', 'tiff');

				if (in_array(strtolower($originalExtension), $validFileTypes))
				{
					$this->_user->setFile($file);

					$em = $this->getDoctrine()->getManager();
					$em->persist($this->_user);
					$em->flush();

					$response = array('status' => 'success', 'msg' => 'ok');
				}
				else
				{
					$response = array('status' => 'error', 'msg' => 'file extension error');
				}
			}
			else
			{
				$response = array('status' => 'error', 'msg' => 'filesize');
			}
		}
		else
		{
			$response = array('status' => 'error', 'msg' => 'instanceof UploadedFile != file');
		}

		return new Response(json_encode($response), 200, array('Content-Type' => 'application/json'));
	}

	public function registerAction(Request $request) {
		$response = $this->forward('FOSUserBundle:Registration:register');

		return $response;
	}

	public function viewImageWebPathAction(Request $request, $path)
	{
		$repository = $this->getDoctrine()->getRepository('MainBundle:PostImage');
		$image = $repository->findOneBy(array('path' => $path));

		if ($image)
		{
			$filename = $image->getAbsolutePath();

			$response = new Response();
			$response->headers->set('Cache-Control', 'private');
			$response->headers->set('Content-type', mime_content_type($filename));
			$response->headers->set('Content-Disposition', 'inline; filename="' . basename($filename) . '";');
			$response->headers->set('Content-length', filesize($filename));
			$response->sendHeaders();
			$response->setContent(file_get_contents($filename));

			return $response;
		}
		else
		{
			throw new HttpException(404, "Image not found");
			
		}
	}

	public function whoIsOnlineAction()
	{
		$response = array();
		$users = $this->getDoctrine()->getManager()->getRepository('MainBundle:User')->getActive($this->_user);

		foreach ($users as $user)
		{
			$response[] = array(
				'id' => $user['id'],
				'fullname' => $user['firstname'] . ' ' . $user['lastname'],
				'profile_pic' => $this->generateUrl('main_user_profile_pic', array('username' => $user['username']))
			);
		}

		return array('users' => $response);
	}

	function getDefaultProfilePicPath()
	{
		$defaultProfilePicName = 'default-profile-pic.png';

		return __DIR__.'/../../../private/' . $defaultProfilePicName;
	}

	function addUserNotification(\MainBundle\Entity\User $user, \MainBundle\Entity\Post $post)
	{
		$em = $this->getDoctrine()->getManager();

		$un = new UserNotification();
		$un->setUser($user);
		$un->setPost($post);

		$em->persist($un);
		$em->flush();

		return true;
	}

	function addSupporterNotification(\MainBundle\Entity\Aim $aim, \MainBundle\Entity\Post $post)
	{
		$em = $this->getDoctrine()->getManager();
		$supporterAims = $em->getRepository('MainBundle:SupporterAim')->getUsersByAim($aim);

		foreach ($supporterAims as $supporterAim)
		{
			$this->addUserNotification($supporterAim->getUser(), $post);
		}

		return true;
	}

	function timeElapsedString($datetime, $remains = false, $full = false)
	{
		$now = new \DateTime;
		$ago = $datetime;
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v)
		{
			if ($diff->$k)
			{
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			}
			else
			{
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);

		if ($remains)
		{
			if ($now > $datetime)
			{
				return 'Expired';
			}
			else
			{
				return $string ? 'Remains ' . implode(', ', $string) : 'just now';
			}
		}
		else
		{
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}
	}

	public function confirmedAction()
	{
		return $this->redirectToRoute('main_dashboard', array(), 301);
	}

}
