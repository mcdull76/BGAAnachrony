<?php 

class blocked31 extends blocked
{ 
    public function execute($arg)
    {
        if(strpos($this->action->extra, "supp") === false)
        {
            $function = get_class($this->action);
            $sql = "INSERT INTO pending (player_id, worker_id, function, extra) VALUES (".$this->player_id.", '".$this->action->worker_id."', '".$function."', 'supp')";
            self::DbQuery( $sql );
        }
    }
}