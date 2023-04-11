<?php 

class superproject8 extends superproject
{ 
    public $cost = N+T+T+T+W+W+W;
    public $vp = 6;
    public $fixedCostShape = 1;
    public $fixedCostIcon = 4;
    public $variableShape1 = 2;
    public $variableShape2 = 3;
    public $name = "Temporal tourism";
    public $freeAction = true;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, 0, "backintime", 3);
    }
}