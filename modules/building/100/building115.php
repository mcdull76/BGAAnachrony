<?php 

class building115 extends building
{
    public $vp = 3;
    public $actionCost = G;
        
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
        $this->player->gain(null,null,VP);
    }
}