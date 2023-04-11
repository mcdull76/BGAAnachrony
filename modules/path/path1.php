<?php 

define('SALVATION', 1);

class path1 extends path
{      
    public $name = "Salvation";
    public $leadernames = ["Shepherd Caratacus","High Sunwalker Amena"];
    public $initialResources= [1,2,0,2,4,3];
    public $moralA = 4;
    public $moralB = 5;
    public $sleepings = [ADMIN];
    public $awakes = [SCIENTIST,SCIENTIST,SCIENTIST,ENGINEER];
    
    public $powerupcost = array(
        SIDEA => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E]),
        SIDEB => array ( 1=>[0],2=>[G,U,T],3=>[E],4=>[G,U,T],5=>[0],6=>[E])
    ); 
    
    public $buildingcostB = array(
        1 => array (1 => N+T+T+W, 2=> N+T+T+W+W, 3=> N+N+T+T+W+W),
        2=> array(1 => T+T+G, 2=> T+T+G+W+W, 3=> T+T+G+G+W+W),
        3=> array(1 => T+T, 2=> U+T+T+W, 3=> U+T+T+G+W+W),
        4=> array(1 => U+T+T+W, 2=> U+T+T+W+W, 3=>U+U+T+T+G+W+W ),
    );
    
    public $moraleVPB = [-8,-6,-4,-2,0,2,4,2];
    public $moralecostB = [4,4,4,5,5,6,7];
    public $temporalVPB = [0,2,4,6,8,12,14,16,20];
    
    public $gainEvacuateA = 3;
    public $gainEvacuateB = 4;
    
    function canEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            return self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type div 100 = 1 ")>=3;
        }
        else
        {
            return self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type = 515 ")>=2;
        }
    }
    
    public function getGainExtraEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            return $this->player->resources[NEUTRONIUM] * 3;
        }
        else
        {
            $vortex = intdiv(self::getUniqueValueFromDB("select temporal from player where player_id = {$this->player->player_id}")-1,2);
            $uranium = intdiv($this->player->resources[URANIUM],2);
            return 5*min($vortex, $uranium);
        }
    }
    
    function getPowerupBlocked() {
        if($this->player->leader == 2)
        {
            return [];
        }
        else
        {
            return [2,3];
        }
    }
}