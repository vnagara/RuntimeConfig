<?php

/*
 * eJoom.com
 * This source file is subject to the new BSD license.
 */

namespace RuntimeConfig;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;

/**
 * Runtime config
 * Setup user defined php ini configurations
 *
 * @author duke
 */
class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    protected static $options;

    public function init(ModuleManager $moduleManager)
    {
        $moduleManager->getEventManager()->attach('loadModules.post', array($this, 'setOptions'));
    }

    public function setOptions(ModuleEvent $e)
    {
        $config = $e->getConfigListener()->getMergedConfig(false);
        static::$options = $config['runtime_config'];
    }

    /**
     * executes on boostrap
     * @param MvcEvent $e
     * @return null
     */
    public function onBootstrap(MvcEvent $e)
    {
        foreach (static::$options as $option => $value) {
            ini_set($option, $value);
        }

        // There don't exist ini option to set it
        if (function_exists('mb_regex_encoding')) {
            mb_regex_encoding('utf-8');
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
