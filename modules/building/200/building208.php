<?php 

class building208 extends building
{
    public $vp = 2;
    public $actionCost = W+G;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null, N+VP);
    }
}