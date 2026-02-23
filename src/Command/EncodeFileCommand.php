<?php

namespace OneToMany\DataUriBundle\Command;

use OneToMany\DataUri\DataDecoder;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'data-uri:encode-file',
    description: 'Encodes a file as a data URL',
)]
final readonly class EncodeFileCommand
{
    public function __invoke()
    {
    }

    public function __construct(
        OutputInterface $output,
        #[Argument('Path to the file to encode')] string $path,
    ): int
    {
        $file = new DataDecoder()->decode($path);

        $output->write($file->toDataUri(), false, OutputInterface::OUTPUT_PLAIN);

        return Command::SUCCESS;
    }
}
