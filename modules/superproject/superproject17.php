<?php 

class superproject17 extends superproject
{ 
    public $cost = N+U+T+G;
    public $vp = 5;
    public $fixedCostShape = 0;
    public $fixedCostIcon = 0;
    public $variableShape1 = 3;
    public $variableShape2 = 3;
    public $name = "Quantum chameleon";
    public $workerTypeRequired = GENIUS;
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must select a building or a superproject to copy');
        $ret['titleyou'] = clienttranslate('${you} must select a building or a superproject to copy');
    
        $worker = self::getObjectFromDb("select * from worker where id={$this->worker_id}");
        
        $buildings = self::getCollectionFromDb("select * from building where player_id IS NOT NULL");
        foreach($buildings as $building)
        {
            $actionname = "building".$building['type'];
            $action = new $actionname();
            
            $action->worker_id = $this->worker_id;
            $action->worker_type = $this->worker_type;  
            
            if($building['type'] != 515)
            {
                $action->slotname = $action->slotname."_".$building['id'];
                if(!$action->isFree() && $action->canDo($worker, null, true) && $this->player->checkCost($action->getActionCost()))
                {
                    $ret['selectable']["building_".$building['id']] = array();
                }
            }
        }
        
        $buildings = self::getCollectionFromDb("select * from superproject where player_id IS NOT NULL");
        foreach($buildings as $building)
        {
            $actionname = "superproject".$building['type'];
            $action = new $actionname();
            if($building['type'] != 17)
            {
                if(!$action->isFree() && $action->canDo($worker, null, true))
                {
                    $ret['selectable']["superproject_".$building['id']] =array();
                }
            }
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2)
    {
        $b_id = (int) filter_var($varg1, FILTER_SANITIZE_NUMBER_INT);
        $type = explode("_",$varg1)[0];
        $b_type = self::getUniqueValueFromDB( "select type from {$type} where id = ${b_id}");
        $action = $type.$b_type;
        $act = new $action();        
        
        $act->worker_id = $this->worker_id;
        $act->worker_type = $this->worker_type;        
        $cost = $act->getActionCost("");
        if($cost >0)
        {
            $this->player->pay(null,null, $cost);
        }
        anachrony::$instance->addPending($this->player_id, $this->worker_id, $action, null, null);
    }
    
}