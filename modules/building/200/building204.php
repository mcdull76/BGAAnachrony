<?php 

class building204 extends building
{
    public $vp = 3;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, G);
    }
}