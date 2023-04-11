<?php 

class building201 extends building
{
    public $vp = 1;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, 2*T);
    }
}