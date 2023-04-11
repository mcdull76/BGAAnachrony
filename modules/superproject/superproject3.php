<?php 

class superproject3 extends superproject
{ 
    public $cost = T+T+G+G;
    public $vp = 6;
    public $fixedCostShape = 3;
    public $fixedCostIcon = 4;
    public $variableShape1 = 1;
    public $variableShape2 = 2;
    public $name = "Continuum stabilizer";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
    
    public function whenBuild()
    {
        if(self::getUniqueValueFromDB( "select count(*) from vortex where location <> 0 and player_id = ".$this->player_id )>0)
        {
            anachrony::$instance->addPending($this->player_id, null, "removeVortex");
            anachrony::$instance->addPending($this->player_id, null, "removeVortex");
            anachrony::$instance->addPending($this->player_id, null, "removeVortex");
        }
    }
}