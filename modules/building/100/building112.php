<?php 

class building112 extends building
{
    public $vp = 1;
      
    public $variablecost = [W];
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {        
        anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "variablecost",0, json_encode($this->variablecost),0);
     }
    
    function argvariablecost($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must start to pay to time travel');
        $ret['titleyou'] = clienttranslate('${you} must start to pay to time travel');                   
        
        $time = anachrony::$instance->getGameStateValue( 'turn') - $parg2 - $this->extra;
        if($time>1)
        {
            foreach(json_decode($parg1) as $id => $cost)
            {
                if($this->player->checkCost($cost)){                  
                    $ret['buttons'][] = 'res'.$cost;
                    $ret['selectable']['res'.$cost] = array();
                }
            }
            if($this->player->hasActiveBuilding(401) && $this->extra != 1 && $this->extra != 3 )
            {
                $ret['buttons'][] = 'usebuilding401';
                $ret['selectable']['usebuilding401'] = array();
            }
        }
        
        if($this->player->hasActiveBuilding(402) && $this->extra != 2 && $this->extra != 3 && $time>2)
        {
            $ret['buttons'][] = 'usebuilding402';
            $ret['selectable']['usebuilding402'] = array();
        }        
        
        if($parg2>0 || $this->extra>0)
        {
            $ret['title'] = clienttranslate('${actplayer} must choose to continue to pay or to time travel in era ${nb}');
            $ret['titleyou'] = clienttranslate('${you} must choose to continue to pay or to time travel in era ${nb}');
            $ret['buttons'][] = 'Travel';
            $ret['selectable']['Travel'] = array();
            $ret['nb'] = $time; 
            $ret['highlight'] = "chronology".$time;
        }
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    function dovariablecost($parg1, $parg2, $varg1, $varg2)
    {
        if($varg1 == "usebuilding401")
        {
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "variablecost",$this->extra+1, $parg1,$parg2);
        }
        else if($varg1 == "usebuilding402")
        {
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "variablecost",$this->extra+2, $parg1,$parg2);
        }
        else if($varg1 == "Travel")
        {
            $time = anachrony::$instance->getGameStateValue( 'turn') - $parg2 - $this->extra;
            $this->player->backintime(null, null, "chronology".$time);
            
            $gain = VP;
            if(get_class($this) == "building113")
            {
                $gain = VP * $parg2;
            }
            $this->player->gain(null,null,$gain);
        }
        else
        {
            $this->player->pay(null,null,$varg1);
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, get_class($this), "variablecost",$this->extra, $parg1,$parg2+1);
        }
    }
}