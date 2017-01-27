<?php

namespace UCI\Boson\BackendBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UCI\Boson\BackendBundle\Generator\FormValidatorGenerator;

class GenerateFormValidatorCommand extends GeneratorCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:form:validator')
            ->setDescription('Generate a new constraint validator')
            ->addOption('bundle', '', InputOption::VALUE_REQUIRED, 'The name of the bundle')
            ->addOption('validator-name', '', InputOption::VALUE_REQUIRED, 'The name of the validator');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validatorName = $input->getOption('validator-name');
        $bundle = $this->getContainer()->get('kernel')->getBundle($input->getOption('bundle'));
        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $validatorName);


        $output->writeln('The validator was created successfully.');
    }

    protected function createGenerator()
    {
        return new FormValidatorGenerator($this->getContainer()->get('filesystem'));
    }


}
