<?php 

class building202 extends building
{
    public $vp = 1;
    public $actionCost = W;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, 3*T);
    }
}