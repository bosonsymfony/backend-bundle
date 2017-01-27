<?php

namespace UCI\Boson\BackendBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use UCI\Boson\BackendBundle\Generator\AngularAppGenerator;

class GenerateAngularAppCommand extends GeneratorCommand
{
    protected function configure()
    {
        $this
            ->setName('generate:angular:app')
            ->setDescription('Generates a angular application structure')
            ->addOption('bundle', '', InputOption::VALUE_REQUIRED, 'The name of the bundle')
            ->addOption('route-format', '', InputOption::VALUE_REQUIRED, 'The format that is used for the routing (yml, xml, php, annotation)', 'annotation')
            ->setHelp(<<<EOT

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        if (null === $input->getOption('bundle')) {
            throw new \RuntimeException('The bundle option must be provided.');
        }

        $bundle = $input->getOption('bundle');
        if (is_string($bundle)) {
            $bundle = Validators::validateBundleName($bundle);

            try {
                $bundle = $this->getContainer()->get('kernel')->getBundle($bundle);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
        }

        $questionHelper->writeSection($output, 'Angular Application generation');

        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, Util::createAppName($bundle->getName()), $input->getOption('route-format'));

        $output->writeln('Generating the angular application code: <info>OK</info>');

        $questionHelper->writeGeneratorSummary($output, array());

        $output->writeln($this->updateRouting($questionHelper, $input, $output, $bundle->getName(), $input->getOption('route-format')));

        $installAssets = true;
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Confirm automatic install assets', 'yes', '?'), true);
            $installAssets = $questionHelper->ask($input, $output, $question);
        }

        if ($installAssets) {
            $kernel = $this->getContainer()->get('kernel');
            $application = new Application($kernel);
            $application->run(new ArrayInput(array(
                'command' => 'assets:install',
                '--symlink' => true
            )));
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Angular application generator');

        // Bundle
        $output->writeln(array(
            'First, you need to give the bundle name where you will generate application.',
            'Example <comment>AcmeBlogBundle</comment>',
            '',
        ));

        $bundleNames = array_keys($this->getContainer()->get('kernel')->getBundles());

        while (true) {
            $question = new Question($questionHelper->getQuestion('Bundle name', $input->getOption('bundle')), $input->getOption('bundle'));
            $question->setAutocompleterValues($bundleNames);
            $bundle = $questionHelper->ask($input, $output, $question);

            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);
                break;
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
        }
        $input->setOption('bundle', $bundle);


        // routing format
        $defaultFormat = (null !== $input->getOption('route-format') ? $input->getOption('route-format') : 'annotation');
        $output->writeln(array(
            '',
            'Determine the format to use for the routing.',
            '',
        ));

        $formats = array('yml', 'xml', 'php', 'annotation');

        $question = new Question($questionHelper->getQuestion('Routing format (php, xml, yml, annotation)', $defaultFormat), $defaultFormat);
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'));
        $question->setAutocompleterValues($formats);
        $routeFormat = $questionHelper->ask($input, $output, $question);
        $input->setOption('route-format', $routeFormat);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg-white', true),
            '',
            sprintf('You are going to generate a "<info>%s</info>" angular application in the %s bundle', Util::createAppName($bundle), $bundle),
            sprintf('using the "<info>%s</info>" format for the routing', $routeFormat)
        ));

    }

    protected function updateRouting(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, $bundle, $format)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $question = new ConfirmationQuestion($questionHelper->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
            $auto = $questionHelper->ask($input, $output, $question);
        }

        $output->write('Importing the angular app routing resource: ');
        $routing = new RoutingManipulator($this->getContainer()->getParameter('kernel.root_dir') . '/config/routing.yml');
        try {
            $ret = $auto ? $routing->addResource($bundle, $format, '/angular_app') : true;
            if (!$ret) {
                if ('annotation' === $format) {
                    $help = sprintf("        <comment>resource: \"@%s/Controller/\"</comment>\n        <comment>type:     annotation</comment>\n", $bundle);
                } else {
                    $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing.%s\"</comment>\n", $bundle, $format);
                }
                $help .= "        <comment>prefix:   /</comment>\n";

                return array(
                    '- Import the bundle\'s routing resource in the app main routing file:',
                    '',
                    sprintf('    <comment>%s:</comment>', $bundle),
                    $help,
                    '',
                );
            }
        } catch (\RuntimeException $e) {
            return array(
                sprintf('Bundle <comment>%s</comment> is already imported.', $bundle),
                '',
            );
        }
    }

    protected function createGenerator()
    {
        return new AngularAppGenerator($this->getContainer()->get('filesystem'));
    }


}
