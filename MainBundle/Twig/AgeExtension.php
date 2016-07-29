<?php

namespace MainBundle\Twig;

class AgeExtension extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			new \Twig_SimpleFilter('age', array($this, 'getAge')),
		);
	}

	public function getAge($birthday)
	{
		if (is_null($birthday))
		{
			return '';
		}

		if (!$birthday instanceof \DateTime)
		{
			$birthday = new \DateTime(date($birthday));
		}

		$referenceDateTimeObject = new \DateTime();
		$diff = $referenceDateTimeObject->diff($birthday);
		$response = ($diff->y > 0) ? $diff->y . ' years old' : '';

		return $response;
	}

	public function getName()
	{
		return 'age_extension';
	}
}
