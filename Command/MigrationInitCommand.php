<?php
namespace Estina\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationInitCommand extends ContainerAwareCommand
{
    /**
     * @see ContainerAwareCommand
     */
    protected function configure()
    {
        $this
            ->setName('migration:init')
            ->setDescription('Create migrations table');
        ;
    }

    /**
     * @see ContainerAwareCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migration = $this->getContainer()->get('estina_migration.service.migration');
        if (false === $migration->isTableCreated()) {
            $output->write('Creating migrations table ');
            if ($migration->createTable()) {
                $output->writeln('<info>OK</info>');
            } else {
                $output->writeln('<info>FAIL</info>');
            }
        } else {
            $output->writeln('<comment>Migrations table already created.</comment>');
        }
    }
}
