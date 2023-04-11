<?php


class ANAPlayer extends APP_GameClass
{    
    
    public $path;
    
    public function __construct($player_id)
    {
        $this->player_id = $player_id;
        $p = self::getObjectFromDB("SELECT * FROM player WHERE player_id = {$player_id}");        
        $classname = "path".$p['path'];
        $this->path = new $classname();
        $this->path->player_id = $p['player_id'];
        $this->path->player = $this;        
        $this->leader = $p['leader'];
        $this->player_no = $p['player_no'];
        $this->player_id = $p['player_id'];
        $this->player_name = $p['player_name'];
        $this->player_score = $p['player_score'];
        $this->vp = $p['vp'];
        $this->chronology = $p['chronology'];
        $this->moral = $p['moral'];
        $this->temporal = $p['temporal'];
        $this->paradox = $p['anomalies'];
        $this->evacuation_side = $p['evacuation_side'];
        $this->resources = array();
        for($i=1;$i<=6;$i++)
        { 
            $this->resources[$i] = $p['res'.$i];;
        }
    }
    
    function vortex()
    {
        $turn = anachrony::$instance->getGameStateValue('turn');
        $available_index = self::getUniqueValueFromDB( "select coalesce(MAX(location_arg), 0) from vortex where location = {$turn}")+1;
        
        $vortexs = self::getCollectionFromDb( "select * from vortex where chosen = 1 and player_id = ".$this->player_id );
        foreach($vortexs as $vortex_id => $vortex)
        {            
            self::DbQuery("update vortex set location = '{$turn}',location_arg = {$available_index} where id = ".$vortex['id']);
            
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'vortex_'.$vortex['id'],
                'parent' => "phchronology".$turn.$available_index
            ) );
            $available_index++;
            
            if(anachrony::$instance->vortexs[$vortex['type']][0]>0)
            {
                $this->pay(null,null,anachrony::$instance->vortexs[$vortex['type']][0]);
            }
            if(anachrony::$instance->vortexs[$vortex['type']][1]>0)
            {
                $this->gain(null,null,anachrony::$instance->vortexs[$vortex['type']][1]);
            }
        }
        $this->updateScore();
    }
    
    function argpowerup()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('3. Power up phase : ${actplayer} may power up Exosuits or finish this phase');
        $ret['titleyou'] = clienttranslate('3. Power up phase : ${you} may power up Exosuits or finish this phase');
     
        
        if($this->path->type == DOMINANCE && $this->leader == 1)
        {
            if($this->checkCost(2*W))
            {
                $ret['buttons'][] = 'leader41';
                $ret['selectable']['leader41'] = array();
            }
            if($this->checkCost(E))
            {
                $ret['buttons'][] = 'leader42';
                $ret['selectable']['leader42'] = array();
            }
        }
        
        $ret['buttons'][] = 'Finish';
        $ret['selectable']['Finish'] = array();
        
        if(self::getUniqueValueFromDB( "select count(*) from exosuit where location like 'phpower%_{$this->player_id}'")>0)
        {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        
        for($i=1;$i<=6;$i++)
        {        
            if(self::getUniqueValueFromDB( "select count(*) from exosuit where location = 'phpower{$i}_{$this->player_id}'") == 0
            && self::getUniqueValueFromDB( "select count(*) from blocked where location = 'phpower{$i}_{$this->player_id}'") == 0)
            {
                $costs = $this->path->getPowerupCost($i);
                $costs = $this->filterCosts($costs);
                if(count($costs)>0)
                {
                    $ret['selectable']["phpower".$i."_".$this->player_id] = array(); 
                }
            }
        }
        return $ret;
    }
    
    function paradox($parg1, $parg2, $varg1, $varg2) {
        
        $list = array();
        $cumul = 0;
        $max = $this->path->getParadoxMax() - $this->paradox;
        for($i=0;$i<$parg1 && $cumul<$max;$i++)
        {
            $paradox = anachrony::$instance->paradoxes[bga_rand(0,5)];
            $list[] = $paradox;
            $cumul += $paradox;
        }        
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} rolls ${paradox}'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player_name,
            'paradox' => implode(',', $list)
        ) );
        if($cumul>0)
        {
            $this->gain(null,null,$cumul*P);
        } 
    }
    
    function powerup($parg1, $parg2, $varg1, $varg2) {
                
        if($varg1 != null)
        {
            if(strpos($varg1, 'leader') === 0)
            {
                if(strpos($varg1, 'leader41') === 0)
                {
                    $this->pay(null,null,2*W);
                    $this->gain(null,null,E);
                }
                else
                {
                    $this->pay(null,null,E);
                    $this->gain(null,null,2*W);                    
                }
                anachrony::$instance->addPending($this->player_id, null, "powerup");                
            }
            else if($varg1 == "Finish")
            {
                $nbfrees = self::getUniqueValueFromDB( "select count(*) from exosuit where location = 'hidden{$this->player_id}'");
               
                $nbfrees -= self::getUniqueValueFromDB( "select count(*) from blocked where location like 'phpower%_{$this->player_id}'");
                
                $waterbonus = $this->path->getPowerupBonus() * $nbfrees;
                if($waterbonus > 0)
                {       
                    $this->gain(null,null,$waterbonus * W);
                }
                
                if(self::getUniqueValueFromDB( "select count(*) from exosuit where player_id = {$this->player_id} and location <> 'hidden{$this->player_id}'") == 0)
                {
                    anachrony::$instance->addPending($this->player_id, null, "confirmationPass");
                }
                
            }
            else
            {
                $index = $varg1[7];
                $exosuit_id = self::getUniqueValueFromDB( "select min(id) from exosuit where location = 'hidden{$this->player_id}'");            
                anachrony::$instance->addPending($this->player_id, null, "powerup");
                anachrony::$instance->addPendingGame("moveExosuit", $exosuit_id, $varg1);
                anachrony::$instance->addPending($this->player_id, null, "pay", json_encode($this->filterCosts($this->path->getPowerupCost($index))));
            }
        }
    }
    
    function checkCosts($costs)
    {        
        foreach($costs as $cost)
        {
            if($this->checkCost($cost))
            {
                return true;
            }
        }        
        return false;
    }
    
    function checkCost($cost, $awakeonly = false)
    {
        $ok = true;
        for($i=1;$i<=6 && $ok;$i++)
        {
            if((intdiv($cost, pow(10,$i)) % 10) > $this->resources[$i])
            {
                $ok =false;
            }
        }
        for($i=7;$i<=10 && $ok;$i++)
        {
            $needed = (intdiv($cost, pow(10,$i)) % 10);
            if($needed>0)
            {
                $type = $i-6;
                
                $loc = "(location = 'awake_{$this->player_id}' or location='sleeping_{$this->player_id}')";
                if($awakeonly)
                {
                    $loc = "(location = 'awake_{$this->player_id}')";
                }
                
                $nbWorkers = self::getUniqueValueFromDB( "select count(*) from worker where type = {$type} and {$loc}");  
                
                if($needed>$nbWorkers)
                {
                    $ok = false;
                }
            }
        }
        $i=11;
        {
            $needed = (intdiv($cost, pow(10,$i)) % 10);
            if($needed>0)
            {
                $exosuit_id = self::getUniqueValueFromDB( "select min(id) from exosuit where location like 'phpower%' and player_id = {$this->player_id}");
                if($exosuit_id == null)
                {
                    $ok = false;
                }
            }
        }
        return $ok;
    }
    
    function filterCosts($costs)
    {
        $filtered = array();
        
        foreach($costs as $c => $cost)
        {
            if($this->checkCost($cost))
            {
                $filtered[] = $cost;
            }
        }
        return $filtered;
    }
        
    function minusCost($cost, $reducs)
    {
        $filtered = array();
        
        foreach($reducs as $c2 => $reduc)
        {
            $after = 0;
            for($type=1;$type<=14;$type++)
            {
                $ct = intdiv($cost, pow(10,$type)) % 10;
                $rt = intdiv($reduc, pow(10,$type)) % 10;
                if($ct-$rt>0)
                {
                    $ct -= $rt;
                }
                else
                {
                    $ct = 0;
                }
                $after += pow(10,$type)*$ct;
            }
            
            if(!in_array($after, $filtered))
            {
                $filtered[] = $after;
            }
            
        }
        return $filtered;
    }
    
    function arggain($parg1)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose what to gain');
        $ret['titleyou'] = clienttranslate('${you} must choose what to gain');
        
        foreach(json_decode($parg1) as $id => $cost)
        {
            $ret['buttons'][] = 'res'.$cost;
            $ret['selectable']['res'.$cost] = array();
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();   
        
        return $ret;
        
    }
    
    function gain($parg1, $parg2, $varg1, $varg2 = null) {
        
        if($varg1 != null)
        {
            $paradox = false;
            $varg1 = preg_replace("/[^0-9\.]/", '', $varg1);
            
            for($type=1;$type<=14;$type++)
            {
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                if($nb>0)
                {
                    if($type<=6)
                    {
                        $this->resources[$type] += $nb;
                        self::DbQuery("update player set res{$type} = ".$this->resources[$type]." where player_id = ".$this->player_id);
                        
                        anachrony::$instance->notifyAllPlayers( "counter", '', array(
                            'id' => "res_".$this->player_id."_{$type}",
                            'nb' => $this->resources[$type]
                        ) );
                    }
                    else if($type<=10)
                    {
                        $typeW = $type - 6;
                        self::DbQuery("INSERT INTO worker (player_id, type, location) VALUES ({$this->player_id},{$typeW}, 'awake_{$this->player_id}')");                        
                        $worker = self::getObjectFromDB( "SELECT* FROM worker order by id desc limit 1");
                        anachrony::$instance->notifyAllPlayers( "newworker", '', array(
                             'worker' => $worker
                        ) );
                    }
                    else if($type==11){
                        $exosuit_id = self::getUniqueValueFromDB( "select min(id) from exosuit where location = 'hidden{$this->player_id}'"); 
                        
                        if($exosuit_id != null)
                        {
                            $ph_id = 0;
                            for($i =1;$i<=6 && $ph_id == 0;$i++)
                            {
                                if(self::getUniqueValueFromDB( "select count(*) from exosuit where location = 'phpower{$i}_{$this->player_id}'") == 0
                                && self::getUniqueValueFromDB( "select count(*) from blocked where location = 'phpower{$i}_{$this->player_id}'") == 0)
                                {
                                    $ph_id = $i;
                                }
                            }
                            
                            if($ph_id != 0)
                            {
                                self::DbQuery("update exosuit set location = 'phpower{$ph_id}_{$this->player_id}' where id = {$exosuit_id}");
                                anachrony::$instance->notifyAllPlayers( "move", '', array(
                                    'mobile' => 'exosuit_'.$exosuit_id,
                                    'parent' => "phpower{$ph_id}_{$this->player_id}"
                                ) );
                            }
                        }
                    }
                    else if($type==12){
                        $this->vp+= $nb;
                        self::DbQuery("UPDATE player SET vp = {$this->vp} where player_id = {$this->player_id}");                        
                        anachrony::$instance->notifyAllPlayers( "counter", '', array(
                            'id' => "vp_".$this->player_id,
                            'nb' => $this->vp
                        ) );                        
                        $this->updateScore();
                    }
                    else if($type==13){
                        $this->paradox+= $nb;
                        self::DbQuery("UPDATE player SET anomalies = {$this->paradox} where player_id = {$this->player_id}");   
                        anachrony::$instance->notifyAllPlayers( "setparadox", '', array(
                            'player_id' => $this->player_id,
                            'nb' => $this->paradox
                        ) );  
                        $paradox = true;
                    }
                    else if($type==14){
                        for($i=0;$i<$nb;$i++)
                        {                            
                            if($this->moral == 7)
                            {
                                $this->gain(null,null,VP*$this->path->getMoraleVP(7));
                            }
                            else
                            {
                                $this->moral++;
                                self::DbQuery("update player set moral = moral + 1 where player_id = {$this->player_id}");
                                anachrony::$instance->notifyAllPlayers( "move", '', array(
                                    'mobile' => 'moral_'.$this->player_id,
                                    'parent' => "moral_{$this->player_id}_{$this->moral}"
                                ) );
                                
                                $this->updateScore();
                            }                            
                        }
                    }
                }
            }
            
            
            if($varg1 > 0)
            {
                anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} gains ${cost}'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name,
                    'cost' => $varg1
                ) );
            }
            
            if($paradox)
                $this->checkParadox();
            
        }
    }
    
    function argpay($parg1)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose how to pay');
        $ret['titleyou'] = clienttranslate('${you} must choose how to pay'); 
        
        foreach(json_decode($parg1) as $id => $cost)
        {
            $ret['buttons'][] = 'res'.$cost;
            $ret['selectable']['res'.$cost] = array();            
        }
        return $ret;
        
    }
    
    function pay($parg1, $parg2, $varg1, $varg2 = null) {
                
        if($varg1 != null)
        {
            $varg1 = preg_replace("/[^0-9\.]/", '', $varg1);
            
            for($type=1;$type<=6;$type++)
            {
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                if($nb>0)
                {
                    $this->resources[$type] -= $nb;
                    self::DbQuery("update player set res{$type} = ".$this->resources[$type]." where player_id = ".$this->player_id); 
                    
                    anachrony::$instance->notifyAllPlayers( "counter", '', array(
                        'id' => "res_".$this->player_id."_{$type}",
                        'nb' => $this->resources[$type]
                    ) );
                }
            }
            for($type=7;$type<=10;$type++)
            {
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                if($nb>0)
                {
                    anachrony::$instance->addPending($this->player_id, null, "payWorker", $type-6, $parg2);
                }
            }
            $type=11;
            {
                $needed = (intdiv($varg1, pow(10,$type)) % 10);
                if($needed>0)
                {
                    $exosuit_id = self::getUniqueValueFromDB( "select min(id) from exosuit where location like 'phpower%' and player_id = {$this->player_id}");
                    if($exosuit_id != null)
                    {
                        self::DbQuery("update exosuit set location = 'hidden{$this->player_id}' where id = {$exosuit_id}");
                        anachrony::$instance->notifyAllPlayers( "move", '', array(
                            'mobile' => 'exosuit_'.$exosuit_id,
                            'parent' => "hidden{$this->player_id}"
                        ) );                        
                    }
                }
            }
            $type =12;
            {
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                $this->vp-= $nb;
                self::DbQuery("UPDATE player SET vp = {$this->vp} where player_id = {$this->player_id}");
                anachrony::$instance->notifyAllPlayers( "counter", '', array(
                    'id' => "vp_".$this->player_id,
                    'nb' => $this->vp
                ) );
                $this->updateScore();
            }
            
            $type = 13;
            {
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                if($this->paradox>= 0)
                {
                    $this->paradox-= $nb;
                    if($this->paradox<0)
                    {
                        $this->paradox = 0;
                    }
                    self::DbQuery("UPDATE player SET anomalies = {$this->paradox} where player_id = {$this->player_id}");
                    anachrony::$instance->notifyAllPlayers( "setparadox", '', array(
                        'player_id' => $this->player_id,
                        'nb' => $this->paradox
                    ) ); 
                }
            }
            
            $type = 14;
            {                
                $nb = intdiv($varg1, pow(10,$type)) % 10;
                for($i=0;$i<$nb;$i++)
                {
                    if($this->moral == 1)
                    {
                        if(!$this->hasActiveSuperProject(10))
                        {
                            anachrony::$instance->addPending($this->player_id, null, "payWorker", 0);
                        }
                    }
                    else
                    {
                        $this->moral--;
                        self::DbQuery("update player set moral = moral - 1 where player_id = {$this->player_id}");
                        anachrony::$instance->notifyAllPlayers( "move", '', array(
                            'mobile' => 'moral_'.$this->player_id,
                            'parent' => "moral_{$this->player_id}_{$this->moral}"
                        ) );
                        
                        $this->updateScore();
                    }
                }
            }
            
            if($varg1 > 0)
            {
                anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} pays ${cost}'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name,
                    'cost' => $varg1
                ) );
            }
        }
    }
    
    function checkParadox() {
        if($this->paradox>=$this->path->getParadoxMax())
        {
            anachrony::$instance->notifyAllPlayers( 'simplePause', '', [ 'time' => 500] );
            
            $this->paradox = 0;
            self::DbQuery("UPDATE player SET anomalies = {$this->paradox} where player_id = {$this->player_id}");
            anachrony::$instance->notifyAllPlayers( "setparadox", clienttranslate('${player_name} : An anomaly appears'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name,
                'nb' => $this->paradox
            ) );
            
            if($this->path->type == HARMONY && anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
            {                
                anachrony::$instance->addPendingSub($this->player_id, null, "path3", "specialAnomaly",null);
            }
            else
            {
                anachrony::$instance->addPending($this->player_id, null, "anomaly");                
            }
            if(self::getUniqueValueFromDB( "select count(*) from vortex where location <> 0 and player_id = ".$this->player_id )>0)
            {
                anachrony::$instance->addPending($this->player_id, null, "removeVortex");
            }
        }
    }
    
    function argremoveVortex()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may remove a warp tile');
        $ret['titleyou'] = clienttranslate('${you} may remove a warp tile');
        
        
        $vortexs = self::getCollectionFromDb( "select * from vortex where location <> 0 and player_id = ".$this->player_id );
        foreach($vortexs as $vortex_id => $vortex)
        {
            $ret['selectable']['vortex_'.$vortex['id']] = array();
        }
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        return $ret;
    }
    
    function removeVortex($parg1, $parg2, $varg1, $varg2 = null) {
        if(strpos($varg1, 'vortex') === 0)
        {
            $vortex_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            self::DbQuery("update vortex set location = 0 where id = ".$vortex_id);
            
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'vortex_'.$vortex_id,
                'parent' => "vortexs_{$this->player_id}"
            ) );
        }
    }
    
    function argbackintime($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may time travel');
        $ret['titleyou'] = clienttranslate('${you} may time travel');
                
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        $turn = anachrony::$instance->getGameStateValue( 'turn');
        $nbback = $parg1;
        
        for($i=max(1,$turn-$nbback);$i<$turn;$i++)
        {
            $ret['selectable']['chronology'.$i] = array();
        }
        
        return $ret;
    }
    
    function backintime($parg1, $parg2, $varg1, $varg2 = null)
    {        
        $chrono = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        self::DbQuery("update player set chronology = {$chrono} where player_id = {$this->player_id}");        
        anachrony::$instance->notifyAllPlayers( "move", '', array(
            'mobile' => "path_{$this->player_id}",
            'parent' => "chronologyTokens".($chrono)
        ) );
        
        anachrony::$instance->addPending($this->player_id, null, "refund");
        
    }
    
    function arganomaly()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must place an anomaly');
        $ret['titleyou'] = clienttranslate('${you} must place an anomaly');
                
        $minslot = 4;
        $target = array();
        
        if($this->path->type == SALVATION && anachrony::$instance->getGameStateValue( 'board_side') == SIDEB)
        {
            for($cat=1;$cat<=4;$cat++)
            {  
                for($slot=1;$slot<=3;$slot++)
                {            
                    $available = self::getUniqueValueFromDB( "select count(*) from building where category = {$cat} and player_id = {$this->player_id} and location = {$slot}") == 0
                    && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$cat} and player_id = {$this->player_id} and (location = {$slot} or location = {$slot}-1)") == 0;
                    if($available)
                    {
                        $ret['selectable']["phbuilding{$cat}{$slot}_{$this->player_id}"] = array();
                    }
                }
            }
        }
        if(count( $ret['selectable']) == 0)
        {
            for($cat=1;$cat<=4;$cat++)
            {            
                $slot = 0;
                $available = false;
                do
                {
                    $slot++;
                    $available = self::getUniqueValueFromDB( "select count(*) from building where category = {$cat} and player_id = {$this->player_id} and location = {$slot}") == 0
                    && self::getUniqueValueFromDB( "select count(*) from superproject where category = {$cat} and player_id = {$this->player_id} and (location = {$slot} or location = {$slot}-1)") == 0; 
                }
                while(!$available && $slot<=3);
                
                if($slot<$minslot)
                {
                    $minslot = $slot;
                    $target = array();
                }
                if($slot==$minslot)
                {
                    $target[] = "phbuilding{$cat}{$slot}_{$this->player_id}";
                }            
            }
            
            if($minslot<4)
            {
                foreach($target as $t_id => $t)
                {
                    $ret['selectable'][$t] = array(); 
                }        
            }
            else
            {
                $slot = 3;
                
                while($slot>=1 && count( $ret['selectable']) == 0)
                {
                    for($cat=1;$cat<=4;$cat++)
                    { 
                        if(self::getUniqueValueFromDB( "select count(*) from building where player_id = {$this->player_id} and category = {$cat} and location = {$slot}")==1)
                        {
                            $ret['selectable']["phbuilding{$cat}3_{$this->player_id}"] = array();
                        }
                    }
                    $slot--;
                }
            }
        }
        return $ret;
    }
    
    function anomaly($parg1, $parg2, $varg1, $varg2 = null) {
        
        $category = substr($varg1, 10,1);
        $slot = substr($varg1, 11,1);
        
        $anomalytype = ANOMALY;
        $id = self::getUniqueValueFromDB( "select max(id) from building") + 1;
        self::DbQuery("INSERT INTO building (player_id, category, type, location) VALUES ({$this->player_id}, {$category},{$anomalytype},{$slot})");
        $anomaly = self::getObjectFromDB( "SELECT* FROM building where id = {$id}");
        
        anachrony::$instance->notifyAllPlayers( "newanomaly", '', array(
            'player_id' => $this->player_id,
            'anomaly' => $anomaly
        ) );
    }
    
    function argrefund()
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} may retrieve a warp tile');
        $ret['titleyou'] = clienttranslate('${you} may retrieve a warp tile');
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        $vortexs = self::getCollectionFromDb( "select * from vortex where location = {$this->chronology} and player_id = ".$this->player_id );
        foreach($vortexs as $vortex_id => $vortex)
        {
            $cost = anachrony::$instance->vortexs[$vortex['type']][1];
            if($this->checkCost($cost, true))
            {   
                $ret['selectable']['vortex_'.$vortex['id']] = array();
            }
        }
        return $ret;        
    }
    
    
    function refund($parg1, $parg2, $varg1, $varg2 = null) {
        if($varg1 != "Skip")
        {
            $vortex_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            $vortex = self::getObjectFromDb( "select * from vortex where id = ".$vortex_id);
            $cost = anachrony::$instance->vortexs[$vortex['type']][1];
            
            $this->pay(null,"awakeonly",$cost);                
            
            
            self::DbQuery("update vortex set location = '0' where id = ".$vortex['id']);
            
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'vortex_'.$vortex['id'],
                'parent' => "vortexs_{$this->player_id}"
            ) );
            
            if($this->temporal<9)
            {
                $this->temporal++;
                
                self::DbQuery("update player set temporal = temporal + 1 where player_id = {$this->player_id}");
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'temporal_'.$this->player_id,
                    'parent' => "temporal_{$this->player_id}_{$this->temporal}"
                ) );
                
                anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} gains <div class="mineres anaicon res15"></div>'), array(
                    'player_id' => $this->player_id,
                    'player_name' => $this->player_name
                ) );
                
                $this->updateScore();
            }
        }
    }
    
    function argpayWorker($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must select a Worker to pay with');
        $ret['titleyou'] = clienttranslate('${you} must select a Worker to pay with');
                
        if($parg1 == 0)
        {
            $add = "";
        }
        else
        {
            $add = " and type= {$parg1}";
        }
        
        if($parg2 != null)
        {
            
            $add = $add." and location = 'awake_{$this->player_id}' limit 1";
        }
                
        $workers = self::getCollectionFromDb( "select * from worker where (location = 'awake_{$this->player_id}' or location = 'sleeping_{$this->player_id}') {$add}" );
        foreach($workers as $worker_id => $worker)
        {
            $ret['selectable']['worker_'.$worker['id']] = array();
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;
    }
    
    function payWorker($parg1, $parg2, $varg1, $varg2 = null) {
                
        $worker_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        
        if($worker_id != 0)
        {
            self::DbQuery("delete from worker where id = {$worker_id}");
            anachrony::$instance->notifyAllPlayers( "remove", '', array(
                'id' => 'worker_'.$worker_id
            ) );
        }
    }
    
    function argconfirmation($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('5. Action rounds phase : ${actplayer} must confirm its action');
        $ret['titleyou'] = clienttranslate('5. Action rounds phase : ${you} must confirm your action');        
        
        $ret['buttons'][] = 'Confirm';
        $ret['selectable']['Confirm'] = array();        
        
        if(anachrony::$instance->getGameStateValue( 'no_undo') == 0)
        {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        return $ret;        
    }
    
    
    function argconfirmationPass($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must confirm it want to pass');
        $ret['titleyou'] = clienttranslate('${you} must confirm you want to pass');
        
        $ret['buttons'][] = 'Confirm';
        $ret['selectable']['Confirm'] = array();
        
        if(anachrony::$instance->getGameStateValue( 'no_undo') == 0)
        {
            $ret['buttons'][] = 'Undo';
            $ret['selectable']['Undo'] = array();
        }
        return $ret;
    }
    
    function confirmation($parg1, $parg2, $varg1, $varg2) {
        
    }
    function confirmationPass($parg1, $parg2, $varg1, $varg2) {
        
    }
    
    function argactionRound($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('5. Action rounds phase : ${actplayer} may take an action');
        $ret['titleyou'] = clienttranslate('5. Action rounds phase : ${you} may take an action');
        
        $workeronly = $parg1 == "workeronly";
        $exoonly = $parg1 == "exoonly";

        if($workeronly || $exoonly)
        {
            $ret['buttons'][] = 'Skip';
            $ret['selectable']['Skip'] = array();
        }
        else
        {
            $ret['buttons'][] = 'Pass';
            $ret['selectable']['Pass'] = array();
        }        
        
        $exosuit_id = self::getUniqueValueFromDB( "select id from exosuit where location like 'phpower%_{$this->player_id}' order by location limit 1");
        $workers = self::getCollectionFromDb( "select * from worker where location='awake_{$this->player_id}'" );
        
        if(!$exoonly)
        {        
            $buildings = self::getCollectionFromDb("select * from building where player_id = ".$this->player_id);
            foreach($buildings as $building)
            {
                $actionname = "building".$building['type'];
                $action = new $actionname();
                if($building['type'] == 515)
                {
                    $action->slotname = $action->slotname."_".$building['id'];
                }
                if(!$workeronly || !$action->isFree())
                {
                    $ret['selectable'] = array_merge($ret['selectable'],$action->getAvailableSlots($exosuit_id, $workers));
                }
            }
            
            $buildings = self::getCollectionFromDb("select * from superproject where player_id = ".$this->player_id);
            foreach($buildings as $building)
            {
                $actionname = "superproject".$building['type'];
                $action = new $actionname();
                if(!$workeronly || !$action->isFree())
                {
                    $ret['selectable'] = array_merge($ret['selectable'],$action->getAvailableSlots($exosuit_id, $workers));
                }
            }
        }
        
        foreach(anachrony::$instance->actions as $actionname)
        {
            $action = new $actionname();
            
            if(!$exoonly || $action->needExosuit)
            {
                if(!$workeronly || !($action->needExosuit || $action->isFree()))
                {            
                    $ret['selectable'] = array_merge($ret['selectable'],$action->getAvailableSlots($exosuit_id, $workers));
                }
            }
        }   
        
        if(count($ret['selectable'] ) == 1 && $ret['buttons'][0] == 'Pass')
        {
            $ret['title'] = clienttranslate('5. Action rounds phase : ${actplayer} has no action left');
            $ret['titleyou'] = clienttranslate('5. Action rounds phase : ${you} have no action left');
        }
        
        return $ret;
    }      
    
    function actionRound($parg1, $parg2, $varg1, $varg2) {
        if($varg1 != "Pass" && $varg1 != "Skip")
        {
            $workeronly = $parg1 == "workeronly";
            $exoonly = $parg1 == "exoonly";
                        
           $action = substr(explode("_",$varg1)[0], 2);
           $act = new $action();
           if(!$workeronly && !$exoonly)
           {
               if($act->isFree())
               {
                   anachrony::$instance->addPending($this->player_id, null, "actionRound");
               }
               else
               {
                   anachrony::$instance->addPending($this->player_id, null, "confirmation");
                   anachrony::$instance->addPendingFirst($this->player_id, null, "actionRound");               
               }
           }
           $act->init($parg1, $parg2, $varg1, $varg2);            
        }
        else if($varg1 == "Pass")
        {
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} passes'), array(
                'player_id' => $this->player_id,
                'player_name' => $this->player_name
            ) );
            anachrony::$instance->addPending($this->player_id, null, "confirmationPass");
        }
    }
    
    function updateScore($final = false)
    {
        $ret = array();
        $ret[0] = $this->player_name;
        if(strlen($ret[0])>11)
        {
            $ret[0] = substr($ret[0],0,10).".";
        }
        
        $btot = 0;
        $anomalies = 0;
        $buildings = self::getObjectListFromDB("select concat('building', type) from building where player_id = ".$this->player_id , true);
        foreach($buildings as $name){
            $c = new $name();
            $c->player = $this;
            $c->player_id = $this->player_id;
            if($c->type == 515)
            {
                $anomalies += $c->getVP();
            }
            else
            {
                $btot += $c->getVP();
            }
        }
        $ret[1] = $btot;
        
        $supers = self::getObjectListFromDB("select concat('superproject', type) from superproject where player_id = ".$this->player_id , true);
        $stot = 0;
        foreach($supers as $name){
            $c = new $name();
            $c->player = $this;
            $c->player_id = $this->player_id;
            $stot += $c->getVP();
        }
        $ret[2] = $stot;
        $ret[3] = $anomalies;
        $ret[4] = $this->path->getTemporalVP($this->temporal-1);
        $ret[5] = $this->path->getMoraleVP($this->moral-1);
        $shapes = self::getObjectListFromDB("select count(*) as nb from breakthrough where player_id = {$this->player_id } group by shape",true);
        if(count($shapes)==3)
        {
            $ret[6] = min($shapes)*2;
        }
        else
        {
            $ret[6] = 0;
        }
        $ret[6] += self::getUniqueValueFromDB("select count(*) from breakthrough where player_id = {$this->player_id }");
        $ret[7] = $this->vp;
        $ret[8] = -2 * intval(self::getUniqueValueFromDB("select count(*) from vortex where player_id = {$this->player_id } and location <> 0"));
        $ret[9] = 0;
        
        $vpend = 0;
        if($final)
        {
            $i = 10;
            $players = self::getCollectionFromDb( "select * from player where player_id <> {$this->player_id}" );
            $endgames = self::getObjectListFromDB("select type from endgame" , true);
            foreach($endgames as $endgame){                
                $en = new endgame();
                if($en->hasMost($players, $this->player_id, $endgame))
                {
                    $ret[$i] = 3;
                    $vpend += 3;
                }
                else
                {
                    $ret[$i] = 0;
                }
                $i++;
            }
        }
        
        $this->player_score = 0;
        for($i=1;$i<count($ret);$i++)
        {
            $this->player_score += $ret[$i];
        }
        $ret[15] = $this->player_score;
        
        self::DbQuery("UPDATE player SET player_score = {$this->player_score} where player_id = {$this->player_id}");
        anachrony::$instance->notifyAllPlayers( "counterid", '', array(
            'id' => 'player_score_'.$this->player_id,
            'nb' => $this->player_score
        ) );        
        
        anachrony::$instance->setStat($ret[1], 'building', $this->player_id);
        anachrony::$instance->setStat($ret[2], 'superproject', $this->player_id);
        anachrony::$instance->setStat($ret[3], 'anomalies', $this->player_id);
        anachrony::$instance->setStat($ret[4], 'travel', $this->player_id);
        anachrony::$instance->setStat($ret[5], 'moral', $this->player_id);
        anachrony::$instance->setStat($ret[6], 'bt', $this->player_id);
        anachrony::$instance->setStat($ret[7], 'tokens', $this->player_id);
        anachrony::$instance->setStat($ret[8], 'warp', $this->player_id);
        anachrony::$instance->setStat($vpend,  'end', $this->player_id);
        return $ret;
    }
    
    public function initialSetup() {  
        
        $sql = "INSERT INTO vortex (player_id, path, type) VALUES ";
        $values = array();       
        for($i=1;$i<=9;$i++)
        {
            $values[] = "('".$this->player_id."','".$this->path->type."','".$i."')";
        }        
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        
        $sql = "INSERT INTO exosuit (player_id, path, location) VALUES ";
        $values = array();
        for($i=1;$i<=6;$i++)
        {
            $values[] = "('".$this->player_id."','".$this->path->type."','hidden".$this->player_id."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
                
        $ressources = "";
        for($i=1;$i<=6;$i++)
        {
            $bonus = 0;
            if($i == 5) //bonus Water
            {
                switch($this->player_no)
                {
                    case 2:
                    case 3:
                        $bonus = 1;
                        break;
                    case 4:
                        $bonus = 2;
                        break;
                }
            }
            $val = $this->path->initialResources[$i-1]+$bonus;
            $ressources = $ressources."res{$i} = {$val},";
        }
        self::DbQuery("update player set ".$ressources." player_no = player_no where player_id = ".$this->player_id);
        
        if($this->path->type == PROGRESS)
        {
            $b_id = bga_rand(1,45);
            self::DbQuery("update breakthrough set player_id = {$this->player_id}, location='breakthroughs_{$this->player_id}' where id = {$b_id}");
        }
        
        if(anachrony::$instance->getGameStateValue( 'board_side') == SIDEA) {
            $moral = $this->path->moralA;
        }
        else
        {
            $moral = $this->path->moralB;
        }
        self::DbQuery("update player set moral = {$moral} where player_id = ".$this->player_id);  
        
        foreach($this->path->sleepings as $id => $type)
        {
            self::DbQuery("INSERT INTO worker (player_id, type, location) VALUES ({$this->player_id},{$type}, 'sleeping_{$this->player_id}')");
        }
        foreach($this->path->awakes as $id => $type)
        {
            self::DbQuery("INSERT INTO worker (player_id, type, location) VALUES ({$this->player_id},{$type}, 'awake_{$this->player_id}')");
        }
        
        $evacuation_side = bga_rand(1,2);
        self::DbQuery("update player set evacuation_side = {$evacuation_side} where player_id = ".$this->player_id);  
        
    }
    
    function cleanUp()
    {
        $workers = self::getCollectionFromDb( "select * from worker where player_id = {$this->player_id} and  location<>'awake_{$this->player_id}' and location<>'sleeping_{$this->player_id}'" );
        foreach($workers as $id => $worker)
        {
            $location = $worker['location'];
            if (strpos($location, 'exosuit') === 0) {
                {
                    $exosuit_id = (int) filter_var($location, FILTER_SANITIZE_NUMBER_INT);
                    $location = self::getUniqueValueFromDB("select location from exosuit where id=".$exosuit_id);
                }
            }
            if(strpos($location, 'hidden') === 0)
            {
                self::DbQuery("update worker set location = 'sleeping_{$this->player_id}' where id = {$worker['id']}");
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'worker_'.$worker['id'],
                    'parent' => "sleeping_{$this->player_id}"
                ) );
            }
            else
            {           
                if(strpos($location, 'stack') !== false)
                {                    
                    $actionname = str_replace("stack","",$location); 
                }
                else
                {                    
                    $actionname = substr(explode("_",$location)[0], 2); 
                }
                $action = new $actionname();
                $target = "sleeping";
                
                if($action->isCleanupDead($worker['type']))
                {
                    self::DbQuery("delete from worker where id = {$worker['id']}");
                    anachrony::$instance->notifyAllPlayers( "remove", '', array(
                        'id' => 'worker_'.$worker['id']
                    ) );
                }
                else
                {
                    if($action->isCleanupAwake($worker['type']))            
                    {
                        $target = "awake";
                    }
                    self::DbQuery("update worker set location = '{$target}_{$this->player_id}' where id = {$worker['id']}");
                    anachrony::$instance->notifyAllPlayers( "move", '', array(
                        'mobile' => 'worker_'.$worker['id'],
                        'parent' => "{$target}_{$this->player_id}"
                    ) );
                }
            }
            
        }
        
    }
    
    function hasActiveBuilding($nb)
    {        
        $b = self::getObjectFromDB("select * from building where type = {$nb} and player_id = {$this->player_id}"); 
        if($b != null)
        {
            $anomaly = self::getObjectFromDB("select * from building where type = 515 and player_id = {$this->player_id} and location = {$b['location']} and category = {$b['category']}"); 
            return $anomaly == null;
        }
        return false;
    }
    
    
    function hasActiveSuperProject($nb)
    {
        $b = self::getObjectFromDB("select * from superproject where type = {$nb} and player_id = {$this->player_id}");
        if($b != null)
        {
            $anomaly = self::getObjectFromDB("select * from building where type = 515 and player_id = {$this->player_id} and (location = {$b['location']} or location = 1 + {$b['location']}) and category = {$b['category']}");
            return $anomaly == null;
        }
        return false;
    }
    
    function getBackintimeBonus()
    {
        $ret = 0;
        if($this->hasActiveBuilding(401))
        {
            $ret += 1;
        }
        if($this->hasActiveBuilding(402))
        {
            $ret += 2;
        }            
        return $ret;
    }
    
    function canUseSCasGE()
    {
        return $this->path->type == PROGRESS && $this->leader == 1;
    }
    
    function continuum()
    {
        $vortexs = self::getCollectionFromDb( "select * from vortex where location <> 0 and player_id = ".$this->player_id );
        foreach($vortexs as $vortex_id => $vortex)
        {
            $cost = anachrony::$instance->vortexs[$vortex['type']][1];
            if($this->checkCost($cost, true))
            { 
                $this->pay(null,"awakeonly",$cost);
                self::DbQuery("update vortex set location = '0' where id = ".$vortex['id']);
                
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'vortex_'.$vortex['id'],
                    'parent' => "vortexs_{$this->player_id}"
                ) );
            }
        }
    }
    
    
}