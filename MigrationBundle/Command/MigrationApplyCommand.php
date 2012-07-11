<?php
namespace Estina\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationApplyCommand extends ContainerAwareCommand
{
    /**
     * @see ContainerAwareCommand
     */
    protected function configure()
    {
        $this
            ->setName('migration:apply')
            ->setDescription('Apply all inactive migrations.');
        ;
    }

    /**
     * @see ContainerAwareCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migration = $this->getContainer()->get('estina_migration.service.migration');
        $migration->apply();
        // try {
        //     $filename = $migration->createMigration();
        //     $output->writeln("New migration script: <info>$filename</info>");
        // } catch (\Exception $e) {
        //     $output->writeln("Error: <error>{$e->getMessage()}</error>");
        // }
    }
}


