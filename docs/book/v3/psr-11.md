# PSR-11 Support

laminas-di supports and implements [PSR-11 ContainerInterface](https://github.com/php-fig/container)
starting in version 3. It supports any implementation to obtain instances for
resolved dependencies.

laminas-di ships with a very basic implementation of the container interface which
only uses the injector to create instances and always shares services it
creates. We suggest you replace it with another implementation such as
[laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/) for
more flexibility.
