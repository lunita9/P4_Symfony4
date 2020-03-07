<?php

namespace App\tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LouvreControllerTest extends WebTestCase
{
    public function testHomeIsUp()
    {
        $client = static::createClient();
        $client->request('GET','/');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testHomeTitle()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/');
        
        $this->assertEquals(1, $crawler->filter('html:contains("Bienvenue au Musée du Louvre !")')->count());
    }
    
    public function testChoixDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/');
        
        $link = $crawler->selectLink('Billetterie')->link();
        $crawler = $client->click($link);
        
        $this->assertEquals('App\Controller\LouvreController::Billeterie', $client->getRequest()->attributes->get('_controller'));
        
        $form = $crawler->selectButton('Continuer')->form();
        $form['formulaire[title]'] = 'irene_fedaoui@orange.fr';
        $form['formulaire[date_billet]'] = date('2021-10-09');
        $form['formulaire[type_jour]'] = 'Journée';
        $form['formulaire[nombre_total_ticket]'] = 2;
        
        $client->submit($form);
        $crawler = $client->followRedirect();
        
        $this->assertEquals('App\Controller\LouvreController::Detail_Client', $client->getRequest()->attributes->get('_controller'));
    }
}