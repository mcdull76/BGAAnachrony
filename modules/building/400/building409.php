<?php 

class building409 extends building
{
    public $vp = 2;
    public $actionCost = 2*W;
    public $workerTypeRequired = ADMIN;
    
    function isCleanupAwake($workertype)
    {
        return true;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, null, "gain", json_encode([SC,EN]));
    }
}