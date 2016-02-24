<?php
/**
 * Created by PhpStorm.
 * User: rosi
 * Date: 21/02/16
 * Time: 16:50
 */

namespace UCI\Boson\BackendBundle\Descriptor;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Descriptor\ApplicationDescription;
use Symfony\Component\Console\Descriptor\Descriptor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class ArrayDescriptor
{
    /**
     * @var Application
     */
    private $application;

    /**
     * ArrayDescriptor constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $application = new Application($kernel);

        foreach ($kernel->getBundles() as $index => $bundle) {
            if ($bundle instanceof Bundle) {
                $bundle->registerCommands($application);
            }
        }
        $this->application = $application;
    }

    /**
     * @return array
     */
    public function describeApplication()
    {
        $description = new ApplicationDescription($this->application);
        $commands = array();

        foreach ($description->getCommands() as $command) {
            $commands[$command->getName()] = $this->getCommandData($command);
        }

        $data = array(
            'commands' => $commands,
            'namespaces' => $this->getTransformedNamespaces(array_values($description->getNamespaces())));

        return $data;
    }

    /**
     * @param InputArgument $argument
     *
     * @return array
     */
    private function getInputArgumentData(InputArgument $argument)
    {
        return array(
            'name' => $argument->getName(),
            'is_required' => $argument->isRequired(),
            'is_array' => $argument->isArray(),
            'description' => preg_replace('/\s*[\r\n]\s*/', ' ', $argument->getDescription()),
            'default' => $argument->getDefault(),
        );
    }

    /**
     * @param InputOption $option
     *
     * @return array
     */
    private function getInputOptionData(InputOption $option)
    {
        return array(
            'name' => '--' . $option->getName(),
            'shortcut' => $option->getShortcut() ? '-' . implode('|-', explode('|', $option->getShortcut())) : '',
            'accept_value' => $option->acceptValue(),
            'is_value_required' => $option->isValueRequired(),
            'is_multiple' => $option->isArray(),
            'description' => preg_replace('/\s*[\r\n]\s*/', ' ', $option->getDescription()),
            'default' => $option->getDefault(),
        );
    }

    /**
     * @param InputDefinition $definition
     *
     * @return array
     */
    private function getInputDefinitionData(InputDefinition $definition)
    {
        $inputArguments = array();
        foreach ($definition->getArguments() as $name => $argument) {
            $inputArguments[$name] = $this->getInputArgumentData($argument);
        }

        $inputOptions = array();
        foreach ($definition->getOptions() as $name => $option) {
            $inputOptions[$name] = $this->getInputOptionData($option);
        }

        return array('arguments' => $inputArguments, 'options' => $inputOptions);
    }

    /**
     * @param Command $command
     *
     * @return array
     */
    private function getCommandData(Command $command)
    {
        $command->getSynopsis();
        $command->mergeApplicationDefinition(false);

        return array(
            'name' => $command->getName(),
            'usage' => array_merge(array($command->getSynopsis()), $command->getUsages(), $command->getAliases()),
            'description' => $command->getDescription(),
            'help' => $command->getProcessedHelp(),
            'definition' => $this->getInputDefinitionData($command->getNativeDefinition()),
        );
    }

    /**
     * @param array $namespaces
     *
     * @return array
     */
    private function getTransformedNamespaces(array $namespaces)
    {
        $result = array();
        foreach ($namespaces as $index => $namespace) {
            $commands = array();
            foreach ($namespace['commands'] as $command) {
                $commandInstance = $this->application->find($command);
                $commands[] = array(
                    $command => $commandInstance->getName()
                );
            }
            $result[] = array(
                'id' => $namespace['id'],
                'commands' => $commands
            );
        }
        return $result;
    }
}