<?php 

class building107 extends building
{
    public $vp = 3;
    public $actionCost = U;
        
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
        $this->player->gain(null,null,VP);
    }
}