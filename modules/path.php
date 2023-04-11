<?php 


include("path/path1.php");
include("path/path2.php");
include("path/path3.php");
include("path/path4.php");


class path extends APP_GameClass
{       
    public $type = 0;
    public $initialResources= [0,0,0,0,0,0];
    public $moralA = 0;
    public $moralB = 0;
    public $sleepings = [];
    public $awakes = [];
    public $powerupcost = array( 
        SIDEA => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E]),
        SIDEB => array ( 1=>[0],2=>[0],3=>[0],4=>[0],5=>[0],6=>[0])        
    ); 
    
    public $buildingcostA = array(
        1 => array (1 => N+T+T+W, 2=> N+T+T+G+W+W, 3=> N+N+T+T+W+W),
        2=> array(1 => T+T+G, 2=> T+T+G+W+W, 3=> T+T+G+G+W+W),
        3=> array(1 => T+T, 2=> U+T+T+W, 3=> U+T+T+G+W+W),
        4=> array(1 => U+T+T+W, 2=> U+T+T+W+W, 3=>U+U+T+T+G+W+W ),
    );
    
    public $buildingcostB = array();
    
    public $moraleVPA = [-6,-4,-2,0,2,4,6,2];
    public $moralecostA = [4,4,5,5,6,7,7];
    public $moraleVPB = [];
    public $moralecostB = [];
    
    
    public $temporalVPA = [0,2,4,6,8,10,12,14,16];
    public $temporalVPB = [0,2,4,6,8,10,12,14,16];
    
    public $leader = 1;
    
    public $gainEvacuateA = 3;
    public $gainEvacuateB = 6;
        
    public function __construct()
    {
        $this->type = (int) filter_var(get_class($this), FILTER_SANITIZE_NUMBER_INT);
        $this->player_id = anachrony::$instance->getActivePlayerId();
    }
    
    function getPowerupCost($index)
    {
        return $this->powerupcost[anachrony::$instance->getGameStateValue( 'board_side')][$index];
    }
    
    function getPowerupBonus()
    {
        return 1;
    }
    
    function getPowerupBlocked() {
        return [2,3];
    }
    
    public function getGainEvacuate()
    {
        return ($this->player->evacuation_side == SIDEA?$this->gainEvacuateA:$this->gainEvacuateB);
    }
    
    public function getGainExtraEvacuate()
    {
        return 0;
    }
    
    public function getTemporalVP($index)
    {
        $bonus = ($this->player->hasActiveSuperProject(2)?$index:0);
        return (anachrony::$instance->getGameStateValue( 'board_side') == SIDEA?$this->temporalVPA:$this->temporalVPB)[$index] + $bonus;
    }
    public function getMoraleVP($index)
    {
        $ret = (anachrony::$instance->getGameStateValue( 'board_side') == SIDEA?$this->moraleVPA:$this->moraleVPB)[$index];
        if($this->player->hasActiveSuperProject(10))
        {
            $ret = max(0, $ret);
        }
        return $ret;
    }
    public function getMoraleCost($index)
    {
        return (anachrony::$instance->getGameStateValue( 'board_side') == SIDEA?$this->moralecostA:$this->moralecostB)[$index];
    }
    
    function getBuildingCost($category, $position, $worker_type)
    {
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            $cost = $this->buildingcostB[$category][$position];
        }
        else
        {
            $cost =  $this->buildingcostA[$category][$position];
        }
        
        if((intdiv($cost, T) % 10) > 0 && ($worker_type == ENGINEER || $worker_type == GENIUS || ( SCIENTIST == $worker_type && $this->player->canUseSCasGE()) ))
        {
            $cost -= T;
        }
        return $cost;
    }
    
    function getParadoxMax() {
        return 3 + ($this->player->hasActiveBuilding(405)?1:0);
    }
    
    function canEvacuate()
    {
        return false;
    }
    
    function addExtraCleanup()
    {
    }
}