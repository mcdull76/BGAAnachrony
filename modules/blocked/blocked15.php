<?php 

class blocked15 extends blocked
{ 
    public function execute($arg)
    {    
        $sql = "INSERT INTO pending (player_id, worker_id, function, extra) VALUES (".$this->player_id.", '".$this->action->worker_id."', 'construct', 'fromres')";
        self::DbQuery( $sql );
    }
}