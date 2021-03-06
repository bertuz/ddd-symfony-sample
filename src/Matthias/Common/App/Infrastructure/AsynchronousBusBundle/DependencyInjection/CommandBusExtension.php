<?php

namespace Matthias\Common\App\Infrastructure\AsynchronousBusBundle\DependencyInjection;

use SimpleBus\SymfonyBridge\DependencyInjection\CommandBusConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class CommandBusExtension extends ConfigurableExtension
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new CommandBusConfiguration($this->getAlias());
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('asynchronous_command_bus.yml');

        $container->setAlias(
            'asynchronous_bus.asynchronous_command_bus.command_name_resolver',
            'asynchronous_bus.asynchronous_command_bus.' . $mergedConfig['command_name_resolver_strategy'] . '_command_name_resolver'
        );
    }
}
