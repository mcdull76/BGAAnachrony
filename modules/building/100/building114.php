<?php 

class building114 extends building
{
    public $vp = 1;
    public $workerTypeRequired = SCIENTIST;
       
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
    }
}