<?php 

class leader3 extends action
{      
    public $freeAction = true;
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return $this->player->leader == 1 && $this->player->path->type == HARMONY && parent::canDo($worker, $index, $ignorefull);
    }    
    
    function do($parg1, $parg2, $varg1, $varg2) {
        
        anachrony::$instance->addPending($this->player_id, null, "actionRound","workeronly");
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