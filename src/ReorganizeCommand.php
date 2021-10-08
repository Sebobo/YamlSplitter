<?php
declare(strict_types=1);

namespace Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ReorganizeCommand extends Command
{

    public function configure(): void
    {
        $this->setName('reorganize')
            ->setDescription('Reorganizes the nodetype file in a Configuration folder into a subfolder based structure for Neos 7.2+')
            ->setHelp('Don\'t panic')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path to the YAML files that should be reorganized'
            )
            ->addArgument(
                'output-path',
                InputArgument::OPTIONAL,
                'The path where the output files should be written to',
                getcwd()
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Just simulate the split'
            )
            ->addOption(
                'copy',
                'c',
                InputOption::VALUE_NONE,
                'Copy files instead of moving them'
            );
    }

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        $helper = $this->getHelper('question');

        if (!$input->getArgument('path')) {
            $question = new Question('Please provide the path to a YAML file with Neos node types: ');
            $input->setArgument('path', $helper->ask($input, $output, $question));
        }
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $path = $input->getArgument('path');
        $outputPath = $input->getArgument('output-path');
        $dryRun = (bool)$input->getOption('dry-run');
        $copy = (bool)$input->getOption('copy');

        $output->writeln(sprintf('<info>Selected path: %s</info>', $path));
        $output->writeln(sprintf('<info>Output path: %s</info>', $outputPath));
        if ($dryRun) {
            $output->writeln('<info>Dry run...</info>');
        }

        $path = rtrim($path, '/');
        $outputPath = rtrim($outputPath, '/');

        $nodeTypeFiles = glob($path . '/NodeTypes.*.yaml');

        $this->reorganizeNodeTypeFiles($nodeTypeFiles, $outputPath, $dryRun, $copy, $output);

        return Command::SUCCESS;
    }

    protected function reorganizeNodeTypeFiles(array $nodeTypeFiles, string $outputPath, bool $dryRun, bool $copy, OutputInterface $output): void
    {
        foreach ($nodeTypeFiles as $filepath) {
            $filename = basename($filepath, '.yaml');
            $parts = explode('.', $filename);

            // Remove 'NodeTypes' from the beginning
            array_shift($parts);

            // Build target path where the file will be moved to
            $targetPath = $outputPath . '/' . implode('/', $parts) . '.yaml';

            if (file_exists(getcwd() . '/' . $targetPath)) {
                $output->writeln(sprintf(
                    'File <bold>%s</bold> already exists, skipping',
                    $targetPath
                ));
                continue;
            }

            if ($dryRun) {
                $output->writeln(sprintf(
                    'Would move <bold>%s</bold> to <bold>%s</bold>',
                    $filepath,
                    $targetPath
                ));
            } else {
                $output->writeln(sprintf(
                    'Moving <bold>%s</bold> to <bold>%s</bold>',
                    $filepath,
                    $targetPath
                ));

                $finalDirectory = dirname($targetPath);

                // Generate folders if necessary
                /** @noinspection MkdirRaceConditionInspection */
                if (!is_dir($finalDirectory) && !mkdir($finalDirectory, 0777, true)) {
                    $output->writeln(sprintf(
                        'Could not create directory <bold>%s</bold>',
                        $finalDirectory
                    ));
                    continue;
                }

                if ($copy) {
                    copy($filepath, $targetPath);
                } else {
                    rename($filepath, $targetPath);
                }
            }
        }
    }
}
