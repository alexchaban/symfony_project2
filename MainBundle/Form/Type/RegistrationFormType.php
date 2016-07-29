<?php

namespace MainBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationFormType extends BaseType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$builder->add('firstname', null, array('label' => 'form.firstname', 'translation_domain' => 'FOSUserBundle'))
				->add('lastname', null, array('label' => 'form.lastname', 'translation_domain' => 'FOSUserBundle'))
                ->add('supporterAims', 'entity', array(
                    'class' => 'MainBundle\Entity\Aim',
                    'choice_label' => 'slug',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('a')
                            ->orderBy('a.slug', 'ASC');
                    },
                    'label' => 'Aims',
                ))
				->remove('username');

			/*
			->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
			->add('plainPassword', 'repeated', array(
					'type' => 'password',
					'options' => array('translation_domain' => 'FOSUserBundle'),
					'first_options' => array('label' => 'form.password'),
					'second_options' => array('label' => 'form.password_confirmation'),
					'invalid_message' => 'fos_user.password.mismatch',
				)
			);
			*/
	}

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => "MainBundle\Entity\User"
		));
	}

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

	public function getName()
	{
		return $this->getBlockPrefix();
	}
}
