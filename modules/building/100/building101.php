<?php 

class building101 extends building
{
    public $vp = 4;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 1 + $this->player->getBackintimeBonus());
    }
}