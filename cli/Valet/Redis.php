<?php

namespace Valet;

class Redis
{
    var $brew;
    var $cli;
    var $files;
    var $configuration;
    var $site;
    const REDIS_CONF = '/usr/local/etc/redis.conf';

    /**
     * Create a new Nginx instance.
     *
     * @param  Brew $brew
     * @param  CommandLine $cli
     * @param  Filesystem $files
     * @param  Configuration $configuration
     * @param  Site $site
     */
    function __construct(Brew $brew, CommandLine $cli, Filesystem $files,
                         Configuration $configuration, Site $site)
    {
        $this->cli = $cli;
        $this->brew = $brew;
        $this->site = $site;
        $this->files = $files;
        $this->configuration = $configuration;
    }

    /**
     * Install the service.
     *
     * @return void
     */
    function install()
    {
        if (!$this->brew->installed('redis')) {
            $this->brew->installOrFail('redis');
            $this->cli->quietly('sudo brew services stop redis');
        }

        $this->installConfiguration();
        $this->restart();
    }

    /**
     * Install the configuration file.
     *
     * @return void
     */
    function installConfiguration()
    {
        info('Installing redis configuration...');
        $this->files->copy(__DIR__.'/../stubs/redis.conf', static::REDIS_CONF);
    }

    /**
     * Restart the service.
     *
     * @return void
     */
    function restart()
    {
        info('Restarting redis...');
        $this->cli->quietlyAsUser('brew services restart redis');
    }

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    function stop()
    {
        info('Stopping redis....');
        $this->cli->quietly('sudo brew services stop redis');
        $this->cli->quietlyAsUser('brew services stop redis');
    }

    /**
     * Prepare Redis for uninstallation.
     *
     * @return void
     */
    function uninstall()
    {
        $this->stop();
    }
}
