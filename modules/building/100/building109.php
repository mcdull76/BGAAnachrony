<?php 

class building109 extends building
{
    public $vp = 2;
    public $workerTypeRequired = SCIENTIST;    
    public $actionCost = N;
       
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
        $this->player->gain(null,null,2*VP);
    }
}