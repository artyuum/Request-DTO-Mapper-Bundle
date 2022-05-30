<?php

namespace Artyum\RequestDtoMapperBundle\DependencyInjection;

use Artyum\RequestDtoMapperBundle\Mapper\Mapper;
use Artyum\RequestDtoMapperBundle\Source\SourceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ArtyumRequestDtoMapperExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.php');

        $container
            ->registerForAutoconfiguration(SourceInterface::class)
            ->addTag('artyum_request_dto_mapper.source')
        ;

        $container->getDefinition(Mapper::class)
            ->replaceArgument(0, $mergedConfig['denormalizer'])
            ->replaceArgument(1, $mergedConfig['validation'])
            ->replaceArgument(7, $mergedConfig['default_source'])
        ;
    }
}
