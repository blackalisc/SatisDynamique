<?php

namespace Cnerta\Model;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Cnerta\Model\RepositoryInterface;
use Cnerta\Validator\UrlValidator;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class RepositoryPackage implements RepositoryInterface
{

    protected $packageType = array("package", "git", "vcs", "hg", "composer");
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addRepository($repository, &$ripositoryList)
    {
        if ($this->checkRepositorySchema($repository)) {

            if (!UrlValidator::validate($repository['package']['source']['url'])) {
                throw new \BadMethodCallException(sprintf("The URL :%s is not a valid URL", $repository['package']['source']['url']));
            }
            if ($repository['package']['dist']['url'] != "") {
                if (!UrlValidator::validate($repository['package']['dist']['url'])) {
                    throw new \BadMethodCallException(sprintf("The URL :%s is not a valid URL", $repository['package']['dist']['url']));
                }
                if (!in_array($repository['package']['dist']['type'], $this->packageType)) {
                    throw new \BadMethodCallException(
                    sprintf("The type of dist :%s is not a valid type, only : %s", $repository['package']['dist']['type'], implode(', ', $this->packageType)));
                }
            }

            $isRepositoryDefined = false;
            foreach ($ripositoryList as $key => $repo) {

                if ($repo['type'] == $repository['type'] && $repo['package']['name'] == $repository['package']['name']) {
                    $ripositoryList[$key] = $repository;
                    $isRepositoryDefined = true;
                }
            }

            if ($isRepositoryDefined == false) {
                $ripositoryList[] = $repository;
            }
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
            throw new NotFoundHttpException(sprintf("This repository dose not exist %s", $repository['package']['name']));
        }
    }

    private function checkRepositorySchema($repository)
    {
        $SchemaFile = new \SplFileInfo(sprintf("%s/src/Cnerta/Resources/Schemas/repository.json", $this->config['base_path']));

        $schemaData = json_decode(file_get_contents($SchemaFile->getRealPath()));
        $data = json_decode(json_encode($repository));

        $validator = new \JsonSchema\Validator();
        $validator->check($data, $schemaData);

        $errors = "";

        if (!$validator->isValid()) {

            $errors .= "JSON does not validate. Violations:\n";
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
            }

            throw new \Exception($errors);
        }

        return true;
    }

}
