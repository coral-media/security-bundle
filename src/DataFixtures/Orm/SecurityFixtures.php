<?php

namespace CoralMedia\Bundle\SecurityBundle\DataFixtures\Orm;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use CoralMedia\Bundle\SecurityBundle\Entity\User;

class SecurityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('superadmin@local')
            ->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setPassword(
            '$2y$13$TP9hFCwcvNTSxof2XlYYjO1SZ5Oeyg5mSA4SNnqEi8mNAbDCmpMdy'
        );

        $this->addReference(sha1($user->getEmail()), $user);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('admin@local')
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            '$2y$13$NjqGU.EvxlLb4bc5LRUS6uVN.LtMfOPswSYRAgYHpqN.yfZrmB7ci'
        );

        $this->addReference(sha1($user->getEmail()), $user);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('api@local')
            ->setRoles(['ROLE_API']);
        $user->setPassword(
            '$2y$13$2ozvt69hUC1QTmpZyp2dMO9XH0EYV0HyZ896m/RR.aA/ttqvrvxlW'
        );

        $this->addReference(sha1($user->getEmail()), $user);
        $manager->persist($user);

        $user = new User();
        $user->setEmail('user@local')
            ->setRoles(['ROLE_USER']);
        $user->setPassword(
            '$2y$13$gZA0aBLqLWVmVKUovY7hLOE2IZxsEOuIyIXlwzrN9NwO0ZfXgemdC'
        );

        $this->addReference(sha1($user->getEmail()), $user);
        $manager->persist($user);

        $manager->flush();
    }
}
