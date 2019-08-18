<?php

namespace Selective\CdChecker\Test;

use Exception;
use PHPUnit\Framework\TestCase;
use Selective\CdChecker\CdCheckerApplication;
use Selective\CdChecker\CdCheckerCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CheckerCommandTest.
 */
class CheckerCommandTest extends TestCase
{
    /**
     * Run command.
     *
     * @param ArrayInput $input Input
     *
     * @throws Exception
     *
     * @return string
     */
    protected function runCommand(ArrayInput $input): string
    {
        $application = new CdCheckerApplication('Checker', '@package_version@');
        $application->setAutoExit(false);

        $command = new CdCheckerCommand();
        $application->add($command);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        return $content;
    }

    /**
     * Test instance.
     *
     * @return void
     */
    public function testInstance()
    {
        $instance = new CdCheckerCommand();
        $this->assertInstanceOf(CdCheckerCommand::class, $instance);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testFileNotFound()
    {
        $input = new ArrayInput([
            '-d' => __DIR__,
            '-f' => __DIR__ . '/TestClass/nada.php',
        ]);

        $output = $this->runCommand($input);

        $this->assertContains('File not found:', $output);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testFile()
    {
        $input = new ArrayInput([
            '-d' => __DIR__,
            '-f' => 'TestClass/ClassA.php',
        ]);

        $output = $this->runCommand($input);

        $this->assertContains('Checked 1 files', $output);
        $this->assertContains('0 Passed / 2 Errors / 0 Warnings', $output);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testDirectory()
    {
        $input = new ArrayInput([
            '-d' => realpath(__DIR__ . '/TestClass/'),
        ]);

        $output = $this->runCommand($input);

        $this->assertContains('Checked 5 files', $output);
        $this->assertContains('1 Passed / 6 Errors / 0 Warnings', $output);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testExclude(): void
    {
        $input = new ArrayInput([
            '-d' => realpath(__DIR__ . '/TestClass/'),
            '-x' => 'Excluded/*',
        ]);

        $output = $this->runCommand($input);

        $this->assertContains('Checked 4 files', $output);
        $this->assertContains('1 Passed / 5 Errors / 0 Warnings', $output);
        $this->assertContains('Circular reference detected', $output);
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testJson(): void
    {
        $input = new ArrayInput([
            '--directory' => realpath(__DIR__ . '/TestClass/'),
            '--format' => 'json',
        ]);

        $output = $this->runCommand($input);

        $this->assertContains('Circular reference detected.', $output);

        $data = json_decode($output, true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }
}
