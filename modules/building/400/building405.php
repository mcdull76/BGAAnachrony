<?php 

class building405 extends building
{
    public $vp = 3;
    
    public function canDo($worker, $index = "", $ignoreFull = false)
    {
        return false;
    }
    
    public function do($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->addPending($this->player_id, $this->worker_id, "backintime", 1);
    }
}