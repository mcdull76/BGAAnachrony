<?php 

class trade extends action
{     
    public $needExosuit = true;
    public $multispace = true;
        
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose resources to trade from');
        $ret['titleyou'] = clienttranslate('${you} must choose resources to trade from');
        
        $filtered = $this->player->filterCosts(array_keys(anachrony::$instance->trades));
        
        if($parg2 != null)
        {
            $filtered = $this->player->filterCosts(array_keys(anachrony::$instance->tradesSP));
        }
        
        foreach( $filtered as $cost)
        {
            $ret['buttons'][] = 'res'.$cost;
            $ret['selectable']['res'.$cost] = array();
        }
        
        
        $ret['buttons'][] = 'Skip';
        $ret['selectable']['Skip'] = array();
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2 = null)
    {
        if($varg1 != "Skip")
        {
            $cost =  (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
            anachrony::$instance->addPendingSub($this->player_id, $this->worker_id, "trade", "tradeto",$this->extra, $cost, $parg2);
        }
    }
    
    function argtradeto($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose resources to trade to');
        $ret['titleyou'] = clienttranslate('${you} must choose resources to trade to');
        
        $filtered = anachrony::$instance->trades[$parg1];
        
        if($parg2 != null)
        {
            $filtered = anachrony::$instance->tradesSP[$parg1];
        }
        
        foreach( $filtered as $cost)
        {
            $ret['buttons'][] = 'res'.$cost;
            $ret['selectable']['res'.$cost] = array();
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    function dotradeto($parg1, $parg2, $varg1, $varg2 = null)
    {
        $this->player->pay(null,null,$parg1);
        $to =  (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        $this->player->gain(null,null,$to);
        
        if($this->worker_type == ADMIN || $this->worker_type == GENIUS || ( SCIENTIST == $this->worker_type && $this->player->canUseSCasGE()))
        {
            anachrony::$instance->addPending($this->player_id, null, "trade");
        }
    }
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Trade'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}