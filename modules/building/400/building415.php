<?php 

class building415 extends building
{
    public $vp = 2;
    public $workerTypeRequired = SCIENTIST;
    
    function isCleanupDead($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,W+VP+W+VP);
    }
}