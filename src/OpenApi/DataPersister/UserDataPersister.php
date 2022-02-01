<?php

namespace CoralMedia\Bundle\SecurityBundle\OpenApi\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use CoralMedia\Bundle\SecurityBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface, ResumableDataPersisterInterface
{
    protected ContextAwareDataPersisterInterface $decorated;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ContextAwareDataPersisterInterface $decorated,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->decorated = $decorated;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    /**
     * @param User $data
     * @param array $context
     * @return object
     */
    public function persist($data, array $context = []): object
    {
        if ($data instanceof PasswordAuthenticatedUserInterface) {
            $data->setPassword(
                $this->passwordHasher->hashPassword(
                    $data,
                    $data->getPlainPassword()
                )
            );
        }
        return $this->decorated->persist($data, $context);
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }

    public function resumable(array $context = []): bool
    {
        return true;
    }
}
