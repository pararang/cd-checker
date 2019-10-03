<?php

namespace Selective\CdChecker;

use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to check a directory of PHP files.
 */
final class CdCheckerCommand extends Command
{
    /**
     * @var string
     */
    protected $basePath = './';

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $warnings = [];

    /**
     * @var array
     */
    protected $exclude = [];

    /**
     * @var string|null 'json'
     */
    protected $format = null;

    /**
     * @var OutputInterface
     */
    protected $output;

    /** @var int */
    protected $passed = 0;

    /**
     * @var array
     */
    private $dependencies = [];

    /**
     * Configure the console command, add options, etc.
     */
    protected function configure()
    {
        $this
            ->setName('check')
            ->setDescription('Check PHP files within a directory for appropriate use of Docblocks.')
            ->addOption(
                'exclude',
                'x',
                InputOption::VALUE_REQUIRED,
                'Files and directories to exclude. You can use exclude patterns ex. tests/*',
                null
            )
            ->addOption('directory', 'd', InputOption::VALUE_REQUIRED, 'Directory to scan.', './')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Single file to scan.', null)
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'json = Output JSON instead of a log.')
            ->addOption('files-per-line', 'l', InputOption::VALUE_REQUIRED, 'Number of files per line in progress', 50)
            ->addOption(
                'fail-on-warnings',
                'w',
                InputOption::VALUE_NONE,
                'Consider the check failed if any warnings are produced.'
            )
            ->addOption('info-only', 'i', InputOption::VALUE_NONE, 'Information-only mode, just show summary.');
    }

    /**
     * Execute the actual checker.
     *
     * @param InputInterface $input Input
     * @param OutputInterface $output   Output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Process options
        $this->processOptions($input, $output);

        $failOnWarnings = $input->getOption('fail-on-warnings');

        $startTime = microtime(true);

        if ($this->format !== 'json') {
            $this->printApplicationTitle($output);
        }

        $finder = new FileFinder($this->basePath);
        $files = $finder->findFiles($input, $this->exclude);
        $totalFiles = $this->checkFiles($input, $files);

        // Output JSON if requested
        if ($this->format === 'json') {
            $this->output->writeln(json_encode(array_merge($this->errors, $this->warnings), JSON_PRETTY_PRINT) ?: '');
        } else {
            // Default output
            $this->printLogResult($input, $startTime, $totalFiles);
        }

        return count($this->errors) || ($failOnWarnings && count($this->warnings)) ? 1 : 0;
    }

    /**
     * Check files.
     *
     * @param InputInterface $input Input
     * @param SplFileInfo[] $files  Files
     *
     * @return int Total files
     */
    protected function checkFiles(InputInterface $input, array $files): int
    {
        $filesPerLine = TypeCast::castInt($input->getOption('files-per-line'));
        $totalFiles = count($files);

        /** @var SplFileInfo[] $chunkFiles */
        $chunkFiles = array_chunk($files, $filesPerLine);

        $this->processFiles($chunkFiles, $filesPerLine, $totalFiles);

        return $totalFiles;
    }

    /**
     * Process files.
     *
     * @param SplFileInfo[] $files  Files
     * @param int $filesPerLine Total fFiles per line
     * @param int $totalFiles   Total files
     *
     * @return int Processed
     */
    protected function processFiles(array $files, int $filesPerLine, int $totalFiles): int
    {
        $fileCountLength = strlen((string)$totalFiles);
        $processed = 0;

        while (!empty($files)) {
            /** @var SplFileInfo[] $chunk */
            $chunk = array_shift($files);
            $chunkFiles = count($chunk);

            $processed += $this->processJunks($chunk);

            if ($this->format !== 'json') {
                $this->output->write(str_pad('', $filesPerLine - $chunkFiles));
                $this->output->writeln('  ' . str_pad(
                    (string)$processed,
                    $fileCountLength,
                    ' ',
                    STR_PAD_LEFT
                ) . '/' . $totalFiles . ' (' . floor((100 / $totalFiles) * $processed) . '%)');
            }
        }

        return $totalFiles;
    }

    /**
     * Process chunk files.
     *
     * @param SplFileInfo[] $chunk  Chunk of file
     *
     * @return int Processed
     */
    protected function processJunks(array $chunk): int
    {
        $processed = 0;

        while (!empty($chunk)) {
            $processed++;
            $file = array_shift($chunk);

            if (!$file) {
                continue;
            }

            [$errors, $warnings] = $this->processFile($file);

            if ($this->format !== 'json') {
                $this->printLegend($errors, $warnings);
            }
        }

        return $processed;
    }

    /**
     * Process options.
     *
     * @param InputInterface $input     Input
     * @param OutputInterface $output   Output
     *
     * @return void
     */
    protected function processOptions(InputInterface $input, OutputInterface $output): void
    {
        $exclude = TypeCast::castString($input->getOption('exclude'));
        $format = $input->getOption('format');
        $this->format = is_string($format) ? $format : null;
        $basePath = $input->getOption('directory');
        $this->basePath = is_string($basePath) ? $basePath : './';

        // Add slash to the path
        if (substr($this->basePath, -1) !== '/') {
            $this->basePath .= '/';
        }

        $this->verbose = $output->isVerbose();
        $this->output = $output;
        //$this->skipClasses = (bool)$input->getOption('skip-classes');

        // Set up excludes:
        if (!empty($exclude)) {
            $this->exclude = array_map('trim', explode(',', $exclude));
        }
    }

    /**
     * Print application name.
     *
     * @param OutputInterface $output   Output
     *
     * @return void
     */
    protected function printApplicationTitle(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('Circular Dependency Checker');
        $output->writeln('');
    }

    /**
     * Print legend.
     *
     * @param bool $errors      Errors
     * @param bool $warnings    Warnings
     *
     * @return void
     */
    protected function printLegend(bool $errors, bool $warnings): void
    {
        if ($errors) {
            $this->output->write('<fg=red>F</>');
        } elseif ($warnings) {
            $this->output->write('<fg=yellow>W</>');
        } else {
            $this->output->write('<info>.</info>');
        }
    }

    /**
     * Print verbose result.
     *
     * @param InputInterface $input Input
     * @param float $startTime  Start time
     * @param int $totalFiles   Total files
     *
     * @return void
     */
    protected function printLogResult(InputInterface $input, float $startTime, int $totalFiles): void
    {
        $time = round(microtime(true) - $startTime, 2);

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln('Checked ' . number_format($totalFiles) . ' files in ' . $time . ' seconds.');
        $this->output->write('<info>' . number_format($this->passed) . ' Passed</info>');
        $this->output->write(' / <fg=red>' . number_format(count($this->errors)) . ' Errors</>');
        $this->output->write(' / <fg=yellow>' . number_format(count($this->warnings)) . ' Warnings</>');
        $this->output->writeln('');

        if (!empty($this->errors) && !$input->getOption('info-only')) {
            $this->printErrors();
        }

        if (!empty($this->warnings) && !$input->getOption('info-only')) {
            $this->printWarnings();
        }

        $this->output->writeln('');
    }

    /**
     * Print errors.
     *
     * @return void
     */
    protected function printErrors(): void
    {
        $this->output->writeln('');
        $this->output->writeln('');

        foreach ($this->errors as $error) {
            $this->output->write('<fg=red>ERROR   </> ' . $error . ' - ');
            $this->output->write('<info>' . $error . '</info>');
            $this->output->writeln('');
        }
    }

    /**
     * Print warnings.
     *
     * @return void
     */
    protected function printWarnings(): void
    {
        foreach ($this->warnings as $error) {
            $this->output->write('<fg=yellow>WARNING </> ');
            $this->output->write('<info>' . $error . '</info>');
            $this->output->writeln('');
        }
    }

    /**
     * Check a specific PHP file for errors.
     *
     * @param SplFileInfo $file File
     *
     * @return array
     */
    protected function processFile(SplFileInfo $file): array
    {
        $realPath = $file->getRealPath();
        if ($this->output->isDebug()) {
            $this->output->writeln('Process file: ' . $realPath);
        }

        if ($realPath === false) {
            throw new RuntimeException(sprintf('File not found: %s', $file));
        }

        $check = new DependencyFinder();

        $dependencies = [];
        $dependencies = $check->processFile($file, $dependencies);

        $cdFinder = new CircularDependencyFinder();
        $cdFinder->processDependencies($dependencies);

        foreach ($cdFinder->getErrors() as $errorItem) {
            if ($this->output->isDebug()) {
                $this->output->writeln('Error in line: ' . var_export($errorItem, true));
            }
            $this->errors[] = $errorItem;
        }

        foreach ($cdFinder->getWarnings() as $warningEntry) {
            if ($this->output->isDebug()) {
                $this->output->writeln('Warning in line: ' . var_export($warningEntry, true));
            }
            $this->warnings[] = $warningEntry;
        }

        $hasErrors = !empty($errorItem);
        $hasWarnings = !empty($warningEntry);

        if (!$hasErrors) {
            $this->passed++;
        }

        return [$hasErrors, $hasWarnings];
    }
}
