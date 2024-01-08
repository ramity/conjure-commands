<?php

namespace Ramity\Bundle\ConjureBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

#[AsCommand( name: 'conjure:wrapper', description: 'A wrapper command for calling other internal commands and simulating user input.' )]
class WrapperCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('A wrapper command for calling other internal commands and simulating user input.')
            ->addArgument('commandString', InputArgument::REQUIRED, 'The command to be called.')
            ->addArgument('inputs', InputArgument::IS_ARRAY, 'The user inputs to be passed to the command.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandString = $input->getArgument('commandString');

        $application = $this->getApplication();
        $command = $application->find($commandString);
        $commandTester = new CommandTester($command);

        // Simulate user inputs
        $inputs = $input->getArgument('inputs');
        $commandTester->setInputs($inputs);
        $commandTester->execute(['command' => $commandString]);

        $response = $commandTester->getDisplay();
        // echo $response;
        $output->writeln($response);

        return Command::SUCCESS;
    }
}
