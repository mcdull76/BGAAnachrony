<?php 

class superproject16 extends superproject
{ 
    public $cost = U+T+T+G+SC;
    public $vp = 3;
    public $fixedCostShape = 0;
    public $fixedCostIcon = 0;
    public $variableShape1 = 1;
    public $variableShape2 = 1;
    public $name = "The ultimate plan";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}