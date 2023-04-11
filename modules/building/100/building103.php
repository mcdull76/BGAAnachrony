<?php 

class building103 extends building
{
    public $vp = 3;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 2+ $this->player->getBackintimeBonus());
    }
}