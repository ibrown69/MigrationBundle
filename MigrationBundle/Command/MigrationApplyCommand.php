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
            ->setDescription('Apply all inactive migrations');
        ;
    }

    /**
     * @see ContainerAwareCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migration = $this->getContainer()->get('estina_migration.service.migration');

        if (false === $migration->isTableCreated()) {
            $output->writeln("Migrations table is not created. Run following command to create it:");
            $output->writeln("  <comment>app/console migration:init</comment>");
        } else {
            $files = array();
            $e = null;
            try {
                $files = $migration->apply($output);
            } catch (\Exception $e) {
                $output->writeln('<error>FAIL</error>');
            }

            if (0 === count($files)) {
                $output->writeln("No migrations were applied");
            } 

            if (null !== $e) {
                throw $e;
            }
        }
    }
}


