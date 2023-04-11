<?php 

class mine extends action
{     
    public $needExosuit = true;
    public $maxindex = 3;
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must take 1 Resource from the Mine pool');
        $ret['titleyou'] = clienttranslate('${you} must take 1 Resource from the Mine pool');
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        for($i=1;$i<=5;$i++)
        {
            $ret['selectable']['mineres_'.$i] = array();
        }
        
        return $ret;
    }    
    
    function do($parg1, $parg2, $varg1, $varg2 = null) {
        
        $exosuit_id =  (int) filter_var(self::getUniqueValueFromDB( "select location from worker where id = {$this->worker_id}"), FILTER_SANITIZE_NUMBER_INT);
        $index =  (int) filter_var(self::getUniqueValueFromDB( "select location from exosuit where id = {$exosuit_id}"), FILTER_SANITIZE_NUMBER_INT);
        
        $res_id =  (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        $res =  self::getObjectFromDB( "select * from mineres where id=".$res_id );
        $gain = pow(10,$res['type']);
        
        self::DbQuery("delete from mineres where id = {$res_id}");
        anachrony::$instance->notifyAllPlayers( "remove", '', array(
            'id' => 'mineres_'.$res_id
        ) );
        
        $bonus_id = 5 + $index;
        $bonus =  self::getObjectFromDB( "select * from mineres where id=".$bonus_id );
        $gain += pow(10,$bonus['type']);
        
        self::DbQuery("delete from mineres where id = {$bonus_id}");
        anachrony::$instance->notifyAllPlayers( "remove", '', array(
            'id' => 'mineres_'.$bonus_id
        ) );
        
        $this->player->gain(null,null,$gain);
        
        if($this->player->hasActiveSuperProject(13))
        {            
            anachrony::$instance->addPending($this->player_id, null, "gain", json_encode([G,U,T]));
        }
    }
    
    function isCleanupAwake($workertype)
    {
        return $workertype == ENGINEER || $workertype == GENIUS || ( SCIENTIST == $workertype && $this->player->canUseSCasGE());
    }
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Mine'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
    
}