<?php 

class building414 extends building
{
    public $vp = 1;
    public $freeAction = true;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,P+VP+VP);
    }
}