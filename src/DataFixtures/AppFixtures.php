<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\State;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $user = new User();
            $user->setFirstName($faker->name());
            $user->setLastName($faker->name());
            $user->setPhone("0123456789");
            $user->setmail($faker->email());
            $user->setUsername($faker->username());
            $user->setPassword($this->encoder->encodePassword($user, $faker->password()));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }
        $manager->flush();

            $campus = new Campus();
            $campus->setCampusName("ENI Nantes");
            $campus->setCampusName("ENI Paris");
            $manager->persist($campus);

        $manager->flush();

        for($i = 0; $i < 10; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $city = new City();
            $city->setName($faker->city());
            $city->setZipCode(mt_rand(1, 976));
            $manager->persist($city);
        }
        $manager->flush();

        $allCities = $manager->getRepository(City::class)->findAll();

        for($i = 0; $i < 10; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $location = new Location();
            $location->setName($faker->name());
            $location->setCity($faker->randomElement($allCities));
        }
        $manager->flush();

        $state = new State();
        $state->setLabel("Creee");
        $state->setLabel("Ouverte");
        $state->setLabel("Cloturée");
        $state->setLabel("En cours");
        $state->setLabel("Passee");
        $state->setLabel("Annulee");
        $manager->persist($state);
        $manager->flush();

        $allUsers = $manager->getRepository(User::class)->findAll();
        $allLocations = $manager->getRepository(Location::class)->findAll();
        $allState = $manager->getRepository(State::class)->findAll();
        $allCampus = $manager->getRepository(Campus::class)->findAll();

        //plein de création d'événements
        for($i = 0; $i < 10; $i++) {

            $event = new Event();
            $event->setName( $faker->sentence );
            $event->setEventDetails( $faker->realText(1000) );
            $event->setNbMaxRegistration(mt_rand(10, 100));
            $event->setStartingDateTime( $faker->dateTimeBetween('now', '+ 6 months') );
            $event->setDuration( $faker-> dateTimeBetween('+ 1 month', "+ 6 months"));

            //un utilisateur au hasard en tant qu'organisateur
            $event->setOrganizer( $faker->randomElement($allUsers) );
            $event->setLocation($faker->randomElement($allLocations));
            $event->setStatus($faker->randomElement($allState));
            $event->setSite($faker->randomElement($allCampus));
            $event->setInscriptionDeadLine($faker->dateTimeBetween('now', '+ 6 months'));

            //on sauvegarde dans la boucle
            $manager->persist($event);
        }
        $manager->flush();
    }
}
