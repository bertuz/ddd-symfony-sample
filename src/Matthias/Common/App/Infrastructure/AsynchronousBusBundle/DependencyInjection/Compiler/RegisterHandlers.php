<?php

namespace Matthias\Common\App\Infrastructure\AsynchronousBusBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterHandlers implements CompilerPassInterface
{
    use CollectServices;

    private $serviceId;
    private $tag;
    private $keyAttribute;

    /**
     * @param string  $serviceId            The service id of the MessageHandlerMap
     * @param string  $tag                  The tag name of message handler services
     * @param string  $keyAttribute         The name of the tag attribute that contains the name of the handler
     */
    public function __construct($serviceId, $tag, $keyAttribute)
    {
        $this->serviceId = $serviceId;
        $this->tag = $tag;
        $this->keyAttribute = $keyAttribute;
    }

    /**
     * Search for message handler services and provide them as a constructor argument to the message handler map
     * service.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition($this->serviceId);

        $handlers = array();

        $this->collectServiceIds(
            $container,
            $this->tag,
            $this->keyAttribute,
            function ($key, $serviceId) use (&$handlers) {
                $handlers[$key] = $serviceId;
            }
        );

        $definition->replaceArgument(0, $handlers);
    }
}
