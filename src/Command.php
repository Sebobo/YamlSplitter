<?php
declare(strict_types=1);

namespace Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{

    /**
     * Command constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function init(InputInterface $input, OutputInterface $output): void
    {
        $outputStyle = new OutputFormatterStyle(null, null, ['bold']);
        $output->getFormatter()->setStyle('bold', $outputStyle);

        $output->writeln([
            '',
            '==============================================================',
            '====**** Neos CMS node type YAML Splitter Console App ****====',
            '==============================================================',
            '',
        ]);
    }
}
