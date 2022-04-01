<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

$users = ['mike', 'mishel', 'adel', 'keks', 'kamila'];

use Slim\Factory\AppFactory;
use DI\Container;

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) {
    $response->getBody()->write('Welcome to Slim!');
    return $response;
    // Благодаря пакету slim/http этот же код можно записать короче
    // return $response->write('Welcome to Slim!');
});

$app->post('/users', function ($request, $response) {
    return $response->withStatus(302);
});

$app->get('/courses/{id}', function ($request, $response, array $args) {
    $id = $args['id'];
    $response->getBody()->write("Course id: {$id}");
    return $response;
});

$app->get('/users/{id}', function ($request, $response, $args) use ($users) {
    $params = ['id' => $args['id'], 'nickname' => 'user-' . $args['id']];

    return $this->get('renderer')->render($response, 'users/show.phtml', $params);
});

$app->get('/users', function ($request, $response) use ($users) {
    $term = $request->getQueryParams('term');
    $newUsers = [];
    foreach ($users as $user) {
        if(str_contains($user, $term['term'])) {
            array_push($newUsers, $user);
        }
    }
    if ($term == '') {
        $filtUsers = $users;
    }
    $params = [
        'term' => $term,
        'users' => $newUsers
    ];

    return $this->get('renderer')->render($response, 'users/index.phtml', $params);
});
$app->run();