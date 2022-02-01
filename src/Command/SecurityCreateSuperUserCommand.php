<?php

namespace CoralMedia\Bundle\SecurityBundle\Command;

use CoralMedia\Bundle\SecurityBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SecurityCreateSuperUserCommand extends Command
{
    protected static $defaultName = 'security:create-super-user';

    private UserPasswordHasherInterface $passwordHasher;
    private string $randomPasswordSeed = "0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        string $name = null
    ) {
        parent::__construct($name);
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates Super User')
            ->addArgument('email', InputArgument::REQUIRED, 'User\'s email')
            ->addOption(
                'first-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'The user first-name',
                ''
            )
            ->addOption(
                'last-name',
                null,
                InputOption::VALUE_OPTIONAL,
                'The user last-name',
                ''
            )
            ->addOption(
                'random-password',
                null,
                InputOption::VALUE_OPTIONAL,
                'Generates random password',
                1
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('random-password') == 1) {
            $plainPassword = substr(str_shuffle($this->randomPasswordSeed), 0, 8);
        } else {
            $helper = $this->getHelper('question');

            $question = new Question('Type password');
            $question->setHidden(true);
            $question->setHiddenFallback(false);

            $plainPassword = $helper->ask($input, $output, $question);
        }

        $user = new User();
        $user->setEmail($input->getArgument('email'))
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setPassword(
                $this->passwordHasher->hashPassword($user, $plainPassword)
            );
        if ($input->getOption('first-name') !== '' && $input->getOption('last-name') !== '') {
            $user->setFirstName($input->getOption('first-name'))
                ->setLastName($input->getOption('last-name'))
                ->setEnabled(true);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $io->success(
            sprintf(
                'User \'%s\' created successfully with password \'%s\'.',
                $input->getArgument('email'),
                $plainPassword
            )
        );

        return Command::SUCCESS;
    }
}
