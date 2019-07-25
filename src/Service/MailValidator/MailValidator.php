<?php
namespace App\Service\MailValidator;
use Twig\Environment;
use App\Entity\Reservation;
use App\Entity\InfoClient;

class MailValidator 
{
    /**
     * @var \Swift_Mailer
     * @var Request
     */
    private $mailer;
    
    public function __CONSTRUCT(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        
    }
    public function notify($dateBillet, $groupeClients, $priceTotal, $title)
    {
        $message = (new \Swift_Message('Votre billet - Musée du Louvre'))
            ->setFrom('openclassrooms_IF@orange.fr')
            ->setTo($title)
            ->setReplyTo($title);
        
        $message->setBody($this->request->request('blog/email.html.twig', [
            'dateBillet' => $dateBillet,
            'groupe_client' => $groupeClients,
            'price_total' => $priceTotal,
            'title' => $title
        ]), 'text/html');
        
        $this->mailer->send($message);
    }
}