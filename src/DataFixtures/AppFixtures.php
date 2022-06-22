<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * AppFixtures construct.
     * 
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;    
    }

    /**
     * @param ObjectManager $manager
     * 
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $utilisateur = new Utilisateur();
            $utilisateur
                ->setNom('Nom'.$i)
                ->setPrenom('Prenom'.$i)
                ->setSociete('Societe'.$i)
                ->setEmail($i.'@foo.bar')
                ->setPassword($this->passwordEncoder->encodePassword($utilisateur, 'password'.$i))
            ;

            $manager->persist($utilisateur);
        }

        $manager->flush();
    }
}
