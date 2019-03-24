<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Formulaire;

class FormulaireFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=1;$i<=10;$i++){
            $formulaire=new Formulaire();
            $formulaire->setTitle("Titre du formulaire n°$i");
            $manager->persist($formulaire);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
