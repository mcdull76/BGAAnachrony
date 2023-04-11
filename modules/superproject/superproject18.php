<?php 

class superproject18 extends superproject
{ 
    public $cost = U+U+T+T+W+W+W+W;
    public $vp = 6;
    public $fixedCostShape = 0;
    public $fixedCostIcon = 0;
    public $variableShape1 = 2;
    public $variableShape2 = 2;
    public $name = "Cloning vat";
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        self::DbQuery("INSERT INTO worker (player_id, type, location) VALUES ({$this->player_id},{$this->worker_type}, 'sleeping_{$this->player_id}')");
        $worker = self::getObjectFromDB( "SELECT* FROM worker order by id desc limit 1");
        anachrony::$instance->notifyAllPlayers( "newworker", '', array(
            'worker' => $worker
        ) );
    }
}