<?php 

define('HARMONY', 3);

class path3 extends path
{
    public $name = "Harmony";
    public $leadernames = ["Patriarch Haulani", "Matriarch Zaida"];
    public $initialResources= [0,0,1,3,6,3];
    public $moralA = 4;
    public $moralB = 3;
    public $sleepings = [GENIUS];
    public $awakes = [SCIENTIST,SCIENTIST,ENGINEER,ENGINEER];
    
    public $powerupcost = array(
        SIDEA => array ( 1=>[0],2=>[0],3=>[0],4=>[E],5=>[E],6=>[E]),
        SIDEB => array ( 1=>[0],2=>[0],3=>[0],4=>[E,3*W],5=>[E],6=>[E,3*W])
    ); 
    
    public $buildingcostB = array(
        1 => array (1 => N+T+T+W, 2=> N+T+T+G+W+W, 3=> N+N+T+T+W+W),
        2=> array(1 => T+T+G+W, 2=> T+T+G+W+W, 3=> T+T+G+G+W+W),
        3=> array(1 => T+T+W, 2=> U+T+T+W, 3=> U+T+T+G+W+W),
        4=> array(1 => U+T+T+W, 2=> U+T+T+W+W, 3=>U+U+T+T+G+W+W ),
    );
    
    public $moraleVPB = [-4,-2,0,2,4,6,8,3];
    public $moralecostB = [4,4,5,5,6,7,8];
    public $temporalVPB = [0,2,4,6,8,10,12,14,16];
    
    function getPowerupBonus()
    {
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            return 2;
        }
        else
        {
            return 1;
        }
    }
    
    function getPowerupBlocked() {
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            return [2,3,5];
        }
        else
        {
            return [2,3];
        }
    }
    
    public $gainEvacuateA = 2;
    public $gainEvacuateB = 2;
    
    function canEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            return self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type div 100 = 3 ")>=3;
        }
        else
        {
            $nbbuildings = self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id} and type <> 515");
            $nbsuper = self::getUniqueValueFromDB("select count(*) from superproject where player_id = {$this->player->player_id}");
            return ($nbbuildings+2*$nbsuper)>=6;
        }
    }
    
    public function getGainExtraEvacuate()
    {
        if($this->player->evacuation_side == SIDEA)
        {
            $gold = $this->player->resources[GOLD];
            $genius  = self::getUniqueValueFromDB("select count(*) from worker where type = ".GENIUS." and player_id = {$this->player->player_id}");
            return 3*min($gold, $genius);
        }
        else
        {
            $nbbuildings = self::getUniqueValueFromDB("select count(*) from building where player_id = {$this->player->player_id}");
            $admin  = self::getUniqueValueFromDB("select count(*) from worker where type = ".ADMIN." and player_id = {$this->player->player_id}");
            return 3*min($admin, $nbbuildings);
        }
    }
    
    public function argspecialAnomaly($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may destroy a building instead of gaining an anomaly');
        $ret['titleyou'] = clienttranslate('${you} may destroy a building instead of gaining an anomaly');
        
        $buildings = self::getCollectionFromDb("select * from building where type <> 515 and player_id = ".$this->player_id);
        foreach($buildings as $building)
        {   
            $ret['selectable']['building_'.$building['id']] = array();
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    public function dospecialAnomaly($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {
            $building_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $building = self::getObjectFromDB( "select * from building where id= {$building_id}");
            
            $path_id = self::getUniqueValueFromDB( "select id from path where location = 'phbuilding{$building['type']}'");
            if($path_id != null)
            {
                anachrony::$instance->notifyAllPlayers( "remove", '', array(
                    'id' => 'path_'.$path_id
                ) );
                self::DbQuery("delete from path where id = '{$path_id}'");
            }
            
            $worker_id = self::getUniqueValueFromDB( "select id from worker where location = 'phbuilding{$building['type']}'");
            if($worker_id != null)
            {
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'worker_'.$worker_id,
                    'parent' => "hidden{$this->player_id}"
                ) );
                self::DbQuery("update worker set location='hidden{$this->player_id}' where id = {$worker_id}");
            }
                        
            $bottom_id = 1+self::getUniqueValueFromDB( "select max(location_arg) from building where location = 1 and player_id IS NULL and category = {$building['category']}");
            self::DbQuery("update building set player_id = NULL, location=1, location_arg = {$bottom_id} where id = {$building['id']}");
            
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'building_'.$building_id,
                'parent' => "phbuilding".$building['category']."1_null",
                'position' => 'first'
            ) );
        }
        else
        {
            anachrony::$instance->addPending($this->player_id, null, "anomaly");  
        }
    }
    
    function addExtraCleanup()
    {
        if($this->player->leader == 2)
        {
            anachrony::$instance->addPendingSub($this->player_id, null, "path3", "extraCleanup",null);
        }
    }
    
    public function argextraCleanup($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('6. Clean Up phase : ${actplayer} may pay 2 <div id="mineres_0" class="mineres anaicon res5"></div> to recruit a worker');
        $ret['titleyou'] = clienttranslate('6. Clean Up phase : ${you} may pay 2 <div id="mineres_0" class="mineres anaicon res5"></div> to recruit a worker');
        
        if($this->player->checkCost(2*W))
        {
            $index = self::getUniqueValueFromDB( "select type from recruitsdrawn order by id desc limit 1");
            $workers = anachrony::$instance->cards_worker[$index];
            for($type=0;$type<4;$type++)
            {
                if($workers[$type]>0)
                {
                    $gain = pow(10,$type+7);
                    $ret['buttons'][] = 'res'.$gain;
                    $ret['selectable']['res'.$gain] = array();
                }
            }
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        return $ret;
    }
    
    public function doextraCleanup($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 != "Skip")
        {
            $this->player->pay(null,null,2*W);
            $this->player->gain(null,null,$varg1);            
        }
    }
    
}