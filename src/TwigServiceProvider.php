<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenSourceRefinery\VicusProvider\TwigServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Vicus\Api\BootableProviderInterface;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bridge\Twig\Extension\HttpKernelExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Form\TwigRenderer;

/**
 * Twig integration for Vicus.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['twig.options'] = array();
        $container['twig.form.templates'] = array('form_div_layout.html.twig');
        $container['twig.path'] = array();
        $container['twig.templates'] = array();

        // $container->extend('
        $container['twig'] = function ($container) {
            $container['twig.options'] = array_replace(
                array(
                    'charset' => $container['charset'],
                    'debug' => $container['debug'],
                    'strict_variables' => $container['debug'],
                ), $container['twig.options']
            );

            $twig = new \Twig_Environment($container['twig.loader'], $container['twig.options']);

            if ($container['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            return $twig;
        };

        $container['twig.loader.filesystem'] = function ($container) {
            return new \Twig_Loader_Filesystem($container['twig.path']);
        };

        $container['twig.loader.array'] = function ($container) {
            return new \Twig_Loader_Array($container['twig.templates']);
        };

        $container['twig.loader'] = function ($container) {
            return new \Twig_Loader_Chain(array(
                $container['twig.loader.array'],
                $container['twig.loader.filesystem'],
            ));
        };
    }

    public function boot(Container $container)
    {
        foreach ($container['template_variables'] as $key => $value) {
            if (isset($container[$key])) {
                $paramValue = $container[$key];
                $container['twig']->addGlobal($key, $paramValue);
            }
        }
    }
}
