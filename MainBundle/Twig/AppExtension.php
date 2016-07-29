<?php

namespace Creative\MainBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('private', array($this, 'privateFilter')),
        );
    }

    public function privateFilter($path)
    {
        $private = '$'.$path;

        return $private;
    }

    public function getName()
    {
        return 'app_extension';
    }
}
