<?php

namespace Quark\Bin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected static $defaultName = 'quark:make:migration';

    protected function configure()
    {
        $this->setDescription('The command to create the migration file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Привет, мир!');
        return Command::SUCCESS;
    }
}
