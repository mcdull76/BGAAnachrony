<?php 

class building408 extends building
{
    public $vp = 2;
    
    function isCleanupAwake($workertype)
    {
        return parent::isCleanupAwake($workertype) || ($workertype == ADMIN || $workertype == GENIUS||  ( SCIENTIST == $workertype && $this->player->canUseSCasGE()));
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
    }
}