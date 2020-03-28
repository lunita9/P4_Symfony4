<?php
namespace App\Service\SendEmail;

use \Twig\Environment;

class SendEmail{
    
    private $twig;
    private $mailer;
 
    
    public function __construct(\Twig\Environment $xtwig)
    {
        $this->twig = $xtwig;
    }

    public function send($from, $to, $object, $twigName, $paramTwig, $image)
    {
        $transport = (new \Swift_SmtpTransport('smtp.orange.fr', 465))
        ->setUsername('openclassrooms_IF@orange.fr')
        ->setPassword('testOPp4')
        ->setEncryption('ssl');
        $mailer = new \Swift_Mailer($transport);

        $message= (new \Swift_Message($object))
        ->setFrom($from)
        ->setTo($to);
        $image = $message->embed(\Swift_Image::fromPath($image));
        $paramImage=['image'=>$image];
        $paramTwigTotal=array_merge($paramTwig, $paramImage);
    
    

        $content = $this->twig->render($twigName, $paramTwigTotal);

        $response = new Response($content);
    
        $message->setBody($response, 'text/html');
        $result = $mailer->send($message);
     }
}


