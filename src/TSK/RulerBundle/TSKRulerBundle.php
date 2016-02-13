<?php
namespace TSK\RulerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use TSK\RulerBundle\DependencyInjection\Compiler\RewardsEngineCompilerPass;

class TSKRulerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RewardsEngineCompilerPass());
    }
}
