<?php

use OneToMany\DataUriBundle\Command\EncodeFileCommand;
use OneToMany\DataUriBundle\Serializer\DataUriNormalizer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            // Commands
            ->set(EncodeFileCommand::class)
                ->tag('console.command')

            // Serializers
            ->set(DataUriNormalizer::class)
                ->tag('serializer.normalizer')
                ->tag('serializer.denormalizer')
    ;
};
