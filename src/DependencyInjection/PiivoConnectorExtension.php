<?php

namespace Piivo\Bundle\ConnectorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Routing\Loader\YamlFileLoader;

/**
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 */
class PiivoConnectorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        var_dump('POUIC');
        $routingLoader = new YamlFileLoader(new FileLocator(__DIR__ . '/../Resources/config/routing'));
        $routingLoader->load('test.yml');


    }
}
