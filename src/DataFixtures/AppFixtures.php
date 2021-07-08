<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Facturation;
use App\Entity\Journee;
use App\Entity\Location;
use App\Entity\Prestations;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;


class AppFixtures extends Fixture
{ 
    public $i = 0;

    public function load(ObjectManager $manager)
    {
        function prestations(AppFixtures $fix, string $prestation, float $price, ObjectManager $manager)
        {
            $tarif = new Prestations();
            $tarif->setLabel($prestation)
                ->setPrice($price);
            $fix->setReference('P' . $fix->i, $tarif);
            $fix->i += 1;
            $manager->persist($tarif);
        }

        prestations($this, 'M-H 3 personnes', 20, $manager);
        prestations($this, 'M-H 4 personnes', 24, $manager);
        prestations($this, 'M-H 5 personnes', 27, $manager );
        prestations($this, 'M-H 6-8 personnes', 34, $manager );
        prestations($this, 'Caravane 2 places', 15, $manager);
        prestations($this, 'Caravane 4 places ', 18, $manager );
        prestations($this, 'Caravane 6 places', 24, $manager );
        prestations($this, 'Emplacement 8 m²', 12, $manager );
        prestations($this, 'Emplacement 12 m² ', 14, $manager );
        prestations($this, 'taxe de séjour enfant', 0.35, $manager );
        prestations($this, 'taxe de séjour Adulte', 0.60, $manager );
        prestations($this, 'Piscine adulte', 1.5, $manager );
        prestations($this, 'Piscine enfant', 1, $manager );

        $faker = Faker\Factory::create('fr_FR');

        $user = new User;
        $user->setNom('bob')
            ->setPrenom('bobi')
            ->setEmail('bobileboss@gmail.com')
            ->setRole('1')
            ->setPassword('passwordGros');
            $this->setReference('U'. 0, $user);
        $manager->persist($user);

        $user = new User;
            $user->setNom('admin')
            ->setPrenom('admin')
            ->setEmail('admin@gmail.com')
            ->setRole('0')
            ->setPassword('admin');

        $manager->persist($user);
        for($i = 0; $i < 50; $i++)
        {
            $location = new Location();
            $location->setUser($this->getReference('U'. 0))
                ->setPrestations($this->getReference('P' .rand(0, 8) ));
            $this->setReference('L' . $i, $location);
            $manager->persist($location);
        }

        for($i = 0; $i < 50; $i++)
        {
            $commande = new Commande();
            $commande->setDateStart($faker->dateTimeBetween('-2 month', '+2 month' ))
                ->setlocation($this->getReference('L'. rand(0,49)));
            $manager->persist($commande);
            $adultes = $faker->numberBetween(1,4);
            $enfant = $faker->numberBetween(1,4);
            $majoration = $faker->boolean();
            for($c = 0; $c < $faker->numberBetween(1, 16); $c++)
            { 
                $ed = new Journee();
                $ed->setAdultes($adultes)
                    ->setEnfants($enfant)
                    ->setPiscineEnfant($faker->numberBetween(0, 8))
                    ->setPiscineAdulte($faker->numberBetween(0, 8))
                    ->setMajoration($majoration)
                    ->setCommande($commande);
                $this->setReference('J'.$c, $ed);
                $manager->persist($ed);
            }

            $client = new Client();
            $client->setPrenom($faker->name)
                ->setNom($faker->name)
                ->setEmail($faker->email);
            $manager->persist($client);

            $facturation = new Facturation();
            $facturation->setCommande($commande)
                ->setDateFacturation(new \DateTime($faker->date('Y-m-d', 'now')))
                ->setDateCheck($faker->boolean())
                ->setClient($client);
            $manager->persist($facturation);
        }
        $manager->flush();
    }
}
