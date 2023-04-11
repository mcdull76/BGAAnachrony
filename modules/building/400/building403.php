<?php 

class building403 extends building
{
    public $vp = 2;
    public $actionCost = E;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, EX);
    }
}