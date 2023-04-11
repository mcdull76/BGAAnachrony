<?php 

class building402 extends building
{
    public $vp = 3;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}