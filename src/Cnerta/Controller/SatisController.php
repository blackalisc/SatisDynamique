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
class SatisController
{   
    public function getHelp(Application $app)
    {
        return json_encode("hello");
    }
    
    public function getAllPakage(Application $app)
    {
        return new JsonResponse($app['sd.service.satis.manager']->getPackages());
    }
    
    public function postPakage(Application $app, Request $request)
    {
        $name = $request->get("name");
        $version = $request->get("version");
        try {
            return new JsonResponse($app['sd.service.satis.manager']->addPackage($name, $version), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
    public function deletePakage(Application $app, Request $request)
    {
        $name = $request->get("name");

        try {
            return new JsonResponse($app['sd.service.satis.manager']->removePackage($name), Response::HTTP_CREATED);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllRepositories(Application $app)
    {
        return new JsonResponse($app['sd.service.satis.manager']->getRepositories());
    }
    
    public function postRepository(Application $app, Request $request)
    {
        $type = $request->get("type");
        $url = $request->get("url");
        try {
            return new JsonResponse($app['sd.service.satis.manager']->addRepository($type, $url), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
    public function deleteRepository(Application $app, Request $request)
    {
        $type = $request->get("type");
        $url = $request->get("url");
        try {
            return new JsonResponse($app['sd.service.satis.manager']->removeRepository($type, $url), Response::HTTP_CREATED);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    
}
