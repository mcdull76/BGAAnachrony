<?php 

class building302 extends building
{
    public $vp = 1;
    public $freeAction = true;
    
    public function whenBuild()
    {
        $this->player->gain(null,null,3*W);
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,W);
    }
}