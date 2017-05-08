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
    protected function getConfig(){
        return [
            'config.path' => 'tests/unit/resources',


        ];
    }
    protected function getContainer(){
        $container = new Container();
        $container['template_variables'] = ['site_title', 'environment'];
        $container['site_title'] = 'TEST TEST';

        return $container;
    }
    public function testRegisterAndRender()
    {
        $config = $this->getConfig();

        $container = $this->getContainer();
        $app = new Application($container, $config);


        $app->register(new TwigServiceProvider(), array(

        ));
        $container['twig.templates'] = array(
            'hello' => 'Hello {{ name }}!'
        );

        $name = 'john';

        $content = $container['twig']->render('hello', array('name' => $name));

        $this->assertEquals('Hello john!', $content);
    }

    // public function testRenderStringFunction()
    // {
    //     $config = $this->config();
    //     $container = new Container();
    //     $app = new Application($container, $config);
    //
    //     $template = 'Hello {{ name }}!';
    //     $app->register(new TwigServiceProvider(), array(
    //     ));
    //
    //     // $app->get('/hello/{name}', function ($name) use ($app) {
    //     //     return $app['twig']->render('hello', array('name' => $name));
    //     // });
    //     $name = 'john';
    //
    //     $content = $container['twig']->render('hello', array('name' => $name));
    //
    //     // $request = Request::create('/hello/john');
    //     // $response = $app->handle($request);
    //     $this->assertEquals('Hello john!', $content);
    // }

    //http://stackoverflow.com/questions/31081910/what-to-use-instead-of-twig-loader-string
    public function testGlobal()
    {
        $config = $this->getConfig();

        $container = $this->getContainer();
        $app = new Application($container, $config);

        $template = 'Hello {{ name }}!';


        foreach($container['template_variables'] as $key => $value)
		{
			if(isset($container[$key])){

				$paramValue = $container[$key];
				$container['twig']->addGlobal($key, $paramValue);
			}

		}

        $app->register(new TwigServiceProvider(), array(
        ));
        $name = 'john';
        $template = $container['twig']->createTemplate('Hello {{ name }}!');
        $content = $template->render(array('name' => $name));
        $this->assertEquals('Hello john!', $content);

    }
}
