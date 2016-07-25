<?php
use Interop\Container\ContainerInterface;

return [
    'settings' => function () {
            return require __DIR__ . '/../config.inc.php';
    },


    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];
        $pdo = new \PDO("mysql:host={$settings['host']};dbname={$settings['dbname']};charset=utf8",
                        $settings['user'],
                        $settings['password']);

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },

    'Renderer' => function (ContainerInterface $container) {
        /* @var $themeEntity WP_Theme */
        $themeEntity = $container->get(\Sarcofag\API\WP::class)->wp_get_theme();
        $themeDirectory = $themeEntity->get_template_directory() . '/src/api/view';

        $paths = array_map('realpath',
                            array_merge($container->get('template.paths'),
                                        [$themeEntity->get_template() => $themeDirectory]));
        
        $renderer = new \Sarcofag\View\Renderer\SimpleRenderer($container->get('HelperManager'), $paths,
                                                               $container->get('Sarcofag\API\WP'));
        return $renderer;
    },

    'errorHandler' => function (ContainerInterface $container) {
            return new \Slim\Handlers\Error($container->get('settings')['displayErrorDetails']);
    },

    'notFoundHandler' => function () {
            return new \Slim\Handlers\NotFound();
    },

    'notAllowedHandler' => function () {
            return new \Slim\Handlers\NotAllowed();
    },

    'environment' => function () {
        return new \Slim\Http\Environment($_SERVER);
    },

    'request' => function (ContainerInterface $container) {
        return \Slim\Http\Request::createFromEnvironment($container->get('environment'));
    },

    'response' => function (ContainerInterface $container) {
        $headers = new \Slim\Http\Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $response = new \Slim\Http\Response(200, $headers);

        return $response->withProtocolVersion($container->get('settings')['httpVersion']);
    },

    'callableResolver' => function (ContainerInterface $container) {
        return new \Slim\CallableResolver($container);
    },

    'renderer' => function (ContainerInterface $container) {
        return $container->get(Slim\Views\PhpRenderer::class);
    }
];
