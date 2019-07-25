<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reservation;
use App\Entity\GroupeClients;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\InfoClient;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\FormulaireType;
use App\Form\InfoClientType;
use App\Form\GroupeClientsType;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Service\PriceCalculator\PriceCalculator;
//use Symfony\Component\Mailer\MailerInterface;
//use Symfony\Component\Mime\Email;
use App\Service\MailValidator\MailValidator;


class LouvreController extends AbstractController
{


    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $priceCalculator = new PriceCalculator();
        $tarifs=$priceCalculator->getTarifs();
        return $this->render('louvre/home.html.twig', [
                'tarifs' => $tarifs
            ]);
    }
    

    /**
     * @Route("/choixdate", name="choixdate")
     */
     public function Billeterie(Request $request)
    {
        $session=$request->getSession();
       
        $reservation=new Reservation();
        $form=$this->createForm(FormulaireType::class, $reservation);

        $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){

            $today = strtotime(date('Y-m-d'));
            $dateSaisie = strtotime($reservation->getDateBillet()->format('Y-m-d'));
            $interval = $dateSaisie - $today;
            if($interval == 0) {//aujourd'hui, tester 14h00
                $today14hSecondes=strtotime(date('Y-m-d').' 14:00');
                $maintenant=strtotime(date('Y-m-d H:i:s'));
                $interval = $today14hSecondes - $maintenant;
            }

            //1000 tickets par jour
            $nbBillet = $reservation->getNombreTotalTicket();
            $dateBillet = $reservation->getDateBillet();
            $repo = $this->getDoctrine()->getRepository(Reservation::class);
            $nbBilletDate=$repo->nbBilletDate($dateBillet);
            if($nbBilletDate >= 1000) {
			    $this->addFlash("notice","Il n'y a plus de billet disponible pour cette date. Choisissez une autre date.");
                return $this->render('louvre/choixdate.html.twig', [
                'controller_name' => 'LouvreController',
                'formFormulaire'=>$form->createView(),
               
            ]);
            } else if($nbBilletDate + $nbBillet > 1000) {
			    $this->addFlash("notice","Il ne reste plus que ".(1000-$nbBilletDate)." billets disponibles pour cette date.");
                return $this->render('louvre/choixdate.html.twig', [
                'controller_name' => 'LouvreController',
                'formFormulaire'=>$form->createView(),
                
            ]);
            } 



            $session->set('type_jour', $reservation->getTypeJour());
            if( $interval > 0) {// $interval >0 : à partir de demain ou aujourd'hui avant 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet());
                $session->set('title', $reservation->getTitle());
                
            } else {// $interval=0 : aujourd'hui après 14h
                $session->set('nombre_total_ticket', $reservation->getNombreTotalTicket());
                $session->set('date_billet', $reservation->getDateBillet());
                $session->set('title', $reservation->getTitle()); 
                //si on a choisi journée complète, message et ne pas changer de page
                if($reservation->getTypeJour() == 'Journée') {
    			    $this->addFlash("notice","Il est trop tard pour le billet Journée.");
                    return $this->render('louvre/choixdate.html.twig', [
                    'controller_name' => 'LouvreController',
                    'formFormulaire'=>$form->createView(),
                   
                ]);
                }
                
            }
            
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('identifications');
        }
        
        return $this->render('louvre/choixdate.html.twig', [
            'controller_name' => 'LouvreController',
            'formFormulaire'=>$form->createView(),
            
        ]);    

        
        
    }

    

    public function getNombreTotalTicket(Reservation $reservation, InfoClient $infoClient)
    {
        $nombreTarifNormal=$reservation->getNombreTarifNormal();
        $nombreTarifReduit=$reservation->getNombreTarifReduit();
        $nombreTarifEnfant=$reservation->getNombreTarifEnfant();
        $nombreTarifSenior=$reservation->getNombreTarifSenior();
        $nombreTotalTicket=$nombreTarifNormal+$nombreTarifReduit+$nombreTarifEnfant+$nombreTarifSenior;
        return $nombreTotalTicket;
    }
    

    public function isClicked(){
        if ( $form -> isSubmitted () && $form -> isValid ()) {
        
            $infoClient = $form -> get ( 'btn_ajout_panier' ) -> isClicked ()
            ? 'task_new'
            : 'task_success' ;
    
            return $this -> redirectToRoute ( $infoClient );
        }
    }


    /**
     * @Route("/identifications", name="identifications")
     */
    public function Detail_Client(Request $request)
    {
        $session=$request->getSession();
        $typeJour=$session->get('type_jour');
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        $title=$session->get('title');
        

        $groupeClients = new GroupeClients();
        for($i=1;$i<=$nombreBillet;$i++){
            $infoClient=new InfoClient();
            $groupeClients->getListeInfoClient()-> add($infoClient);
        }

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);
        

        $form->handleRequest($request);
        
        if($form->isSubmitted()&& $form->isValid()){
            $priceTotal=0;
            $priceCalculator = new PriceCalculator();
            for($i=0;$i<$nombreBillet;$i++){
                $infoClient= $groupeClients->getListeInfoClient()[$i];
                $dateNaissance=$infoClient->getDateNaissance();
                $anneeNaissance=$dateNaissance->format('Y');
                $moisNaissance=$dateNaissance->format('m');
                $jourNaissance=$dateNaissance->format('d');
                $reduit=$infoClient->getAccesReduit();
                $prixClient=$priceCalculator->getTarifClient($anneeNaissance, $moisNaissance, $jourNaissance, $reduit, $typeJour);
                echo $prixClient;
                $priceTotal+=$prixClient;
                
            }
            echo $priceTotal;
            

            $session->set('groupe_client', $groupeClients);
            $session->set('price_total', $priceTotal);
            $session->set('title', $title);
            return $this->redirectToRoute('recap_paiement');
        }
        
        return $this->render('louvre/identifications.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'type_jour'=>$typeJour,
            'title'=>$title,
            'controller_name' => 'LouvreController',
            'forminfo_client'=>$form->createView(),
            'groupe_client'=>$groupeClients
        ]);   
        
    }

    /**
     * @Route("/recap_paiement", name="recap_paiement")
     */
    public function Recap(Request $request)
    {
        $session=$request->getSession();
        $typeJour=$session->get('type_jour');
        $nombreBillet=$session->get('nombre_total_ticket');
        $dateBillet=$session->get('date_billet');
        $title=$session->get('title');
        $priceTotal=$session->get('price_total');
        
        $groupeClients=$session->get('groupe_client');
        

        $form=$this->createForm(GroupeClientsType::class, $groupeClients);
        
        return $this->render('louvre/recap_paiement.html.twig', [
            'nbBillet'=>$nombreBillet,
            'dateBillet'=>$dateBillet->format('d-m-Y'),
            'price_total'=>$priceTotal,
            'type_jour'=>$typeJour,
            'title'=>$title,
            'groupe_client'=>$groupeClients,
            'controller_name' => 'LouvreController',
            'forminfo_client'=>$form->createView()
            ]);
        
    }

    

    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function Payer(Request $request)
    {
        $session=$request->getSession();
        $title=$session->get('title');
        $dateBillet=$session->get('date_billet');
        $priceTotal=$session->get('price_total');
        $typeJour=$session->get('type_jour');
        $groupeClients=$session->get('groupe_client');
        $code=$session->get('code');
        
        \Stripe\Stripe::setApiKey("sk_test_v58YrQMVbowbCjD1DUPz7D0900tM8CdbBG");

        \Stripe\Charge::create(array(
            "amount"=>2000,
            "currency"=>"eur",
            "source"=>$_POST['stripeToken'],
            "description"=>"Paiement de test"
        ));
        $transport = (new \Swift_SmtpTransport('smtp.orange.fr', 465))
        ->setUsername('openclassrooms_IF@orange.fr')
        ->setPassword('testOPp4')
        ->setEncryption('ssl');
        $mailer = new \Swift_Mailer($transport);

        $message= (new \Swift_Message('Soyez les bienvenus au Louvre!!'))
        ->setFrom('noreply@museedulouvre.fr')
        ->setTo($title);
        $image = $message->embed(\Swift_Image::fromPath('img/louvre_logo_01.jpg'));
        $code=strtoupper(substr(md5(uniqid()),0,10));
        $message->setBody($this->renderView('louvre/email.html.twig',[
            'image'=>$image,
            'code'=>$code,
            'dateBillet' => $dateBillet->format('d-m-Y'),
            'groupe_client' => $groupeClients,
            'price_total' => $priceTotal,
            'type_jour'=>$typeJour,
            'title' => $title
         ]), 'text/html');
        

        $result = $mailer->send($message);

        return $this->render('louvre/confirmation.html.twig', [
            'title'=>$title
        ]);
    }

   
}



