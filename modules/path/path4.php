<?php 

define('DOMINANCE', 4);

class path4 extends path
{
    public $name = "Dominance";
    public $leadernames = ["Captain Wolfe", "Treasure Hunter Samira"];
    public $initialResources= [0,1,2,2,3,4];
    public $moralA = 4;
    public $moralB = 4;
    public $sleepings = [ENGINEER];
    public $awakes = [SCIENTIST,SCIENTIST,ENGINEER,ENGINEER];

    public $powerupcost = array(
        SIDEA => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E]),
        SIDEB => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E])
    ); 
    
    public $buildingcostB = array(
        1 => array (1 => N+T+T+W, 2=> N+T+T+W+W, 3=> N+N+T+T+W+W),
        2=> array(1 => T+T+G, 2=> T+T+G+W+W, 3=> T+T+G+W+W),
        3=> array(1 => T+T, 2=> T+T+W, 3=> U+T+T+G+W+W),
        4=> array(1 => U+T+T+W, 2=> U+T+T+W+W, 3=>U+U+T+T+G+W+W ),
    );
    
    public $moraleVPB = [-3,-2,-1,0,2,4,6,2];
    public $moralecostB = [4,4,4,4,5,5,5];
    public $temporalVPB = [0,2,4,6,8,10,12,12,12];
    
    function getPowerupCost($index)
    {
        return $this->powerupcost[anachrony::$instance->getGameStateValue( 'board_side')][$index];
    }
    
    function getPowerupBonus()
    {
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            return 0;
        }
        else
        {            
            return 1;
        }
    }
    
    public $gainEvacuateA = 5;
    public $gainEvacuateB = 3;
    
    function canEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            return self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type div 100 = 2 ")>=3;
        }
        else
        {
            return $this->player->moral == 7;
        }
    }
    
    public function getGainExtraEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            $gold = $this->player->resources[TITANIUM];
            $genius  = self::getUniqueValueFromDB("select count(*) from worker where type = ".ENGINEER." and player_id = {$this->player->player_id}");
            return 2*min($gold, $genius);
        }
        else
        {
            $worker  = self::getUniqueValueFromDB("select count(*) from worker where player_id = {$this->player->player_id}");
            return $worker;
        }
    }
    
    function addExtraCleanup()
    {
        if($this->player->leader == 2)
        {
            anachrony::$instance->addPendingSub($this->player_id, null, "path4", "extraCleanup2",null);
            anachrony::$instance->addPendingSub($this->player_id, null, "path4", "extraCleanup",null);
        }
    }
    
    public function argextraCleanup($parg1, $parg2)
    {
        return null;
    }
    
    public function doextraCleanup($parg1, $parg2, $varg1, $varg2)
    {
        $gain = pow(10,2+bga_rand(0,2));
        $this->player->gain(null,null,$gain);
    }
    
    public function argextraCleanup2($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('6. Clean Up phase : ${actplayer} may pay 2 <div id="mineres_0" class="mineres anaicon res5"></div> to get extra resource');
        $ret['titleyou'] = clienttranslate('6. Clean Up phase : ${you} may pay 2 <div id="mineres_0" class="mineres anaicon res5"></div> to get extra resource');
        
        
        if($this->player->checkCost(2*W))
        {
            $ret['buttons'][] = 'res'.T;
            $ret['selectable']['res'.T] = array();
            $ret['buttons'][] = 'res'.U;
            $ret['selectable']['res'.U] = array();
            $ret['buttons'][] = 'res'.G;
            $ret['selectable']['res'.G] = array();
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        return $ret;
    }
    
    public function doextraCleanup2($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {
            $this->player->pay(null,null,2*W);
            $this->player->gain(null,null,$varg1);
        }
    }
    
}