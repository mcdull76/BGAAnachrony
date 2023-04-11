<?php 

class building313 extends building
{
    public $vp = 1;
    public $actionCost = U;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,6*W+VP);
    }
}