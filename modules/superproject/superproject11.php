<?php 

class superproject11 extends superproject
{ 
    public $cost = U+U+T+T+T;
    public $vp = 6;
    public $fixedCostShape = 2;
    public $fixedCostIcon = 2;
    public $variableShape1 = 1;
    public $variableShape2 = 3;
    public $name = "Exocrawler";
    public $freeAction = true;
    
    function do($parg1, $parg2, $varg1, $varg2) {
        
        anachrony::$instance->addPending($this->player_id, null, "actionRound","exoonly");
    }
}