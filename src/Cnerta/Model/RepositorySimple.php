<?php

namespace Cnerta\Model;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Cnerta\Model\RepositoryInterface;
use Cnerta\Validator\UrlValidator;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class RepositorySimple implements RepositoryInterface
{

    protected $packageType = array("git", "vcs", "hg", "composer");

    public function __construct()
    {
    }

    public function addRepository($repository, &$ripositoryList, $repositoryToUpdate = null)
    {
        $repositoryToUpdate = $repositoryToUpdate ? serialize($repositoryToUpdate) : null;
        
        if (!UrlValidator::validate($repository['url'])) {
            throw new \BadMethodCallException(sprintf("The URL :%s is not a valid URL", $repository['url']));
        }

        if (!in_array($repository['type'], $this->packageType)) {
            throw new \BadMethodCallException(
            sprintf("The type :%s is not a valid type, only : %s", $repository['type'], implode(', ', $this->packageType)));
        }

        $isRepositoryDefined = false;
        
        foreach ($ripositoryList as $key => $repo) {
            if ($repositoryToUpdate != null && $repositoryToUpdate == serialize($repo)) {
                $ripositoryList[$key] = $repository;
                $isRepositoryDefined = true;
            }
        }
        
        if ($isRepositoryDefined == false) {
            $ripositoryList[] = $repository;
        }
    }

    public function deleteRepository($repository, &$ripositoryList)
    {
        $isDeleted = false;
        $repositorySerialize = serialize($repository);

        foreach ($ripositoryList as $key => $repo) {

            $repoSerialize = serialize($repo);

            if($repositorySerialize == $repoSerialize) {
                unset($ripositoryList[$key]);
                $isDeleted = true;
            }
        }
        
        if($isDeleted == false) {
            throw new NotFoundHttpException(sprintf("This repository dose not exist %s", $repository['url']));
        }
    }

}
