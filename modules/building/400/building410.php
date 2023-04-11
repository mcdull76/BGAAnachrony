<?php 

class building410 extends building
{
    public $vp = 1;
    public $actionCost = 2*W;
    public $workerTypeRequired = ADMIN;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,GE);
    }
}