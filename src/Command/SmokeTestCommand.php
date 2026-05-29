<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:smoke-test',
    description: 'Verifies the main Symfony and database wiring.'
)]
final class SmokeTestCommand extends Command
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Check des routes Symfony...');

        if (null === $this->router->getRouteCollection()->get('app_index')) {
            $output->writeln('<error>Missing route: app_index</error>');

            return Command::FAILURE;
        }

        $output->writeln('Check de la connexion à la base de données...');

        try {
            $this->connection->executeQuery('SELECT 1');
        } catch (\Throwable $exception) {
            $output->writeln(sprintf('<error>Check de la base de données échoué: %s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $requiredTables = ['tbl_activity', 'tbl_company', 'tbl_user', 'tbl_internship'];
        $existingTables = $this->connection->createSchemaManager()->listTableNames();

        foreach ($requiredTables as $tableName) {
            if (!in_array($tableName, $existingTables, true)) {
                $output->writeln(sprintf('<error>Missing table: %s</error>', $tableName));

                return Command::FAILURE;
            }
        }

        $output->writeln('<info>Smoke test fonctionnel</info>');

        return Command::SUCCESS;
    }
}