<?php 

class superproject4 extends superproject
{ 
    public $cost = U+U+T+T+EN+W+W;
    public $vp = 6;
    public $fixedCostShape = 3;
    public $fixedCostIcon = 3;
    public $variableShape1 = 1;
    public $variableShape2 = 2;
    public $name = "Grand reservoir";
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
}