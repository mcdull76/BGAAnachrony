<?php 

class blocked23 extends blocked
{ 
    public function getCostBonus()
    {
        if($this->player->hasActiveSuperProject(5))
        {
            return [G+G,U+G,T+G,U+T,U+U,T+T];
        }
        return [G,U,T];
    }
}