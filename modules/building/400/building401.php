<?php 

class building401 extends building
{
    public $vp = 4;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}