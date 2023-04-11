<?php 

class blocked22 extends blocked
{ 
    public function getCostBonus()
    {
        if($this->player->hasActiveSuperProject(5))
        {
            return [G+N,U+N,T+N];
        }
        return [N];
    }
}