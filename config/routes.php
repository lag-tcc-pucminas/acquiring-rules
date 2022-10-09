<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;

Router::addRoute('GET', '/', 'App\Controller\Documentation\DocumentationController@index');

Router::addRoute('GET', '/docs', 'App\Controller\Documentation\DocumentationController@docs');

Router::addRoute('GET' , '/health', 'App\Controller\HealthController@index');

Router::addRoute('GET', '/acquirers', 'App\Controller\AcquirerController@getAll');

Router::addRoute('POST', '/scenarios', 'App\Controller\PaymentScenarioController@store');
Router::addRoute('GET', '/scenarios/{id}', 'App\Controller\PaymentScenarioController@getById');
Router::addRoute('DELETE', '/scenarios/{id}', 'App\Controller\PaymentScenarioController@delete');
Router::addRoute('PUT', '/scenarios/{id}', 'App\Controller\PaymentScenarioController@update');
Router::addRoute('GET', '/scenarios', 'App\Controller\PaymentScenarioController@search');
Router::addRoute(
    'GET',
    '/acquirer-prioritization',
    'App\Controller\PaymentScenarioController@getAcquirerPrioritization'
);