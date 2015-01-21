<?php

namespace Matthias\Common\App\Infrastructure\AsynchronousBusBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class EventBusExtension extends ConfigurableExtension
{
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
        return new EventBusConfiguration($this->getAlias());
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('asynchronous_event_bus.yml');

        $container->setAlias(
            'asynchronous_bus.asynchronous_event_bus.event_name_resolver',
            'asynchronous_bus.asynchronous_event_bus.' . $mergedConfig['event_name_resolver_strategy'] . '_event_name_resolver'
        );
    }
}
