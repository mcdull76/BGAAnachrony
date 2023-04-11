<?php 

class building214 extends building
{
    public $vp = 1;
    public $actionCost = 3*W;
    public $workerTypeRequired = ENGINEER;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, 2*E);
    }
}