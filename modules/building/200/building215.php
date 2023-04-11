<?php 

class building215 extends building
{
    public $vp = 1;
    public $freeAction = true;
    public $actionCost = W;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {        
        anachrony::$instance->addPending($this->player_id, null, "gain", json_encode([G,U,T]));
    }
}