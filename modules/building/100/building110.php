<?php 

class building110 extends building
{
    public $vp = 2;
    public $actionCost = W;
        
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 4+ $this->player->getBackintimeBonus());
    }
}