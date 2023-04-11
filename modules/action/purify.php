<?php 

class purify extends action
{     
    public $needExosuit = true;
    public $multispace = true;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $water = 3;
        if($this->worker_type == SCIENTIST || $this->worker_type == GENIUS)
        {
            $water++;
        }
        $this->player->gain(null,null, W*$water);
    }  
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Purify'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}