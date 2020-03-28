<?php
namespace App\Service\Payment;

use Symfony\Component\Dotenv\Dotenv;

class Payment{
  public $succeed = false;
    
  
  public function pay($data){

    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../../../.env');

    \Stripe\Stripe::setApiKey($_ENV['STRIPE_SK']);
    try {
      \Stripe\Charge::create(array(
        "amount"=>$data["priceTotal"] * 100,
        "currency"=>"eur",
        "source"=>$data['stripeToken'],
        "description"=>"Paiement billets Louvre"
      ));
      $this->succeed = true;
    
     } catch (\Exception $e) {
      $this->succeed = false;
     }
   }
}