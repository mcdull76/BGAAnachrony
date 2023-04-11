<?php 

class building315 extends building
{
    public $vp = 2;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
    
    public function whenBuild()
    {
        $this->player->gain(null,null,8*W);
    }
}