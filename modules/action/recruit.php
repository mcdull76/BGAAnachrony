<?php 

class recruit extends action
{     
    public $needExosuit = true;
    
    function __construct( )
    {
        parent::__construct();
        $this->maxindex = self::getUniqueValueFromDB("select count(*) from player")>3?3:2;
    }
    
    public function getActionCost($index = 0)
    {
        if(anachrony::$instance->getGameStateValue( "after_impact") == 1)
        {
            return 0;
        }
        
        return W*max(0,$index - 1  - ($this->player->hasActiveSuperProject(4)?1:0));
    }     
    
    public function canDo($worker, $index = "", $ignorefull = false)
    {
        return parent::canDo($worker, $index, $ignorefull)
        && ($worker['type'] != SCIENTIST || ( SCIENTIST == $worker['type'] && $this->player->canUseSCasGE()));
    }
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must select a Worker to recruit');
        $ret['titleyou'] = clienttranslate('${you} must select a Worker to recruit');
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        
        $workers = self::getCollectionFromDb( "select max(id) as id, type from worker where location like 'phrecruitworker%' group by type" );
        foreach($workers as $worker_id => $worker)
        {
            if($this->worker_type != ENGINEER || $worker['type'] != GENIUS)
            {
                $ret['selectable']['worker_'.$worker['id']] = array();
            }
        }
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2) {
        
        
        $worker_id =  (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        $worker =  self::getObjectFromDB( "select * from worker where id=".$worker_id );    
        
        self::DbQuery("update worker set player_id = {$this->player_id}, location = 'awake_{$this->player_id}' where id = {$worker_id}");
        anachrony::$instance->notifyAllPlayers( "move", clienttranslate('${player_name} recruits one <b>${worker_name}</b>'), array(
            'player_id' => $this->player_id,
            'player_name' => $this->player->player_name,
            'worker_name' => $worker['type'],
            'mobile' => 'worker_'.$worker_id,
            'parent' => "awake_{$this->player_id}"
        ) );
        
        $this->boost($worker['type']);
        $this->gain($worker['type']);
    }   
    
    function gain($worker_type)
    {
        switch($worker_type)
        {
            case SCIENTIST:
                $this->player->gain(null,null,W+W);
                break;
            case ENGINEER:
                $this->player->gain(null,null,E);
                break;
            case ADMIN:
                $this->player->gain(null,null,VP);
                break;
            case GENIUS:
                anachrony::$instance->addPending($this->player_id, null, "gain", json_encode([W+W,E,VP]));
                break;
        }
    }
}