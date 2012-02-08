Embedded Composer
=================

Sample console application embedding [Composer](http://github.com/composer/composer)
to manage its runtime dependencies.


Usage
-----

    git clone git://github.com/dflydev/dflydev-embedded-composer-application.git
    cd dflydev-embedded-composer-application/
    wget -nc http://getcomposer.org/composer.phar
    php composer.phar install
    cd sample
    ../bin/embedded-composer install

This showcases the ability to have an embedded Composer install dependencies in
a directory against the dependencies of the calling application. This would be
useful in cases where a PHP application may be distrubted as a `.phar` but still
need to be extended by plugins.


License
-------

This library is licensed under the New BSD License - see the LICENSE file for details.


Community
---------

If you have questions or want to help out, join us in the
[#dflydev](irc://irc.freenode.net/#dflydev) channel on irc.freenode.net.
