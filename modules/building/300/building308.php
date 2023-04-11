<?php 

class building308 extends building
{
    public $vp = 2;
    public $workerTypeRequired = ADMIN;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,5*W);
    }
}