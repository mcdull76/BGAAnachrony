<?php 

define('PROGRESS', 2);

class path2 extends path
{
    public $name = "Progress";
    public $leadernames = ["Patron Valerian","Librarian Cornella"];
    public $initialResources= [0,1,1,2,3,4];
    public $moralA = 4;
    public $moralB = 4;
    public $sleepings = [ENGINEER];
    public $awakes = [SCIENTIST,SCIENTIST,ADMIN,ADMIN];
    
    public $powerupcost = array(
        SIDEA => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E]),
        SIDEB => array ( 1=>[0],2=>[0],3=>[0],4=>[G,U,T,E],5=>[G,U,T,E],6=>[G,U,T,E])
    ); 
    
    public $buildingcostB = array(
        1 => array (1 => N+T+T+G+W, 2=> N+T+T+G+W+W, 3=> N+N+T+T+W+W),
        2=> array(1 => T+T+G, 2=> T+T+G+W+W, 3=> T+T+G+G+W+W),
        3=> array(1 => U+T+T, 2=> U+T+T+W, 3=> U+T+T+G+W+W),
        4=> array(1 => U+T+T+W, 2=> U+T+T+W+W, 3=>U+U+T+T+G+W+W ),
    );    
    
    public $moraleVPB = [-6,-4,-2,0,3,5,7,2];
    public $moralecostB = [4,4,5,5,7,8,8];
    public $temporalVPB = [0,1,2,4,8,10,12,14,16];
    
    function getParadoxMax() {
        $bonus = 0;
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            $bonus = 1;
        }
        return parent::getParadoxMax()+$bonus;
    }
    
    public $gainEvacuateA = 6;
    public $gainEvacuateB = 3;
    
    function canEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            return self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type div 100 = 4 ")>=3;
        }
        else
        {
            return $this->player->resources[WATER]>=8;
        }
    }
    
    public function getGainExtraEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            $bt = self::getUniqueValueFromDB("select count(*) from breakthrough where player_id = {$this->player->player_id}");
            $scientist  = intdiv(self::getUniqueValueFromDB("select count(*) from worker where type = ".SCIENTIST." and player_id = {$this->player->player_id}"),2);
            return 4*min($bt, $scientist);
        }
        else
        {
            return 5 * self::getUniqueValueFromDB("select count(*) from superproject where player_id = {$this->player->player_id}");
        }
    }
    
    function addExtraCleanup()
    {
        if($this->player->leader == 2)
        {
            anachrony::$instance->addPendingSub($this->player_id, null, "path2", "extraCleanup",null);
        }
    }
    
    public function argextraCleanup($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('6. Clean Up phase : ${actplayer} may pay 3 <div id="mineres_0" class="mineres anaicon res5"></div> to take a Research action');
        $ret['titleyou'] = clienttranslate('6. Clean Up phase : ${you} may pay 3 <div id="mineres_0" class="mineres anaicon res5"></div> to take a Research action');
         
        if($this->player->checkCost(3*W))
        {
            $ret['buttons'][] = 'res'.(3*W);
            $ret['selectable']['res'.(3*W)] = array();
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        return $ret;
    }
    
    public function doextraCleanup($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {            
            $this->player->pay(null,null,3*W);
            anachrony::$instance->addPending($this->player_id, 0, "research", null, null);
            
        }
    }
}