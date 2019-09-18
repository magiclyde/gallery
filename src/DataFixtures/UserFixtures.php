<?php

// php bin/console security:encode-password
// php bin/console doctrine:fixtures:load

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\UserManager;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

	private $userManager;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserManager $userManager)
	{
        $this->passwordEncoder = $passwordEncoder;
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->userManager->createUser();
        $user->setEmail('xx@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user,'plainPasswd')
        );

        $manager->persist($user);
        $manager->flush();
    }
}
