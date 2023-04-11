<?php 

class superproject12 extends superproject
{ 
    public $cost = T+T+G+G+W+W;
    public $vp = 6;
    public $fixedCostShape = 2;
    public $fixedCostIcon = 5;
    public $variableShape1 = 1;
    public $variableShape2 = 3;
    public $name = "Dark matter converter";
    public $freeAction = true;
        
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, null, "gain", json_encode([GE,N,E]));
        anachrony::$instance->addPending($this->player_id, null, "payWorker", 0);
    }
}