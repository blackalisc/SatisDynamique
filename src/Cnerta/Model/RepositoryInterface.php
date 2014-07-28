<?php

namespace Cnerta\Model;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
interface RepositoryInterface {
    
    /**
     * Add or replace a repository in the list
     * 
     * @param array $repository
     * @param array $ripositoryList
     */
    public function addRepository($repository, &$ripositoryList);
    
    
    /**
     * Delete a repository in the repository list
     * 
     * @param array $repository
     * @param array $ripositoryList
     */
    public function deleteRepository($repository, &$ripositoryList);
}
