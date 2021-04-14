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

        $campus = new Campus();
        $campus->setCampusName("ENI Nantes");
        $manager->persist($campus);
        $manager->flush();

        $campus = new Campus();
        $campus->setCampusName("ENI Paris");
        $manager->persist($campus);
        $manager->flush();

        $campus = new Campus();
        $campus->setCampusName("ENI Rennes");
        $manager->persist($campus);
        $manager->flush();

        $allCampus = $manager->getRepository(Campus::class)->findAll();

        for($i = 0; $i < 30; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $user = new User();
            $user->setFirstName($faker->name());
            $user->setLastName($faker->name());
            $user->setPhone("0123456789");
            $user->setmail($faker->email());
            $user->setUsername($faker->username());
            $user->setPassword($this->encoder->encodePassword($user, $faker->password()));
            $user->setRoles(['ROLE_USER']);
            $user->setCampus($faker->randomElement($allCampus));
            $manager->persist($user);
        }
        $manager->flush();


        $user = new User();
        $faker = \Faker\Factory::create("fr_FR");
        $user->setFirstName("Dimitri");
        $user->setLastName("GERMANY");
        $user->setPhone("0123456789");
        $user->setmail($faker->email());
        $user->setUsername("dim");
        $user->setPassword($this->encoder->encodePassword($user, "0123456"));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCampus($faker->randomElement($allCampus));
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $faker = \Faker\Factory::create("fr_FR");
        $user->setFirstName("test");
        $user->setLastName("test");
        $user->setPhone("0123456789");
        $user->setmail($faker->email());
        $user->setUsername("test");
        $user->setPassword($this->encoder->encodePassword($user, "0123456"));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCampus($faker->randomElement($allCampus));
        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $faker = \Faker\Factory::create("fr_FR");
        $user->setFirstName("test1");
        $user->setLastName("test1");
        $user->setPhone("0123456789");
        $user->setmail($faker->email());
        $user->setUsername("test1");
        $user->setPassword($this->encoder->encodePassword($user, "0123456"));
        $user->setRoles(['ROLE_USER']);
        $user->setCampus($faker->randomElement($allCampus));
        $manager->persist($user);
        $manager->flush();




        for($i = 0; $i < 30; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $city = new City();
            $city->setName($faker->city());
            $city->setZipCode(mt_rand(1, 976));
            $manager->persist($city);
        }
        $manager->flush();

        $allCities = $manager->getRepository(City::class)->findAll();

        for($i = 0; $i < 30; $i++) {
            $faker = \Faker\Factory::create("fr_FR");
            $location = new Location();
            $location->setName($faker->name());
            $location->setStreet($faker->name());
            $location->setCity($faker->randomElement($allCities));
            $manager->persist($location);
        }
        $manager->flush();

        $state = new State();
        $state->setLabel("Creee");
        $manager->persist($state);
        $manager->flush();

        $state = new State();
        $state->setLabel("Ouverte");
        $manager->persist($state);
        $manager->flush();

        $state = new State();
        $state->setLabel("Cloturée");
        $manager->persist($state);
        $manager->flush();

        $state = new State();
        $state->setLabel("En cours");
        $manager->persist($state);
        $manager->flush();

        $state = new State();
        $state->setLabel("Passee");
        $manager->persist($state);
        $manager->flush();

        $state = new State();
        $state->setLabel("Annulee");
        $manager->persist($state);
        $manager->flush();

        $allUsers = $manager->getRepository(User::class)->findAll();
        $allLocations = $manager->getRepository(Location::class)->findAll();
        $allState = $manager->getRepository(State::class)->findAll();
        $allCampus = $manager->getRepository(Campus::class)->findAll();

        //plein de création d'événements
        for($i = 0; $i < 30; $i++) {

            $event = new Event();
            $event->setName( $faker->sentence );
            $event->setEventDetails( $faker->realText(1000) );
            $event->setNbMaxRegistration(mt_rand(10, 100));
            $event->setStartingDateTime( $faker->dateTimeBetween('now', '+ 6 months') );
            $event->setDuration(mt_rand(1, 20));

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
