<?php 

class construct extends action
{     
    public $needExosuit = true;
    
    function __construct( )
    {
        parent::__construct();
        $this->maxindex = self::getUniqueValueFromDB("select count(*) from player")>3?3:2;
    }
        
    public function getActionCost($index = 0)
    {    
        if(anachrony::$instance->getGameStateValue( "after_impact") == 1)
        {
            return 0;
        }
        $minus = ($this->player->hasActiveSuperProject(4)?1:0);
        return W*(max(0,$index - 1 - $minus));
    }
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return parent::canDo($worker, $index, $ignorefull)
            && $worker['type'] != ADMIN;
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose what to construct');
        $ret['titleyou'] = clienttranslate('${you} must choose what to construct');
        
        $reducs = [0];
        $blocked = $this->getBlocked();
        if($blocked != null)
        {
            $reducs = $blocked->getCostBonus();
        }
        else if($this->player->hasActiveSuperProject(5))
        {
            $reducs = [G,U,T];
        }
        
        
        $buildings = self::getCollectionFromDb( "select * from building where location_arg = 1 and player_id IS NULL" );
        foreach($buildings as $building_id => $building)
        {
            $name = "building".$building['type'];
            $b = new $name();            
            $b->type = $building['type'];
            $b->category = $building['category'];
            $b->id = $building['id'];
            
            $slot = 0;
            $available = false;
            do
            {
                $slot++;
                $available = self::getUniqueValueFromDB( "select count(*) from building where category = {$b->category} and player_id = {$this->player_id} and location = {$slot}") == 0
                && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$b->category} and player_id = {$this->player_id} and (location = {$slot} or location = {$slot}-1)") == 0;
            }
            while(!$available && $slot<=3);
            
            if($slot<=3)
            {
                if(strpos($this->extra, "fromres") === false)
                {
                    $cost = $this->player->path->getBuildingCost($b->category, $slot, $this->worker_type);
                    $costs = $this->player->minusCost($cost, $reducs);
                    
                    if($this->player->checkCosts($costs))
                    {
                        $ret['selectable']['building_'.$b->id] = array();
                    }
                }
            }
        }
        
        $target1 = array();
        $target2 = array();
        for($cat=1;$cat<=4;$cat++)
        {
            $slot1 = self::getUniqueValueFromDB( "select count(*) from building where category = {$cat} and player_id = {$this->player_id} and (location = 1 or location = 2)") == 0
            && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$cat} and player_id = {$this->player_id} and (location = 1 or location = 2)") == 0;
            $slot2 = self::getUniqueValueFromDB( "select count(*) from building where category = {$cat} and player_id = {$this->player_id} and (location = 3 or location = 2)") == 0
            && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$cat} and player_id = {$this->player_id} and (location = 1 or location = 2)") == 0;
            
            
            if($slot1 || $slot2)
            {                
                $breakthroughs = self::getCollectionFromDb( "select * from breakthrough where player_id = {$this->player_id}");
                $superprojects = self::getCollectionFromDb( "select * from superproject where player_id IS NULL and id = {$this->player->chronology} and id = {$this->player->chronology}" );
                foreach($superprojects as $super_id => $superproject)
                {
                    $class = "superproject".$superproject["type"];
                    $sp = new $class();
                    $sp->id = $superproject['id'];
                    
                    $cost = $sp->getCost($this->worker_type);
                    $costs = $this->player->minusCost($cost, $reducs);
                    
                    if($this->player->checkCosts($costs) && $sp->checkBreakthroughCost($breakthroughs, false))
                    {
                       
                        if($slot1)
                        {                            
                            $target1[] = "phbuilding{$cat}1_{$this->player_id}";
                        }
                        
                        if($slot2)
                        {
                            $target2[] = "phbuilding{$cat}2_{$this->player_id}";                            
                        }
                    }
                }
            }  
        }
        
        if(count($target1)>0 || count($target2)>0)
        {
            $ret['selectable']['superproject_'.$sp->id] = array();
            $ret['selectable']['superproject_'.$sp->id]['titleyou'] = clienttranslate('${you} must select where to construct the Superproject');
            $ret['selectable']['superproject_'.$sp->id]['target'] = $target1;
            
            
            if(count($target1)==0  || ($this->player->path->type == DOMINANCE && anachrony::$instance->getGameStateValue( 'board_side') == SIDEB))
            {
                $ret['selectable']['superproject_'.$sp->id]['target'] = array_merge($target1, $target2);
            }
        }          
        
        if($this->extra != null)
        {
         $ret['buttons'][] = 'Skip';
         $ret['selectable']['Skip'] = array();
        }
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    
    public function do($parg1, $parg2, $varg1, $varg2) {
        
        if($varg1 != null && $varg1 != "Skip")
        {            
            $reducs = [0];
            $blocked = $this->getBlocked();
            if($blocked != null)
            {
                $reducs = $blocked->getCostBonus();
            }
            else if($this->player->hasActiveSuperProject(5))
            {
                $reducs = [G,U,T];
            }
            
            if (strpos($varg1, 'superproject') === 0) {
                
                $superproject_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
                $superproject = self::getObjectFromDB( "select * from superproject where id=".$superproject_id );
                $category = substr($varg2, 10,1);
                $slot = substr($varg2, 11,1);
                
                $class = "superproject".$superproject["type"];
                $sp = new $class();
                $sp->id = $superproject['id'];
                $cost = $sp->getCost($this->worker_type);
                $costs = $this->player->minusCost($cost, $reducs);
                
                self::DbQuery("update superproject set player_id = {$this->player_id}, category= {$category}, location = '{$slot}' where id = {$sp->id}");
                anachrony::$instance->notifyAllPlayers( "move", clienttranslate('${player_name} constructs Superproject <b>${superproject_name}</b>'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player->player_name,
                    'superproject_name' => $sp->name,
                    'mobile' => 'superproject_'.$sp->id,
                    'parent' => "phbuilding{$category}{$slot}_{$this->player_id}"
                ) );
                
                $sp->whenBuild();
                
                $this->boost($varg1);
                anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "construct", "breakthroughcost",$this->extra, $superproject_id);
                anachrony::$instance->addPending($this->player_id, null, "pay", json_encode($this->player->filterCosts($costs)));               
            }
            else
            {
                $building_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
                $building = self::getObjectFromDB( "select * from building where id=".$building_id );
                $name = "building".$building['type'];
                $b = new $name();
                $b->type = $building['type'];
                $b->category = $building['category'];
                $b->id = $building['id'];
                
                $slot = 0;
                $available = false;
                do
                {
                    $slot++;
                    $available = self::getUniqueValueFromDB( "select count(*) from building where category = {$b->category} and player_id = {$this->player_id} and location = {$slot}") == 0
                    && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$b->category} and player_id = {$this->player_id} and (location = {$slot} or location = {$slot}-1)") == 0;
                }
                while(!$available && $slot<=3);
                                
                $cost = $this->player->path->getBuildingCost($b->category, $slot, $this->worker_type);
                $costs = $this->player->minusCost($cost, $reducs);
                
                self::DbQuery("update building set player_id = {$this->player_id}, location = '{$slot}' where id = {$building_id}");
                anachrony::$instance->notifyAllPlayers( "move", clienttranslate('${player_name} constructs <b>Building ${building_type}</b>'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player->player_name,
                    'building_type' => $b->type,
                    'mobile' => 'building_'.$building_id,
                    'parent' => "phbuilding{$b->category}{$slot}_{$this->player_id}"
                ) );
                
                $b->whenBuild();
                
                $this->boost($varg1);
                anachrony::$instance->addPending($this->player_id, null, "pay", json_encode($this->player->filterCosts($costs)));
                
                if($building['location'] == 1)
                {
                    $toreveal_id = self::getUniqueValueFromDB("select id from building where category = {$b->category} and location = 1 and location_arg = 2 and player_id IS NULL");
                    if($toreveal_id != null)
                    {
                        anachrony::$instance->notifyAllPlayers( "revealbuilding", "", array(
                            'building' => self::getObjectFromDB( "SELECT * FROM building where id = ".$toreveal_id)
                        ) );
                        
                        self::DbQuery("update building set location_arg = location_arg - 1 where location = 1 and player_id IS NULL and category = {$b->category}");
                        
                        anachrony::$instance->setGameStateValue( 'no_undo', 1);
                    }
                }
                else
                {
                    self::DbQuery("update building set location_arg = location_arg - 1 where location = 2 and player_id IS NULL and category = {$b->category}");
                 }
            }
            $this->player->updateScore();
            
        }
    }
    
    function argbreakthroughcost($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose how to pay breakthroughs');
        $ret['titleyou'] = clienttranslate('${you} must choose how to pay breakthroughs');
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        $superproject_id = (int) filter_var($parg1, FILTER_SANITIZE_NUMBER_INT);
        $superproject = self::getObjectFromDB( "select * from superproject where id=".$superproject_id );
        
        $class = "superproject".$superproject["type"];
        $sp = new $class();
        $sp->id = $superproject['id'];
        
        $used = null;
        if($parg2 != null)
        {
            $used = self::getObjectFromDb( "select * from breakthrough where id = {$parg2}");
        }
        
        $breakthroughs = self::getCollectionFromDb( "select * from breakthrough where player_id = {$this->player_id}");
        $breakthroughs2 = self::getCollectionFromDb( "select * from breakthrough where player_id = {$this->player_id}");
        foreach($breakthroughs as $bt_id => $bt)
        {
            if($sp->checkBreakthroughCost([ $bt ], true))
            {
                $ret['selectable']['breakthrough_'.$bt['id']] = array();
            }
            else
            {
                foreach($breakthroughs2 as $bt_id2 => $bt2)
                {
                    if($bt['id'] != $bt2['id'] && $sp->checkBreakthroughCost([$bt, $bt2], true))
                    {
                        if(!array_key_exists('breakthrough_'.$bt['id'],$ret['selectable']))
                        {
                            $ret['selectable']['breakthrough_'.$bt['id']] = array();
                            $ret['selectable']['breakthrough_'.$bt['id']]['titleyou'] = clienttranslate('${you} must select the second breakthrough');
                            $ret['selectable']['breakthrough_'.$bt['id']]['target'] = array();
                        }
                        $ret['selectable']['breakthrough_'.$bt['id']]['target'][] = 'breakthrough_'.$bt2['id'];
                    }
                }
            }
        }
        
        return $ret;
    }
    
    function dobreakthroughcost($parg1, $parg2, $varg1, $varg2) {
        
        $bt_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        
        self::DbQuery("update breakthrough set location = 'hidden', player_id = NULL where id = {$bt_id}");
        anachrony::$instance->notifyAllPlayers( "move", '', array(
            'mobile' => 'breakthrough_'.$bt_id,
            'parent' => "hidden"
        ) );
        
        if($varg2 != null)
        {
            $bt_id = (int) filter_var($varg2, FILTER_SANITIZE_NUMBER_INT);
            self::DbQuery("update breakthrough set location = 'hidden', player_id = NULL where id = {$bt_id}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'breakthrough_'.$bt_id,
                'parent' => "hidden"
            ) );
        }
    }
}