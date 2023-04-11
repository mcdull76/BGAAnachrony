<?php 

class building311 extends building
{
    public $vp = 1;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
    
}