<?php 

class superproject10 extends superproject
{ 
    public $cost = T+T+T+W+W+W+SC;
    public $vp = 5;
    public $fixedCostShape = 1;
    public $fixedCostIcon = 5;
    public $variableShape1 = 2;
    public $variableShape2 = 3;
    public $name = "Synthetic endorphins";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}