<?php

namespace App;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class() implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition('doctrine.orm.default_configuration')
                    ->addMethodCall(
                        'setIdentityGenerationPreferences',
                        [
                            [
                                PostgreSQLPlatform::class => ClassMetadata::GENERATOR_TYPE_SEQUENCE,
                            ],
                        ]
                    );
            }
        });
    }
}
