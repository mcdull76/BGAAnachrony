<?php 

class building212 extends building
{
    public $vp = 2;
    public $workerTypeRequired = ENGINEER;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, E);
    }
}