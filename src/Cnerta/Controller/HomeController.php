<?php

namespace Cnerta\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Silex\Application;

/**
 *
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class HomeController
{   
    public function getHome()
    {
        return new Response(file_get_contents(__DIR__ . "/../../../web/index.html"), Response::HTTP_OK);
    }
    
}
