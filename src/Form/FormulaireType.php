<?php

namespace App\Form;

use App\Entity\Formulaire;
use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class FormulaireType extends AbstractType
{
    private function getDisabledDateForDaysOff()
    {
        
        $disabledDateCurrentYear = '';
        $disabledDateNextYear = '';
        $currentYear = date('Y');
        $nextYear = date('Y')+1;
        //$daysOff = ['01-01-', '17-04-', '01-05-', '08-05-', '25-05-', '05-06-', '14-07-', '15-08-', '01-11-', '11-11-'];
        $daysOff = ["/01/01", "/05/01", "/05/08", "/07/14", "/08/15", "/11/01", "/11/11", "/12/25"];
        //$daysOff = [2, 7, [2019,4,1], [2019,11,25], [2020,4,1], [2020,11,25] ]
        //$daysOff=[[1, 1, 2013],[1, 5, 2013],[8, 5, 2013],[14, 7, 2013],[15, 8, 2013],[1, 11, 2013],[11, 11, 2013],[25, 12, 2013]];
        $listDate="";
        foreach ($daysOff as $dayOff){
            //$disabledDateCurrentYear = $disabledDateCurrentYear.$dayOff.$currentYear.', ';
            //$disabledDateNextYear = $disabledDateNextYear.$dayOff.$nextYear.', ';
            //$disabledDateCurrentYear = $disabledDateCurrentYear.$currentYear.$dayOff.', '; //format yyy-mm-dd
            //$disabledDateNextYear = $disabledDateNextYear.$nextYear.$dayOff.', ';
            //$disabledDateCurrentYear = $disabledDateCurrentYear.$currentYear.$dayOff.', ';
            //$disabledDateNextYear = $disabledDateNextYear.$nextYear.$dayOff.', ';
            $listDate=$listDate.$currentYear.$dayOff.', ';
            $listDate=$listDate.$nextYear.$dayOff.', ';
        }
        //$disabledDate = $disabledDateCurrentYear.$currentYear.'-05-01'.', '.$disabledDateNextYear.$nextYear.'-05-01';
        return $listDate; //ne marche pas
        //return array("2019-05-01", "2019-12-25"); //erreur avec ' et avec "
        //return ["2019/05/01", "2019/12/25"]; //erreur avec ' et avec "
        //return "2019/05/01, 2019/12/25, 2019/12/23"; //OK 1er mai désactivé
        //return $daysOff;
        //return [["2019/12/25"], ["2019/8/5"]];
        //return ["05/01/2019"];
    }
    

   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        date_default_timezone_set('Europe/Paris');//sinon on récupère l'heure del'ordi moins un
        setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');
        /*$reservation=new Reservation();*/
        $builder
            ->add('title',EmailType::class, [
                'label' => 'Entrez votre adresse e-mail :'
            ])
            ->add('date_billet', DateType::class, array(
                'widget' => 'single_text', 
                'label'  => 'Date de visite :',
                'html5' => false,
                //'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'form-control  input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-mm-dd',
                    //'data-date-format'=>'MMMM Do YYYY, h:mm:ss a',
                    //'data-date-format'=>'dd/mm/yy',
                    'placeholder'=> "now"|date('d/m/Y'),
                    'data-date-language'=>'fr-FR',
                    'data-date-days-of-week-disabled' => '0,2',
                    'data-date-start-date' => "0d",
                    'data-date-end-date' => '+364d',
                    /*'dayNames'=> ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
                    'dayNamesShort'=> ["Dim", "Lun", "Mar", "Mer", "Jeu", "Vend", "Sam"],
                    'dayNamesMin'=> ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                    'monthNames'=> ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"],
                    'monthNamesShort'=> ["Jan", "Fev", "Mar", "Avr", "Mai", "Juin", "Jul", "Ao", "Sep", "Oct", "Nov", "Dec"],                       
                    'clear'=> "Clear",
                    'prevText'=> 'Précédent',
                    'nextText'=> 'Suivant',*/
                    'data-date-dates-disabled' => $this->getDisabledDateForDaysOff()
                ]
                
                ))
                //'class'=>'form-control input-inline datepicker',
                //'data-provide' => 'datepicker',
                //'data-date-days-of-week-disabled' => '2',
                //'data-date-format' => 'mm-dd-yyyy'
                //,'attr' => array('class'=>'input-lg')))
                //'format' => 'dd-MM-yyyy',
                

                /*'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd-mm-yyyy',
                    'data-date-days-of-week-disabled' => '02',
                    'data-date-language' => 'fr',
                    'data-date-start-date' => "0d",
                    'data-date-end-date' => '+364d',
                    'data-date-dates-disabled' => $this->getDisabledDateForDaysOff()
                ]
            ))*/
            ;


            if (localtime(time(),true)['tm_hour']>=14 && date("now")) {
                $builder    ->add('type_jour', ChoiceType::class,array('choices'  => array( 
                    //'choices' => [
                        //'Journée' => true,
                        //'Demi-journée' => false
                        'Journée' => 'Journée',
                        'Demi-journée' => 'Demi-journée'),
                        
                    
                    //],
                    
                    //'label'=>'Durée de la visite (trop tard pour journée complète) :',
                    //'expanded' => true,
                    //'multiple' => false
                    ));
            } else {
                $builder->add('type_jour', ChoiceType::class,array('choices'  => array( 
                //'choices' => [
                    //'Journée' => true,
                    //'Demi-journée' => false
                    'Journée' => 'Journée',
                    'Demi-journée' => 'Demi-journée'),
                    
                
                //],
                'label'=>'Durée de la visite :',
                'expanded' => true,
                'multiple' => false,
                ));
            }

                $builder ->add('nombre_total_ticket', IntegerType::class, array(
                'label' => 'Nombre Total de Billet :',
                'attr'=>array('min'=>0, 'max'=>1000)
                
            ))
            ->add('btn_ajout_panier', SubmitType::class, [
                'label'=>'Ajouter au panier'
            ])
            /*->add('nombre_tarif_normal', IntegerType::class, array(
                'label' => 'Tarif Normal',
            ))
            ->add('nombre_tarif_reduit', IntegerType::class, array(
                'label' => 'Tarif Réduit',
            ))
            ->add('nombre_tarif_enfant', IntegerType::class, array(
                'label' => 'Tarif Enfant',
            ))
            ->add('nombre_tarif_senior', IntegerType::class, array(
                'label' => 'Tarif Senior',
            ))*/
        ;

       /* $formModifier=function ( $form, $bb){
            try{
            $bb->remove('type_jour');
            } catch(Exception $e) {

            }
        };*/

       /* $builder->get('date_billet')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) 
            {   $data=$event->getData();
                $form=$event->getForm()->getParent();
                //$formModifier($form,$builder);//, $data->getType());
                echo("test Karim".date("now"));
            }
        );*/
        
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
