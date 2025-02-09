<?php

namespace Orchestra\Lumenate;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeCommand extends Command
{
    use Concerns\PublishFiles;

    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('make')
                ->setDescription('Make Lumen skeleton into the current project.')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite any existing files.');
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->publishFiles(
            new Filesystem(), $this->getInstallationPaths($filesystem), $input->getOption('force')
        );
    }

    /**
     * Get installation paths from lumen.json file.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     *
     * @return array
     */
    protected function getInstallationPaths(Filesystem $filesystem): array
    {
        $basePath = \getcwd();
        $vendorPath = "{$basePath}/vendor/orchestra/lumen/skeleton";
        $paths = [];

        if (! $filesystem->isFile($schema = "{$basePath}/lumen.json")) {
            $schema = "{$vendorPath}/lumen.json";
        }

        $paths = \json_decode($filesystem->get($schema), true);

        if (\is_null($paths)) {
            return ["{$vendorPath}" => "{$basePath}/lumen"];
        }

        $paths = \array_map(static function ($path) use ($vendorPath) {
            return "{$vendorPath}/{$path}";
        }, \array_flip($paths));

        return \array_map(static function ($path) use ($basePath) {
            return "{$basePath}/{$path}";
        }, \array_flip($paths));
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @param  int|string|null  $verbosity
     *
     * @return void
     */
    public function error(string $string, $verbosity = null): void
    {
        $this->output->writeln("<error>{$string}</error>", $verbosity);
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @param  string  $style
     *
     * @return void
     */
    public function line(string $string, ?string $style = null): void
    {
        $styled = $style ? "<{$style}>{$string}</{$style}>" : $string;

        $this->output->writeln($styled);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     *
     * @return string
     */
    protected function replaceNamespace(string $stub): string
    {
        return $stub;
    }
}
