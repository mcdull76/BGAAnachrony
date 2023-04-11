<?php 

class superproject1 extends superproject
{ 
    public $cost = T+T+G+G+W+W+W+W;
    public $vp = 6;
    public $fixedCostShape = 2;
    public $fixedCostIcon = 3;
    public $variableShape1 = 1;
    public $variableShape2 = 3;
    public $name = "Welfare Society";
    
    public $actionCost = W;
    public $workerTypeRequired = ADMIN;
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        $this->player->gain(null,null,M);
    }
}