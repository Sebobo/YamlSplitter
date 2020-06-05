<?php
declare(strict_types=1);

namespace Console;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class SplitCommand extends Command
{

    /**
     *
     */
    public function configure(): void
    {
        $this->setName('split')
            ->setDescription('Splits YAML files with Neos CMS node types into multiple individual files')
            ->setHelp('TODO: Write help')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path to the YAML file that should be split'
            )
            ->addArgument(
                'output-path',
                InputArgument::OPTIONAL,
                'The path where the output files should be written to',
                getcwd()
            )
            ->addOption(
                'package-key',
                'p',
                InputOption::VALUE_OPTIONAL,

                'The primary package key the node types should be split by'
            )
            ->addOption(
                'indentation',
                'i',
                InputOption::VALUE_OPTIONAL,

                'The indentation of the written YAML files',
                2
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The path to the YAML file that should be split',
                false
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        $helper = $this->getHelper('question');

        if (!$input->getArgument('path')) {
            $question = new Question('Please provide the path to a YAML file with Neos node types: ');
            $input->setArgument('path', $helper->ask($input, $output, $question));
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $packageKey = $input->getOption('package-key');
        $path = $input->getArgument('path');
        $outputPath = $input->getArgument('output-path');
        $dryRun = (bool)$input->getOption('dry-run');
        $indentation = (int)$input->getOption('indentation');

        $output->writeln(sprintf('<info>Selected path: %s</info>', $path));
        $output->writeln(sprintf('<info>Output path: %s</info>', $outputPath));
        if ($dryRun) {
            $output->writeln('<info>Dry run...</info>');
        }

        $yamlData = Yaml::parseFile($path);

        [$packageNodeTypes, $otherNodeTypes] = $this->splitNodeTypesByPackageKey($yamlData, $packageKey);

        if ($packageNodeTypes) {
            $output->writeln('');
            $output->writeln('<info>Splitting node types matching the given package key</info>');
            $this->writeNodeTypesToFiles($packageKey, $packageNodeTypes, $outputPath, $dryRun, $indentation, $output);
        }

        if ($otherNodeTypes) {
            $output->writeln('');
            $output->writeln('<info>Splitting node types not matching the given package key</info>');
            $this->writeNodeTypesToFiles($packageKey, $otherNodeTypes, $outputPath, $dryRun, $indentation, $output);
        }

        return Command::SUCCESS;
    }

    /**
     * @param array $nodeTypes
     * @param string $packageKey
     * @return array[]
     */
    protected function splitNodeTypesByPackageKey(array $nodeTypes, string $packageKey): array
    {
        $packageNodeTypes = [];
        $otherNodeTypes = [];

        foreach ($nodeTypes as $nodeType => $nodeTypeConfig) {
            if ($packageKey && strpos($nodeType, $packageKey) === 0) {
                $packageNodeTypes[$nodeType] = $nodeTypeConfig;
            } else {
                $otherNodeTypes[$nodeType] = $nodeTypeConfig;
            }
        }
        return [$packageNodeTypes, $otherNodeTypes];
    }

    /**
     * @param string $packageKey
     * @param array $nodeTypes
     * @param string $outputPath
     * @param bool $dryRun
     * @param int $indentation
     * @param OutputInterface $output
     */
    protected function writeNodeTypesToFiles(
        string $packageKey,
        array $nodeTypes,
        string $outputPath,
        bool $dryRun,
        int $indentation,
        OutputInterface $output
    ): void {
        foreach ($nodeTypes as $nodeType => $nodeTypeConfig) {
            $filename = $this->generateFileNameFromNodeType($packageKey, $nodeType);
            if ($dryRun) {
                $output->writeln(sprintf(
                    'Would write node type data for <bold>%s</bold> to <bold>%s</bold>',
                    $nodeType,
                    $filename
                ));
            } else {
                $output->writeln(sprintf(
                    'Writing node type data for <bold>%s</bold> to <bold>%s</bold>',
                    $nodeType,
                    $filename
                ));

                $result = $this->writeNodeTypeToYaml($filename, $outputPath, $nodeType, $nodeTypeConfig, $indentation);

                if (!$result) {
                    $output->writeln(sprintf('<error>Could not write file %s</error>', $filename));
                }
            }
        }
    }

    /**
     * @param string $packageKey
     * @param string $nodeType
     * @return string
     */
    protected function generateFileNameFromNodeType(string $packageKey, string $nodeType): string
    {
        [$nodeTypePackageKey, $nodeTypeName] = explode(':', $nodeType);

        $isDocument = strpos($nodeTypeName, 'Document') !== false;
        $isContent = strpos($nodeTypeName, 'Content') !== false;
        $isMixin = strpos($nodeTypeName, 'Mixin') !== false;

        $prefix = 'NodeTypes.';

        if ($packageKey !== $nodeTypePackageKey) {
            $prefix .= 'Override.';
        }

        if ($isDocument) {
            $prefix .= 'Document.';
        } elseif ($isContent) {
            $prefix .= 'Content.';
        } elseif ($isMixin) {
            $prefix .= 'Mixin.';
        }
        return $prefix . $nodeTypeName . '.yaml';
    }

    /**
     * @param string $filename
     * @param string $outputPath
     * @param string $nodeType
     * @param array $nodeTypeConfig
     * @param int $indentation
     * @return bool
     */
    protected function writeNodeTypeToYaml(
        string $filename,
        string $outputPath,
        string $nodeType,
        array $nodeTypeConfig,
        int $indentation
    ): bool {
        $finalPath = $outputPath . '/' . $filename;

        if (file_exists($finalPath)) {
            return false;
        }

        $yaml = Yaml::dump([
            $nodeType => $nodeTypeConfig
        ], 99, $indentation);
        return file_put_contents($finalPath, $yaml) !== false;
    }
}
