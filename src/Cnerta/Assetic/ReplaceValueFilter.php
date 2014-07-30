<?php

namespace Cnerta\Assetic;

use Assetic\Asset\AssetInterface;

use Assetic\Filter\FilterInterface;

class ReplaceValueFilter implements FilterInterface {
    
    private $arrayReplacement;
    
    public function __construct($arrayReplacement) {
        $this->arrayReplacement = $arrayReplacement;
    }

    
    public function filterDump(AssetInterface $asset) {
        
        $content = $asset->getContent();
        
        foreach ($this->arrayReplacement as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        $asset->setContent($content);
        return;
    }

    public function filterLoad(AssetInterface $asset) {
    }

}