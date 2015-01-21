<?php

namespace Matthias\Common\App\Infrastructure\AsynchronousBusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfigureMiddlewares implements CompilerPassInterface
{
    private $mainBusId;
    private $busTag;

    public function __construct($mainBusId, $busTag)
    {
        $this->mainBusId = $mainBusId;
        $this->busTag = $busTag;
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $middlewareIds = new \SplPriorityQueue();

        foreach ($container->findTaggedServiceIds($this->busTag) as $specializedBusId => $tags) {
            foreach ($tags as $tagAttributes) {
                $priority = isset($tagAttributes['priority']) ? $tagAttributes['priority'] : 0;
                $middlewareIds->insert($specializedBusId, $priority);
            }
        }

        $orderedMiddlewareIds = iterator_to_array($middlewareIds, false);

        $mainBusDefinition = $container->findDefinition($this->mainBusId);
        foreach ($orderedMiddlewareIds as $middlewareId) {
            $mainBusDefinition->addMethodCall('appendMiddleware', array(new Reference($middlewareId)));
        }
    }
}
