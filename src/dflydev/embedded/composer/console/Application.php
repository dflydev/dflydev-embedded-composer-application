<?php

/*
 * This file is a part of Embedded Composer.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace dflydev\embedded\composer\console;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    /**
     * Class Loader
     * @var ClassLoader
     */
    private $classLoader;
    
    /**
     * Path to internal vendor root if exists
     * @var string
     */
    private $internalVendorRoot;

    /**
     * Enable the internally installed repository (composer)
     * @var bool
     */
    private $internallyInstalledRepositoryEnabled = false;

    /**
     * Default value for project root.
     * @var unknown_type
     */
    const DEFAULT_PROJECT_ROOT = '.';
    
    public function __construct(ClassLoader $classLoader)
    {
        parent::__construct('Embedded Composer', 'master-dev');
        $this->getDefinition()->addOption(new InputOption('project-root', null, InputOption::VALUE_REQUIRED, 'Project root.', self::DEFAULT_PROJECT_ROOT));
        $this->classLoader = $classLoader;
        $obj = new \ReflectionClass($this->classLoader);
        $this->internalVendorRoot = dirname(dirname($obj->getFileName()));
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $projectRoot = realpath($input->getParameterOption('--project-root') ?: self::DEFAULT_PROJECT_ROOT);
        if ($autoloadNamespacesFile = realpath($projectRoot.'/vendor/.composer/autoload_namespaces.php')) {
            if ($this->internalVendorRoot != dirname(dirname($autoloadNamespacesFile))) {
                // We have an autoload file that is *not* the same as the
                // autoload that bootstrapped this application.
                $map = require $autoloadNamespacesFile;
                foreach ($map as $namespace => $path) {
                    $this->classLoader->add($namespace, $path);
                }
            }
        }
        if (strpos($this->internalVendorRoot, $projectRoot)===false) {
            // If our vendor root does not contain our project root then we
            // can assume that we should enable the internally installed
            // repository.
            $this->internallyInstalledRepositoryEnabled = true;
        }
        $this->add(new command\ComposerInstallCommand());
        $this->add(new command\ComposerUpdateCommand());
        return parent::doRun($input, $output);
    }

    /**
     * Class loader
     * @return \Composer\Autoload\ClassLoader
     */
    public function classLoader()
    {
        return $this->classLoader;
    }

    /**
     * Internal vendor root
     *
     * This is the vendor root for the class loader used to bootstrap
     * the application. In some cases  this may be embedded in a phar,
     * in a development directory, or the vendor for the package using
     * Embedded Composer.
     * @return string
     */
    public function internalVendorRoot()
    {
        return $this->internalVendorRoot;
    }

    /**
     * Whether or not Composer should use the internally installed repsoitory
     *
     * This is done when the Embedded Composer instance running is located in
     * another directory or is embedded in a phar. It instructs Composer to
     * include the installed repository from the internal vendor root.
     */
    public function internallyInstalledRepositoryEnabled()
    {
        return $this->internallyInstalledRepositoryEnabled;
    }
}
