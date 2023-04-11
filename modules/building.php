<?php 

for($cat = 1; $cat<=4;$cat++)
{
    for($dist = 1; $dist<=15;$dist++)
    {
        $i = $cat*100+$dist;
        include("building/{$cat}00/building{$i}.php");
    }
}
include("building/building515.php");

class building extends action
{
    public $category = 0;
    public $type = 0;
    public $id = 0;
    public $vp = 0;
    
    public function __construct()
    {
        parent::__construct();
        $this->type = (int) filter_var(get_class($this), FILTER_SANITIZE_NUMBER_INT);
        $this->category = intdiv($this->type,100);
    }
    
    public function whenBuild()
    {
        
    }
    
    public function getVP()
    {
        return $this->vp;
    }    
    
    function isCleanupAwake($workertype)
    {
        return $workertype == SCIENTIST && $this->player->path->type == PROGRESS && anachrony::$instance->getGameStateValue( 'board_side') == SIDEB
        && ($this->category == 1 || $this->category == 2 || $this->category == 4);
    }
    
    function log()
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('${player_name} uses <b>Building ${nb}</b>'),
            array(
                "player_id" => $this->player->player_id,
                "player_name" => $this->player->player_name,
                "nb" => $this->type
            )
            );
    }
}

