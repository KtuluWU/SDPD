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
            ->add('siren', null, array('translation_domain' => 'IFG'))
            ->add('numdepot', null, array('translation_domain' => 'IFG'))
            ->add('datedepot', DateType::class, array(
                'widget' => 'single_text',
                'years'  =>  range(1940,2200),
                'attr' => ['class' => 'js-datepicker'],
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