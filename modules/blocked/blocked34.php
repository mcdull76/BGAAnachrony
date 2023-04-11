<?php 

class blocked34 extends blocked
{ 
    public function execute($arg)
    {
        $act = new recruit();
        $act->player_id = $this->player_id;
        $act->player = $this->player;
        $act->gain($arg);
    }
}