<?php 

class supply extends action
{     
    public $player_board = true;
    
    public function getActionCost($index = 0)
    {
        $cost = $this->player->path->getMoraleCost($this->player->moral-1);
        
        if($this->player->hasActiveBuilding(311) && $this->player->hasActiveBuilding(312))
        {
            $cost = 0;
        }
        else 
        {
            if($this->player->hasActiveBuilding(311))
            {
                $cost = ceil($cost/2);
            }
            if($this->player->hasActiveBuilding(312))
            {
                $cost = ceil($cost/2);
            }        
            $cost -= ($this->player->hasActiveSuperProject(4)?1:0);
        }
        
        return W*$cost;
    }  
    
    public function isFree()
    {
        return $this->player->path->type == DOMINANCE && anachrony::$instance->getGameStateValue( 'board_side') == SIDEB;
    }
    
    function isCleanupAwake($workertype)
    {
        return (anachrony::$instance->getGameStateValue( 'board_side') == SIDEA || $this->player->path->type == HARMONY || $this->player->path->type == SALVATION)
        && ($workertype == ADMIN || $workertype == GENIUS);
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $workers = self::getCollectionFromDb( "select * from worker where location='sleeping_{$this->player_id}'" );        
        foreach($workers as $worker_id => $worker)
        {
            //move workers
            self::DbQuery("update worker set location = 'awake_{$this->player_id}' where id = {$worker['id']}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'worker_'.$worker['id'],
                'parent' => "awake_{$this->player_id}"
            ) );
        }
        
        $this->player->gain(null,null,M);
    } 
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Supply'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}