<?php

namespace App\Form;

use App\Entity\InfoClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\InfoClientType;

class InfoClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
      
        ->add('nom', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre nom"
            ]
           ])
        ->add('prenom', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre prénom"
            ]
           ])
        ->add('pays', TextType::class, [
            'attr'=>[
            'placeholder'=>"Votre pays de résidence"
            ]
           ])
        ->add('date_naissance', DateType::class, array(
            'format' => 'dd/MM/yyyy',
            'years' => range(1920, date('Y'))
            
            
        ))
        
        /*->add('acces_reduit', ChoiceType::class, array(
            'choices' => array(
                'Positive choice' => true,
                'Negative choice' => false,
            ),
            'choices_as_values' => true,
            'choice_value' => function ($choiceKey) {
                if (null === $choiceKey) {
                    return null;
                }
        
                // cast to string after testing for null,
                // as both null and false cast to an empty string
                $stringChoiceKey = (string) $choiceKey;
        
                // true casts to '1'
                if ('1' === $stringChoiceKey) {
                    return 'true';
                }
        
                // false casts to an empty string
                if ('' === $stringChoiceKey) {
                    return 'false';
                }
        
                
            },
            'expanded' => true,
            'multiple' => false,
        ));*/
           ->add('acces_reduit', ChoiceType::class, array( 
            'choices' => array(
                'Oui'=> 'Oui' , 
                'Non' => 'Non', 
                
            ),
            'expanded' => true,
            'multiple' => false,
           //])
          /* ->getForm();
        return $this->render('blog/accueil.html.twig', [
            'forminfo_client'=>$form->createView()
            ]); */   
        ));  
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InfoClient::class,
            //'choices' => $reduit,
            'choice_label' => function ($value) {
            return $value;}
        ]);
    }
}
