<?php

use Behat\Behat\ApplicationFactory;
use PhpSpec\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CodeCoverageGenerator
 */
class CodeCoverageGenerator extends Command
{
    protected function configure()
    {
        $this
            ->setName('code-coverage:generate')
            ->setDescription('Generates a code coverage report');
    }

    /**
     * Builds the code coverage clove report file
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = new PHP_CodeCoverage_Filter();
        $filter->addDirectoryToBlacklist(realpath(__DIR__.'/../console'));
        $filter->addDirectoryToBlacklist(realpath(__DIR__.'/../features'));
        $filter->addDirectoryToBlacklist(realpath(__DIR__.'/../spec'));
        $filter->addDirectoryToBlacklist(realpath(__DIR__.'/../vendor'));

        $coverage = new PHP_CodeCoverage(null, $filter);

        $coverage->start('<tests>');
        $input = new ArgvInput(['phpspec', 'run', '--format=pretty']);
        $app = new Application(null);
        $app->setAutoExit(false);
        $exit = $app->run($input, new ConsoleOutput());
        if ($exit !== 0) {
            exit($exit);
        }

        define('BEHAT_BIN_PATH', realpath(__DIR__.'/../bin/behat'));
        $input = new ArgvInput(['behat']);
        $factory = new ApplicationFactory();
        $app = $factory->createApplication();
        $app->setAutoExit(false);
        $exit = $app->run($input, new ConsoleOutput());
        if ($exit !== 0) {
            exit($exit);
        }

        $coverage->stop();

        $writer = new PHP_CodeCoverage_Report_Clover;
        $writer->process($coverage, realpath(__DIR__.'/..').'/clover.xml');

        $writer = new PHP_CodeCoverage_Report_HTML();
        $writer->process($coverage, realpath(__DIR__.'/..').'/code-coverage');
    }
}
