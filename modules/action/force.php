<?php 

class force extends action
{     
    public $player_board = true;    
    public $freeAction = true;
    
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
        
        $this->player->pay(null,null,M);
        
    } 
    
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses Force'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
            )
            );
    }
}