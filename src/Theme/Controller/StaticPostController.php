<?php
namespace Sarcofag\Theme\Controller;

use Psr\Http\Message\ResponseInterface;
use Sarcofag\View\Renderer\PsrHttpRendererInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class StaticPostController
{
    /**
     * @var PsrHttpRendererInterface
     */
    protected $renderer;

    /**
     * StaticPostController constructor.
     *
     * @param PsrHttpRendererInterface $renderer
     */
    public function __construct(PsrHttpRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request,
                             Response $response,
                             array $args)
    {
        return $this->renderer
                    ->response($response,
                               'theme/script/static.phtml',
                               ['staticEntry'=>$args['requestedEntity']]);
    }
}
