<?php

namespace OneToMany\DataUriBundle;

use OneToMany\DataUriBundle\Command\EncodeFileCommand;
use OneToMany\DataUriBundle\Serializer\TemporaryFileNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class DataUriBundle extends AbstractBundle
{
    protected string $extensionAlias = 'onetomany_datauri';

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface
     *
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container
            ->services()
                // Commands
                ->set(EncodeFileCommand::class)
                    ->tag('console.command')

                // Normalizers
                ->set(TemporaryFileNormalizer::class)
                    ->tag('serializer.denormalizer')
                    ->tag('serializer.normalizer')
        ;
    }
}
