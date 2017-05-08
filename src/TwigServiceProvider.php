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
            // $twig->addGlobal('container', $container);
            // $twig->setLoader(new \Twig_Loader_String());

            if ($container['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            if (class_exists('Symfony\Bridge\Twig\Extension\RoutingExtension')) {
                if (isset($container['url_generator'])) {
                    $twig->addExtension(new RoutingExtension($container['url_generator']));
                }

                if (isset($container['translator'])) {
                    $twig->addExtension(new TranslationExtension($container['translator']));
                }

                if (isset($container['security.authorization_checker'])) {
                    $twig->addExtension(new SecurityExtension($container['security.authorization_checker']));
                }

                if (isset($container['fragment.handler'])) {
                    $container['fragment.renderer.hinclude']->setTemplating($twig);

                    $twig->addExtension(new HttpKernelExtension($container['fragment.handler']));
                }

                if (isset($container['form.factory'])) {
                    $container['twig.form.engine'] = function ($container) {
                        return new TwigRendererEngine($container['twig.form.templates']);
                    };

                    $container['twig.form.renderer'] = function ($container) {
                        return new TwigRenderer($container['twig.form.engine'], $container['form.csrf_provider']);
                    };

                    $twig->addExtension(new FormExtension($container['twig.form.renderer']));

                    // add loader for Symfony built-in form templates
                    $reflected = new \ReflectionClass('Symfony\Bridge\Twig\Extension\FormExtension');
                    $path = dirname($reflected->getFileName()).'/../Resources/views/Form';
                    $container['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));
                }
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
        foreach($container['template_variables'] as $key => $value)
		{

			if(isset($container[$key])){
				$paramValue = $container[$key];
				$container['twig']->addGlobal($key, $paramValue);
			}

		}
    }
}
