<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('app:example')
            ->setDescription("Echo hello, world!");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("\n<info>Hello, world!</info>");
    }
}
