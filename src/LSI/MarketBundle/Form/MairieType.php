<?php

namespace LSI\MarketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MairieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, array('required' => false))
            ->add('service', TextareaType::class, array('required' => false))
            ->add('longitude', TextType::class, array('required' => false))
            ->add('latitude', TextType::class, array('required' => false))
            //->add('addresse', AdresseType::class, array('required' => false))
           // ->add('epi', TextType::class, array('required' => false))
        ;

        $builder
            ->remove('epci')
            ->remove('longitude')
            ->remove('latitude')
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LSI\MarketBundle\Entity\Mairie'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'lsi_marketbundle_mairie';
    }


}
