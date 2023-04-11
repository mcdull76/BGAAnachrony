<?php 

class blocked35 extends blocked
{ 
    public function execute($arg)
    {
        $workers = self::getCollectionFromDb( "select * from worker where location='sleeping_{$this->action->player_id}'" );
        foreach($workers as $worker_id => $worker)
        {
            //move workers
            self::DbQuery("update worker set location = 'awake_{$this->action->player_id}' where id = {$worker['id']}");
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'worker_'.$worker['id'],
                'parent' => "awake_{$this->action->player_id}"
            ) );
        }
    }
}