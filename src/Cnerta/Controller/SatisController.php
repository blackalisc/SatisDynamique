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
    public function getAllPakage(Application $app)
    {
        return new JsonResponse($app['sd.service.satis.manager']->getPackages());
    }
    
    public function postPakage(Application $app, Request $request)
    {       
        $package = $request->request->get("package");

        if (array_key_exists("old", $package)) {

            try {
                return new JsonResponse(
                        $app['sd.service.satis.manager']
                                ->addPackage(
                                        $package['new']['name'], $package['new']['version']
                                ), Response::HTTP_CREATED
                );
            } catch (\Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
            
        } elseif (array_key_exists("name", $package) && array_key_exists("version", $package)) {

            try {

                return new JsonResponse(
                        $app['sd.service.satis.manager']
                                ->addPackage(
                                        $package['name'], $package['version']
                                ), Response::HTTP_CREATED
                );
            } catch (\Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }
        
        return new JsonResponse("We don't understand your request.", Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function deletePakage(Application $app, Request $request)
    {
        
        $package = json_decode($request->query->get("package"), true);

        if (!array_key_exists("name", $package)) {
            return new JsonResponse("No package name found", Response::HTTP_BAD_REQUEST);
        }

        try {
            return new JsonResponse($app['sd.service.satis.manager']->removePackage($package['name']), Response::HTTP_CREATED);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        
        return new JsonResponse("We don't understand your request.", Response::HTTP_UNPROCESSABLE_ENTITY);
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
        
        return new JsonResponse("We don't understand your request.", Response::HTTP_UNPROCESSABLE_ENTITY);
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
        
        return new JsonResponse("We don't understand your request.", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
