<?php
namespace MMT;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{

    public function configure() {

        $this->setName("hello")
            ->setDescription("A simple Greeting command")
            ->addArgument('name', InputArgument::REQUIRED, 'enter your name to get a warm greeting!');

    }

    public function execute(InputInterface $input, OutputInterface $output) {

        $name = $input->getArgument('name');

        $output->writeln('Hello, ' . $name);

    }

}