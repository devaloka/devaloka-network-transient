<?php
/**
 * Transient Provider
 *
 * @author Whizark <devaloka@whizark.com>
 * @see http://whizark.com
 * @copyright Copyright (C) 2015 Whizark.
 * @license MIT
 */

namespace Devaloka\Transient\Provider;

use Pimple\Container;
use Devaloka\Devaloka;
use Devaloka\Component\DependencyInjection\ContainerInterface;
use Devaloka\Provider\ServiceProviderInterface;

/**
 * Class TransientProvider
 *
 * @package Devaloka\Transient\Provider
 */
class TransientProvider implements ServiceProviderInterface
{
    public function register(Devaloka $devaloka, ContainerInterface $container)
    {
        $container->add('transient.class', 'Devaloka\\Transient\\Transient');
        $container->add(
            'transient',
            function (Container $container) {
                return new $container['transient.class']();
            }
        );
    }
}
