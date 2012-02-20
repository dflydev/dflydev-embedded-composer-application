<?php

/*
 * This file is a part of Embedded Composer
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dflydev\embedded\composer\console\command;

use Composer\Command\InstallCommand as BaseInstallCommand;
use Composer\Factory;
use Composer\IO\ConsoleIO;
use Composer\Json\JsonFile;
use Composer\Repository\FilesystemRepository;
use Composer\Script\EventDispatcher;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerInstallCommand extends BaseInstallCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output, $this->getApplication()->getHelperSet());
        $composer = Factory::create($io);
        $eventDispatcher = new EventDispatcher($composer, $io);
        if ($this->getApplication()->internallyInstalledRepositoryEnabled()) {
            $internalRepositoryFile = $this->getApplication()->internalVendorRoot().'/.composer/installed.json';
            $filesystemRepository = new FilesystemRepository(new JsonFile($internalRepositoryFile));
            $io->write('Including '.$internalRepositoryFile.' in installed repository, contains:');
            foreach ($filesystemRepository->getPackages() as $package) {
                $io->write(' * '.$package->getName() . ':' . $package->getPrettyVersion());
            }
        } else {
            $filesystemRepository = null;
        }
        return $this->install(
            $io,
            $composer,
            $eventDispatcher,
            (Boolean)$input->getOption('prefer-source'),
            (Boolean)$input->getOption('dry-run'),
            (Boolean)$input->getOption('verbose'),
            (Boolean)$input->getOption('no-install-recommends'),
            (Boolean)$input->getOption('install-suggests'),
            false,
            $filesystemRepository
        );
    }
}
