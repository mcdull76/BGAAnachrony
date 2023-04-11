<?php 

class building309 extends building
{
    public $vp = 2;
    public $actionCost = N;    
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,8*W);
    }
}