<?php 

class building111 extends building
{
    public $vp = 1;
     
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 3+ $this->player->getBackintimeBonus());
    }
    
    public function whenBuild()
    {
        if(self::getUniqueValueFromDB( "select count(*) from vortex where location <> 0 and player_id = ".$this->player_id )>0)
        {
            anachrony::$instance->addPending($this->player_id, null, "removeVortex");
        }
    }
}