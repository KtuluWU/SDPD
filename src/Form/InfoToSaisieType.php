<?php

namespace App\Form;

use App\Entity\IFG_SDPD\InfoToSaisie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class InfoToSaisieType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siren', null, array('label' => 'InfoToSaisie.siren','translation_domain' => 'IFG'))
            ->add('numdepot', null, array('label' => 'InfoToSaisie.numdepot','translation_domain' => 'IFG'))
            ->add('datedepot', DateType::class, array(
                'label' => 'InfoToSaisie.datedepot',
                'widget' => 'single_text',
                'attr' => ['min' => '1940-01-01', 'max' => '2200-12-31'],
                'translation_domain' => 'IFG'
                ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => InfoToSaisie::class,
        ));
    }
}