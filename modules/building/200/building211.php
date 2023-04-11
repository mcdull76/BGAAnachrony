<?php 

class building211 extends building
{
    public $vp = 3;
    public $actionCost = T;
    public $workerTypeRequired = ENGINEER;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, E);
    }
}