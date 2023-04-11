<?php 

class building310 extends building
{
    public $vp = 2;
    
    function isCleanupDead($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,7*W);
    }
}