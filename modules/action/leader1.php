<?php 

class leader1 extends action
{      
    public $freeAction = true;
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return $this->player->leader == 1 && $this->player->path->type == SALVATION && parent::canDo($worker, $index);
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose one action option');
        $ret['titleyou'] = clienttranslate('${you} must choose one action option');
        $ret['buttons'][] = 'leader11';
        $ret['selectable']['leader11'] = array();
        $ret['buttons'][] ='leader12';
        $ret['selectable']['leader12'] = array();
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;        
    }
    
    function do($parg1, $parg2, $varg1, $varg2) {
        if($varg1 == "leader11")
        {
            $this->player->gain(null,null,2*W+P);
        }
        else
        {
            $this->player->pay(null,null,2*W+P);            
        }
    }
    
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses its leader action'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}