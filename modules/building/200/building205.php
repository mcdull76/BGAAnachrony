<?php 

class building205 extends building
{
    public $vp = 2;
    public $actionCost = W;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, 2*G);
    }
}