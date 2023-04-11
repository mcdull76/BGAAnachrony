<?php

for($j=1;$j<=3;$j++)
{
    for($i=1;$i<=5;$i++)
    {
        include("blocked/blocked{$j}{$i}.php");
    }
}

class blocked extends APP_GameClass
{
    public $player_id = 0;
    public $player;
    public $action; 
        
    public function __construct()
    {
        $this->name = get_class($this);
    }
    
    public function getCostBonus()
    {
        if($this->player->hasActiveSuperProject(5))
        {
            return [G,U,T];
        }
        return [0];
    }
    
    public function execute($arg)
    {
        return 0;
    }
}