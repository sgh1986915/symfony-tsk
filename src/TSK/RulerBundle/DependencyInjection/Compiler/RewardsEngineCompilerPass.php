<?php
namespace TSK\RulerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RewardsEngineCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tsk_ruler.rewards_engine_chain')) {
            return;
        }

        $definition = $container->getDefinition('tsk_ruler.rewards_engine_chain');

        $taggedServices = $container->findTaggedServiceIds('tsk_ruler.rewards_engine');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addRewardsEngine',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
