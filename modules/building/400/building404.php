<?php 

class building404 extends building
{
    public $vp = 3;
    public $workerTypeRequired = SCIENTIST;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->pay(null,null,P);
    }
}