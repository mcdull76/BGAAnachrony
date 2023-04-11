<?php 

class council extends action
{     
    public $needExosuit = true;  
    public $maxindex = 2;    
    
    public function getActionCost($index = 0)
    {
        $ret = 3-$index - ($this->player->hasActiveSuperProject(4)?1:0);
        return W*(max(0,$ret));
    }  
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return parent::canDo($worker, $index, $ignorefull)
        && ($index == 1 || count(anachrony::$instance->getFullCapitalActions())>0);        
    }
    
    public function init($parg1, $parg2, $varg1, $varg2)
    {
        parent::init($parg1, $parg2, $varg1, $varg2);
        
        $index =  (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        if($index == 1) {
            //First player changes
            $path_to_delete = self::getUniqueValueFromDB( "select id from path where location = 'phfirstplayer'");
            self::DbQuery("delete from path where id = {$path_to_delete}");
            anachrony::$instance->notifyAllPlayers( "remove", '', array(
                'id' => 'path_'.$path_to_delete
            ) );
            
            self::DbQuery("INSERT INTO path (player_id, path, location) VALUES ({$this->player_id},{$this->player->path->type},'phfirstplayer')");
            $path = self::getObjectFromDB( "select * from path where location = 'phfirstplayer'");
            anachrony::$instance->notifyAllPlayers( "newpath", '', array(
                'path' => $path
            ) );
        }
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose a Capital action to copy');
        $ret['titleyou'] = clienttranslate('${you} must choose a Capital action to copy');
        
        foreach(anachrony::$instance->getFullCapitalActions() as $action)
        {
            $block = $action == "research" && ($this->worker_type == ENGINEER || $this->worker_type == ADMIN);
            $block = $block || ( $action == "recruit" && $this->worker_type == SCIENTIST);
            $block = $block || ( $action == "construct" && $this->worker_type == ADMIN);
            
            if(!$block)
            {
                $ret['selectable']['capitalaction_'.$action] = array();
            }
        }
        
        if(count($ret['selectable']) == 0)
        {
            $ret['title'] = clienttranslate('${actplayer} has no Capital action to copy');
            $ret['titleyou'] = clienttranslate('${you} have no Capital action to copy');
            $ret['buttons'][] = 'Confirm';
            $ret['selectable']['Confirm'] = array();
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2 = null) {
        if($varg1 != "Confirm")
        {
            $action = explode("_", $varg1)[1];
            anachrony::$instance->addPending($this->player_id, $this->worker_id, $action, $parg1, $parg2);
        }
    }
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Council'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}