<?php

namespace App\DataFixtures;

use Faker;
use DateTime;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Journee;
use App\Entity\Commande;
use App\Entity\Location;
use App\Entity\Facturation;
use App\Entity\Prestations;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{ 
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

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
        $user->setNom('admin')
        ->setPrenom('admin')
        ->setEmail('admin@gmail.com')
        ->setRole('0')
        ->setPassword($this->encoder->hashPassword($user, 'admin'));
        $this->setReference('admin', $user);
        $manager->persist($user);
        for($i = 0; $i < 10; $i++)
        {
            $user = new User;
            $user->setNom($faker->lastName)
                ->setPrenom($faker->firstName)
                ->setEmail($faker->email)
                ->setRole('1')
                ->setPassword($this->encoder->hashPassword($user, 'bob'));
                $this->setReference('U'. $i, $user);
            $manager->persist($user);
        }
        $manager->persist($user);
        for($i = 0; $i < 20; $i++)
        {
            $location = new Location();
            $location->setUser($this->getReference('admin'))
                ->setPrestations($this->getReference('P' .rand(0, 3) ));
            $this->setReference('L' . $i, $location);
            $manager->persist($location);
        }

        for($i = 20; $i < 50; $i++)
        {
            $location = new Location();
            $location->setUser($this->getReference('U'. rand(0, 9)))
                ->setPrestations($this->getReference('P' .rand(0, 3) ));
            $this->setReference('L' . $i, $location);
            $manager->persist($location);
        }
        
        for($i = 50; $i < 60; $i++)
        {
            $location = new Location();
            $location->setUser($this->getReference('admin'))
                ->setPrestations($this->getReference('P' .rand(4, 6) ));
            $this->setReference('L' . $i, $location);
            $manager->persist($location);
        }

        for($i = 60; $i < 90; $i++)
        {
            $location = new Location();
            $location->setUser($this->getReference('admin'))
                ->setPrestations($this->getReference('P' .rand(7, 9) ));
            $this->setReference('L' . $i, $location);
            $manager->persist($location);
        }

        for($i = 0; $i < 50; $i++)
        {
            $commande = new Commande();
            $commande->setDateStart($faker->dateTimeBetween(  strftime('%D' ,1620172800),  strftime('%D' ,1633824000)))
                ->setlocation($this->getReference('L'. rand(0,89)));
            $manager->persist($commande);
            $adultes = $faker->numberBetween(1,4);
            $enfant = $faker->numberBetween(1,4);
            if(strftime('%j',$commande->getDateStart()->getTimestamp())  <  strftime('%j' ,strtotime('2021-06-21'))
            || strftime('%j',$commande->getDateStart()->getTimestamp())  <  strftime('%j' ,strtotime('2021-08-31') ))
                $majoration = true;
            else
                $majoration = false;
            
            for($c = 0; $c < $faker->numberBetween(1, 16); $c++)
            { 
                $ed = new Journee();
                $ed->setAdultes($adultes)
                    ->setEnfants($enfant)
                    ->setPiscineEnfant($faker->numberBetween(0, $enfant))
                    ->setPiscineAdulte($faker->numberBetween(0, $adultes))
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
                ->setDateFacturation( $faker->dateTimeBetween(  strftime('%D' ,1620172800),  strftime('%D' ,1620172800)))
                ->setDateCheck($faker->boolean())
                ->setClient($client);
            $manager->persist($facturation);
        }
        $manager->flush();
    }
}
