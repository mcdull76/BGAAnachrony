<?php 

class building407 extends building
{
    public $vp = 2;
    public $workerTypeRequired = SCIENTIST;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        if(self::getUniqueValueFromDB( "select count(*) from vortex where location <> 0 and player_id = ".$this->player_id )>0)
        {
            anachrony::$instance->addPending($this->player_id,  $this->worker_id, "removeVortex");
        }
    }
}