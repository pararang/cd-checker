<?php

namespace Selective\CdChecker;

use DirectoryIterator;
use SplFileInfo;
use Symfony\Component\Console\Input\InputInterface;

final class FileFinder
{
    /**
     * @var string
     */
    protected $basePath = '';

    /**
     * @var array
     */
    protected $exclude = [];

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Find all files.
     *
     * @param InputInterface $input
     * @param array $exclude
     *
     * @return array
     */
    public function findFiles(InputInterface $input, array $exclude = []): array
    {
        $this->exclude = $exclude;

        // Check base path ends with a slash:
        if (substr($this->basePath, -1) != '/') {
            $this->basePath .= '/';
        }

        $fileOption = $input->getOption('file');

        if ($fileOption !== null) {
            $file = $this->basePath . TypeCast::castString($fileOption);
            $files = [new SplFileInfo($file)];
        } else {
            // Get files to check:
            $files = [];
            $this->processDirectory('', $files);
        }

        return $files;
    }

    /**
     * Iterate through a directory and check all of the PHP files within it.
     *
     * @param string $path
     * @param SplFileInfo[] $worklist
     */
    protected function processDirectory(string $path = '', array &$worklist = []): void
    {
        $dir = new DirectoryIterator($this->basePath . $path);

        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }

            $itemPath = $path . $item->getFilename();

            if ($this->exclude($itemPath)) {
                continue;
            }

            if ($item->isFile() && $item->getExtension() == 'php') {
                $worklist[] = $item->getFileInfo();
            }

            if ($item->isDir()) {
                $this->processDirectory($itemPath . '/', $worklist);
            }
        }
    }

    /**
     * Check if path should be excluded.
     *
     * @param string $path
     *
     * @return bool True if path has matched pattern
     */
    protected function exclude(string $path): bool
    {
        foreach ($this->exclude as $pattern) {
            $replacements = [
                '\\,' => ',',
                '*' => '.*',
            ];

            $pattern = strtr($pattern, $replacements);

            if (preg_match("|{$pattern}|i", $path) === 1) {
                return true;
            }
        }

        return false;
    }
}
