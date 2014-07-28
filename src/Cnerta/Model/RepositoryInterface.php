<?php

namespace Cnerta\Model;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
interface RepositoryInterface {
    
    /**
     * Add or update a repository in the list
     * 
     * @param array $repository
     * @param array $ripositoryList
     */
    public function addRepository($repository, &$ripositoryList, $repositoryToUpdate = null);
    
    /**
     * Delete a repository in the repository list
     * 
     * @param array $repository
     * @param array $ripositoryList
     */
    public function deleteRepository($repository, &$ripositoryList);
}
