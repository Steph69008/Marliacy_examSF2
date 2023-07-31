<?php

namespace App\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-outdated-staff',
    description: 'Add a short description for your command',
)]
class DeleteOutdatedStaffCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $users = $this->entityManager->getRepository(\App\Entity\User::class)->findAll();

        $currentDate = new DateTime();

        // Parcourir tous les utilisateurs
        foreach($users as $user) {
            // Si l'utilisateur a une date expirée ET que le rôle de l'utilisateur n'est pas 'RH'
            if ($user->getReleaseDate() < $currentDate && in_array('ROLE_USER', $user->getRoles())) {
                // Supprimer l'utilisateur
                $this->entityManager->remove($user);
            }
        }

        // Exécuter la suppression
        $this->entityManager->flush();

        $io->success('Les employées qui ont un contrat expirées ont été supprimés avec succès.');

        return Command::SUCCESS;
    }
}
