<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is ssubject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vicus\Tests\Provider;
use Pimple\Container;
use Vicus\Application;
use OpenSourceRefinery\VicusProvider\TwigServiceProvider\TwigServiceProvider;
// use Silex\Provider\HttpFragmentServiceProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * TwigProvider test cases.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class TwigServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    protected function config(){
        return [
            'config.path' => 'tests/resources',
        ];
    }
    public function testRegisterAndRender()
    {
        $config = $this->config();
        $container = new Container();
        $app = new Application($container, $config);

        $app->register(new TwigServiceProvider(), array(
            'twig.templates' => array('hello' => 'Hello {{ name }}!'),
        ));

        // $app->get('/hello/{name}', function ($name) use ($app) {
        //     return $app['twig']->render('hello', array('name' => $name));
        // });
        $name = 'john';

        $content = $container['twig']->render('hello', array('name' => $name));

        // $request = Request::create('/hello/john');
        // $response = $app->handle($request);
        $this->assertEquals('Hello john!', $content);
    }

    // public function testRenderFunction()
    // {
    //     if (!class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
    //         $this->markTestSkipped();
    //     }
    //
    //     $config = $this->config();
    //     $container = new Container();
    //     $app = new Application($container, $config);
    //
    //     $app->register(new HttpFragmentServiceProvider());
    //     $app->register(new TwigServiceProvider(), array(
    //         'twig.templates' => array(
    //             'hello' => '{{ render("/foo") }}',
    //             'foo' => 'foo',
    //         ),
    //     ));
    //
    //     // $app->get('/hello', function () use ($app) {
    //     //     return $app['twig']->render('hello');
    //     // });
    //
    //     $app->get('/foo', function () use ($app) {
    //         return $app['twig']->render('foo');
    //     });
    //
    //     $request = Request::create('/hello');
    //     $response = $app->handle($request);
    //     $this->assertEquals('foo', $response->getContent());
    // }

    // public function testLoaderPriority()
    // {
    //     $app = new Application();
    //     $app->register(new TwigServiceProvider(), array(
    //         'twig.templates' => array('foo' => 'foo'),
    //     ));
    //     $loader = $this->getMock('\Twig_LoaderInterface');
    //     $loader->expects($this->never())->method('getSource');
    //     $app['twig.loader.filesystem'] = $app->share(function ($app) use ($loader) {
    //         return $loader;
    //     });
    //     $this->assertEquals('foo', $app['twig.loader']->getSource('foo'));
    // }
}
