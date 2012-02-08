The Problem
-----------

Composer installs things locally that are listed in the installed repository.


How to Reproduce
----------------

    git clone git://github.com/dflydev/dflydev-embedded-composer-application.git
    cd dflydev-embedded-composer-application/
    wget -nc http://getcomposer.org/composer.phar
    php composer.phar install
    cd sample
    ../bin/embedded-composer install


Expected Result
---------------

The expectation is that nothing would actually be installed in `sample/` because
`symfony/finder` is included in `composer` itself and already installed by the
`embedded-composer` application.

The `composer.json` in `sample/` looks like this:

```json
{
    "name": "dflydev/embedded-composer-application-broken",
    "require": {
        "php": ">=5.3.2",
        "symfony/finder": "*",
        "dflydev/embedded-composer-application": "*"
    },
    "repositories": {
        "sculpin": {
            "vcs": {
                "url": "git://github.com/dflydev/dflydev-embedded-composer-application.git"
            }
        }
    }
}
```

The list of installed packages can be verified by the output of the `ComposerInstallCommand`:

    Including /home/altern8/dflydev-embedded-composer-application/vendor/.composer/installed.json in installed repository, contains:
     * symfony/process:2.1.0-dev
     * symfony/finder:2.1.0-dev
     * symfony/console:2.1.0-dev
     * composer/composer:master-dev


Actual Result
-------------

    Installing dependencies                                                         
      - Package dflydev/embedded-composer-application (master-dev)
        Downloading                                                                 
        Unpacking archive
        Cleaning up

      - Package symfony/finder (2.1.0-dev)
        Downloading                                                                 
        Unpacking archive
        Cleaning up

      - Package composer/composer (master-dev)
        Downloading                                                                 
        Unpacking archive
        Cleaning up


**Note** The fact that the Embedded Composer application itself is included
again is something for which I have not yet found an elegant solution as
the whole "what version is installed" thing is not trivial. That is outside
the scope of this document.
