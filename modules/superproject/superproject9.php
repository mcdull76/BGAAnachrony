<?php 

class superproject9 extends superproject
{ 
    public $cost = U+U+T+T+G+G;
    public $vp = 4;
    public $fixedCostShape = 3;
    public $fixedCostIcon = 5;
    public $variableShape1 = 1;
    public $variableShape2 = 2;
    public $name = "Outback conditioner";
    public $actionCost = 2*W;
    
    function arg($parg1, $parg2)
    {
        $ret = array();
        $ret['selectable'] = array();
        $ret['buttons'] = array();
        $ret['title'] = clienttranslate('${actplayer} must choose a Capital action to copy');
        $ret['titleyou'] = clienttranslate('${you} must choose a Capital action to copy');
        
        $worker = self::getObjectFromDB( "SELECT* FROM worker where id = ".$this->worker_id);
        
        foreach(["research","recruit","construct"] as $action)
        {
            $act = new $action();
            if($act->canDo($worker))
            {
                $ret['selectable']['capitalaction_'.$action] = array();
            }
        }
        
        if(count($ret['selectable']) == 0)
        {
            $ret['title'] = clienttranslate('${actplayer} has no Capital action to copy');
            $ret['titleyou'] = clienttranslate('${you} have no Capital action to copy');
            $ret['buttons'][] = 'Confirm';
            $ret['selectable']['Confirm'] = array();
        }
        
        $ret['buttons'][] = 'Undo';
        $ret['selectable']['Undo'] = array();
        return $ret;
    }
    
    function do($parg1, $parg2, $varg1, $varg2 = null) {
        if($varg1 != "Confirm")
        {
            $action = explode("_", $varg1)[1];
            anachrony::$instance->addPending($this->player_id, $this->worker_id, $action, $parg1, $parg2);
        }
    }
}