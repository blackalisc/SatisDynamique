<?php

namespace Cnerta\Validator;

use Cnerta\Model\RepositoryInterface;

/**
 * @author valérian Girard <valerian.girard@educagri.fr>
 */
class UrlValidator
{
    /**
     * Validate an URL
     * 
     * @param string $url
     * @return boolean
     */
    public static function validate($url)
    {
        $isUrlValid = false;
        
        $isUrlValid |= filter_var($url, FILTER_VALIDATE_URL);
        
        $isUrlValid |= (boolean) preg_match('#^(?:(?:https?|git)://github\.com/|git@github\.com:)([^/]+)/(.+?)(?:\.git)?$#', $url);

        return (boolean) $isUrlValid;
    }
}
