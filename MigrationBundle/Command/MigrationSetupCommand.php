<?php
namespace Estina\MigrationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationSetupCommand extends ContainerAwareCommand
{
    /**
     * @see ContainerAwareCommand
     */
    protected function configure()
    {
        $this
            ->setName('migration:setup')
            ->setDescription('Setup DB core');
        ;
    }

    /**
     * @see ContainerAwareCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migration = $this->getContainer()->get('estina_migration.service.migration');


        try {
            $files = $migration->setup($output);
        } catch (\Exception $e) {
            $output->writeln('<error>FAIL. '.$e.'</error>');
        }

       
    }
}


