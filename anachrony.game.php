<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * anachrony implementation : © <Nicolas Gocel> <nicolas.gocel@gmail.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * anachrony.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

include('modules/anaplayer.php');
include('modules/path.php');
include('modules/action.php');
include('modules/building.php');
include('modules/superproject.php');
include('modules/blocked.php');
include('modules/endgame.php');

class anachrony extends Table
{
    
    public static $instance = null;
    
	function __construct( )
	{
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        
        self::initGameStateLabels( array(            
            "after_impact" => 10,
            "turn" => 11,
            "extra_cleanup" => 12,
            "finish" => 13,
            "no_undo" => 14,
            "board_side" => 100,
            "path_selection" => 101
        ) );
        
        self::$instance = $this;
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "anachrony";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue( 'after_impact', 0 );
        self::setGameStateInitialValue( 'turn', 1 );
        self::setGameStateInitialValue( 'extra_cleanup', 0 );
        self::setGameStateInitialValue( 'finish', 0 );
        self::setGameStateInitialValue( 'no_undo', 0 );
        
        // Init game statistics        
        self::initStat( 'table', 'turns_number', 1 );
        $this->initStat('player', 'building', 0);
        $this->initStat('player', 'superproject', 0);
        $this->initStat('player', 'anomalies', 0);
        $this->initStat('player', 'travel', 0);
        $this->initStat('player', 'moral', 0);
        $this->initStat('player', 'bt', 0);
        $this->initStat('player', 'tokens', 0);
        $this->initStat('player', 'warp', 0);
        $this->initStat('player', 'end', 0);
        
        // TODO: setup the initial game situation here
        
        if(self::getGameStateValue( 'path_selection') == 1)
        {
            foreach( $players as $player_id => $player )
            {
                $path = 0;
                do {
                    $path = bga_rand(1,4);
                }
                while(self::getUniqueValueFromDB( "select count(*) from player where path = ".$path)>0);
                
                self::DbQuery("UPDATE player SET path = {$path}, player_color = '{$this->player_colors[$path]}' where player_id = {$player_id}");
            }
        }
        self::reloadPlayersBasicInfos();
        
        $sql = "INSERT INTO breakthrough (shape, icon, location) VALUES ";
        $values = array();
        for($i=1;$i<=3;$i++)
        {
            for($shape=1;$shape<=3;$shape++)
            {
                for($icon=1;$icon<=5;$icon++)
                {
                    $values[] = "(".$shape.",".$icon.", 'hidden')";
                }
            }
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        
        for($category=1;$category<=4;$category++)
        {
            for($i=1;$i<=15;$i++)
            {
                $type = 0;
                do {
                    $type = 100*$category + bga_rand(1,15);
                }
                while(self::getUniqueValueFromDB( "select count(*) from building where type = ".$type)>0);
                
                self::DbQuery("INSERT INTO building (category, type, location, location_arg) VALUES ('".$category."','".$type."','1',$i)");
            }
        }
        
        for($i=1;$i<=5;$i++)
        {
            $type = 0;
            do {
                $type = bga_rand(1,8);
            }
            while(self::getUniqueValueFromDB( "select count(*) from endgame where type = ".$type)>0);
            
            self::DbQuery("INSERT INTO endgame (type) VALUES ('".$type."')");
        }
        
        for($i=1;$i<=7;$i++)
        {
            $type = 0;
            do {
                $type = bga_rand(1,18);
            }
            while(self::getUniqueValueFromDB( "select count(*) from superproject where type = ".$type)>0);
            
            self::DbQuery("INSERT INTO superproject (type) VALUES ('".$type."')");
        }
        
        self::DbQuery("update superproject set visible = 1 where id = 1");

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();
        
        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();
    
        $sql = "SELECT player_id id, player_score score, evacuation_side, player_color color, player_name name, vp, path, leader, chronology, evacuation, anomalies, temporal, moral, res1, res2, res3, res4, res5, res6 FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
        
        $result['side'] = self::getGameStateValue( 'board_side') == SIDEA?'A':'B';
        $result['after_impact'] = self::getGameStateValue( 'after_impact');
        
        $sql = "SELECT * FROM vortex ";
        $result['vortexs'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM path ";
        $result['paths'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM worker ";
        $result['workers'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM exosuit ";
        $result['exosuits'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM endgame ";
        $result['endgames'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM breakthrough ";
        $result['breakthroughs'] = self::getCollectionFromDb( $sql );
        
        $sql = "SELECT * FROM superproject ";
        $result['superprojects'] = self::getCollectionFromDb( $sql );
        
        foreach($result['superprojects'] as $project_id => $project)
        {
            if( $project['visible'] == 0)
            {
                $result['superprojects'][$project_id]['type'] = 19;
            }
        }
        
        $sql = "SELECT * FROM mineres ";
        $result['mineres'] = self::getCollectionFromDb( $sql );
                
        $sql = "SELECT * FROM building order by location_arg desc";
        $result['buildings'] = self::getObjectListFromDB( $sql );
        
        foreach($result['buildings'] as $building_id => $building)
        {
            if($building['player_id'] == null && $building['location'] == 1 &&  $building['location_arg'] > 1)
            {
                $result['buildings'][$building_id]['type'] = $building['category'] * 100 + 16;
            }
        }
               
        $sql = "SELECT * FROM blocked";
        $result['blocked'] = self::getObjectListFromDB( $sql );
                
        $result['setupAuto'] = self::getGameStateValue( 'path_selection') == 1?1:0;
        $result['setupDone'] = (self::getUniqueValueFromDB( "SELECT count(*) FROM player where path = 0")==0?1:0);
        
        
        $score = array();
        if(self::getGameStateValue( 'finish') == 1)
        {
            foreach($this->getPlayersOrdered() as $player)
            {
                $p = new ANAPlayer( $player['player_id'] );
                $score[] = $p->updateScore(true);
            }
        }
        $result['score'] = $score;
        
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        
        $turn =  self::getGameStateValue( 'turn');
        
        return min(100,($turn-1) * 15);
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    function getFullCapitalActions()
    {
        $ret = array();        
        $maxactions = (self::getUniqueValueFromDB("select count(*) from player")>3)?3:2;
        foreach(["research","recruit","construct"] as $action)
        {
            $nbexos = self::getUniqueValueFromDB( "select count(*) from exosuit where location like 'ph{$action}%'");
            $nbblock = self::getUniqueValueFromDB( "select count(*) from blocked where (type=0 or type%10=6) and location like 'ph{$action}%'");
                        
            if($nbexos+$nbblock>=$maxactions)
            {
                $ret[] = $action;
            }
            
        }
        
        return $ret;
    }
    
    function getPlayerRelativePositions()
    {
        $result = array();
        
        $players = self::loadPlayersBasicInfos();
        $nextPlayer = self::createNextPlayerTable(array_keys($players));
        
        $current_player = self::getCurrentPlayerId();
        
        if(!isset($nextPlayer[$current_player])) {
            // Spectator mode: take any player for south
            $player_id = $nextPlayer[0];
        }
        else {
            // Normal mode: current player is on south
            $player_id = $current_player;
        }
        $result[$player_id] = 0;
        
        for($i=1; $i<count($players); $i++) {
            $player_id = $nextPlayer[$player_id];
            $result[$player_id] = $i;
        }
        return $result;
    } 

    function checkArgs($arg1, $arg2)
    {       
        $ret = self::argPlayerTurn();
        
        if(!in_array($arg1,array_keys($ret['selectable'])) && !in_array($arg1,array_keys($ret['buttons'])))
        {
            throw new feException( "Not a valid move");
        }
        else if($arg2 != null && in_array($arg1,array_keys($ret['selectable'])) && !in_array($arg2,$ret['selectable'][$arg1]['target']))
        {
            throw new feException( "Not a valid target");
        }
    }
    
    function addPendingSub($player_id, $worker_id, $function, $target, $extra = NULL, $arg = NULL, $arg2 = NULL) {
        $sql = "INSERT INTO pending (player_id, worker_id, target_id, function, extra, arg, arg2) VALUES (".$player_id.", '".$worker_id."', '".$target."', '".$function."', '".$extra."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }
    
    function addPending($player_id, $worker_id, $function, $arg = NULL, $arg2 = NULL) {
        $sql = "INSERT INTO pending (player_id, worker_id, function, arg, arg2) VALUES (".$player_id.", '".$worker_id."', '".$function."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }
    
    function addPendingGame($function, $arg = NULL, $arg2 = NULL) {
        $sql = "INSERT INTO pending (function, arg, arg2) VALUES ('".$function."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }
    
    function addPendingFirst($player_id, $worker_id, $function, $arg = NULL, $arg2 = NULL) {
        $minid = self::getUniqueValueFromDB( "select min(id) from pending")-1;
        $sql = "INSERT INTO pending (id, player_id, worker_id, function, arg, arg2) VALUES (".$minid.",".$player_id.", '".$worker_id."', '".$function."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }
    
    function addPendingGameFirst($function, $arg = NULL, $arg2 = NULL) {
        $minid = self::getUniqueValueFromDB( "select min(id) from pending")-1;
        $sql = "INSERT INTO pending (id, function, arg, arg2) VALUES (".$minid.", '".$function."', '".$arg."', '".$arg2."')";
        self::DbQuery( $sql );
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    function actSelect($arg1, $arg2 )
    {
        self::checkAction( 'select' );        
        if($this->gamestate->state()['name'] == "selection")
        {
            $player_id = $this->getActivePlayerId();
            $path = (int) filter_var($arg1, FILTER_SANITIZE_NUMBER_INT);
            $leader = 0;
            
            self::DbQuery("update player set path = {$path}, player_color = '{$this->player_colors[$path]}', leader = {$leader} where player_id = {$player_id}");
            self::reloadPlayersBasicInfos();
            
            $p = new ANAPlayer($player_id);
            
            anachrony::$instance->notifyAllPlayers( "choosepath", clienttranslate('${player_name} chooses path : ${path}'), 
                array(
                    "player_id" => $p->player_id,
                    "player_name" => $p->player_name,
                    "path" => $p->path->name,
                    "pathnb" => $path,
                    "color"=> $this->player_colors[$path]
                )
            );
        }
        else if($this->gamestate->state()['name'] == "selectionLeader")
        {
            $player_id = $this->getActivePlayerId();
            $leader = substr($arg1, strlen($arg1)-1,1);
            
            self::DbQuery("update player set leader = {$leader} where player_id = {$player_id}");
            
            $p = new ANAPlayer($player_id);
            
            anachrony::$instance->notifyAllPlayers( "chooseleader", clienttranslate('${player_name} chooses its leader : ${leader}'),
                array(
                    "player_id" => $p->player_id,
                    "player_name" => $p->player_name,
                    "leader" => $p->path->leadernames[$leader-1],
                    "path" => $p->path->type,
                    "leadernb" => $leader,
                )
                );
        }
        else {
            self::checkArgs($arg1, $arg2 );        
            if($arg1 == "Undo")
            {
                $this->undoRestorePoint();
                $this->gamestate->nextState('next');
                return;
            }
            
            $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
            $this->callPending($pending, true, $arg1, $arg2);
            self::DbQuery("delete from pending where id=".$pending['id']);
            
        }
        $this->giveExtraTime(self::getActivePlayerId());
        $this->gamestate->nextState( 'next');  
                
    }

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////    
  
    function argPlayerTurn()
    {
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        $ret = $this->callPending($pending, false);
        
        if(in_array('Undo',$ret['buttons']) && self::getGameStateValue( 'no_undo') == 1)
        {
            unset($ret['selectable']['Undo']);
            $ret['buttons'] = array_diff($ret['buttons'], ['Undo']);
        }
        
        return $ret;
    } 
    
    function argSelection()
    {
        $ret = array();
        $ret['selectable'] = array();
        
        for($path=1;$path<=4;$path++)
        {
            if(self::getUniqueValueFromDB( "select count(*) from player where path = {$path}") == 0)
            {
                $ret['selectable']["selectrow{$path}"] = array();
                $ret['selectable']["selectrow{$path}"] = array();
            }
        }
        return $ret;
    }  
    
    function argSelectionLeader()
    {
        $player_id = $this->getActivePlayerId();
        $ret = array();
        $ret['selectable'] = array();
        $ret['selectable']["leader_".$player_id."_1"] = array();
        $ret['selectable']["leader_".$player_id."_2"] = array();
        return $ret;
    } 

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////


    function originalSetup()
    {      
        
        //Initial resources
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        foreach($players as $player_id => $player)
        {     
            $p = new ANAPlayer( $player['player_id'] );
            $p->initialSetup();
        }
        
        $firstplayer = self::getObjectFromDB("select * from player where player_no = 1");
        
        self::DbQuery("INSERT INTO path (player_id, path, location) VALUES ({$firstplayer['player_id']},{$firstplayer['path']},'phfirstplayer')");
        $this->addPendingGame("preparation");
    }   
    
    
    function moveExosuit($parg1, $parg2) {
        self::DbQuery("update exosuit set location = '{$parg2}' where id = {$parg1}");
        $this->notifyAllPlayers( "move", '', array(
            'mobile' => 'exosuit_'.$parg1,
            'parent' => $parg2
        ) );
    }
    
    function preparation() {
        
        //Determine Available Resources:
        self::DbQuery("delete from mineres");
        $index = 0;
        do {
            $index = bga_rand(1,11);
        }
        while(self::getUniqueValueFromDB( "select count(*) from resourcesdrawn where id = ".$index)>0);
        $resources = $this->cards_mine[$index];
        for($i=0;$i<5;$i++)
        {
            $res = $resources[$i];
            if(self::getGameStateValue( "after_impact") == 1 && $i==0)
            {
                $res = NEUTRONIUM;
            }
            
            self::DbQuery("INSERT INTO mineres (id, type) VALUES (".($i+1).",".$res.")");
        }
        self::DbQuery("INSERT INTO mineres (id, type) VALUES (6,".URANIUM.")");
        self::DbQuery("INSERT INTO mineres (id, type) VALUES (7,".GOLD.")");
        self::DbQuery("INSERT INTO mineres (id, type) VALUES (8,".TITANIUM.")");
        self::DbQuery("INSERT INTO resourcesdrawn (id) VALUES (".$index.")");
        
        //Determine Available Workers:
        self::DbQuery("delete from worker where player_id IS NULL");
        $index = 0;
        do {
            $index = bga_rand(1,11);
        }
        while(self::getUniqueValueFromDB( "select count(*) from recruitsdrawn where type = ".$index)>0);
        self::DbQuery("INSERT INTO recruitsdrawn (type) VALUES (".$index.")");
        $sql = "INSERT INTO worker (type, location) VALUES ";
        $values = array();
        $workers = $this->cards_worker[$index];
        for($type=0;$type<4;$type++)
        {            
            for($i=0; $i<$workers[$type]; $i++)
            {
                $values[] = "(".($type+1).",'phrecruitworker".($type+1)."')";
            }
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        
        //Reveal Superproject:
        $turn =  self::getGameStateValue( 'turn');
        self::DbQuery("update superproject set visible = 1 where id <= ".($turn+1));
        
        anachrony::$instance->notifyAllPlayers( "newturn", clienttranslate('Start of turn ${turn}'), array(
            'turn' => $turn,
            'superproject' => self::getObjectFromDB( "SELECT * FROM superproject where id = ".($turn+1)." limit 1"),
            'mineres' => self::getCollectionFromDb( "SELECT * FROM mineres "),
            'workers' => self::getCollectionFromDb( "SELECT * FROM worker where player_id IS NULL ")
        ) );
        
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate('1. Preparation Phase'), array());
        
        
        //Shift building offers:
        self::DbQuery("update building set location_arg = location_arg + 1 where location = 2 and player_id IS NULL ");
        for($category=1;$category<=4;$category++)
        {
            $tomove_id = self::getUniqueValueFromDB("select id from building where category = {$category} and location = 1 and location_arg = 1 and player_id IS NULL");
            if($tomove_id != null)
            {
                $toreveal_id = self::getUniqueValueFromDB("select id from building where category = {$category} and location = 1 and location_arg = 2 and player_id IS NULL");
                if($toreveal_id != null)
                { 
                    anachrony::$instance->notifyAllPlayers( "revealbuilding", "", array(
                        'building' => self::getObjectFromDB( "SELECT * FROM building where id = ".$toreveal_id)
                    ) );                    
                }
                
                self::DbQuery("update building set location = 2 where id = {$tomove_id}");  
                anachrony::$instance->notifyAllPlayers( "move", "", array(
                    'mobile' => "building_".$tomove_id,
                    'parent' => "phbuilding".$category."2_null",
                    'position' => 'last'
                ) );
            }
        }
        self::DbQuery("update building set location_arg = location_arg - 1 where location = 1 and player_id IS NULL ");
      
        
        //Program round phases
        $firstplayer_id =  self::getUniqueValueFromDB("select player_id from path where location = 'phfirstplayer'"); 
        $firstplayer_no = self::getUniqueValueFromDB("select player_no from player where player_id = {$firstplayer_id}"); 
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        $nbplayers= count($players);
        $playersordered = array();
        $nbRolls = array();
        foreach($players as $player_id => $player)
        {
            $playersordered[($player['player_no']-$firstplayer_no+$nbplayers)%$nbplayers] = $player; 
            $nbRolls[$player['player_id']] = 0;
        }  
        
        if($turn>1)
        {
            $this->addPendingGameFirst("note",clienttranslate('2. Paradox phase'));
            
            //Paradox
            for($i=1;$i<$turn;$i++)
            {
                if(self::getUniqueValueFromDB("select count(*) from vortex where location = {$i}")>0)
                {
                    $max = null;
                    $players = self::getCollectionFromDb("select player_id, count(*) as nb FROM `vortex` where location = {$i} group by player_id order by nb desc");
                    foreach($players as $player)
                    {
                        if($max == null)
                        {
                            $max = $player['nb'];
                        }
                        
                        if($max == $player['nb'] && $player['nb']>0)
                        {
                            $nbRolls[$player['player_id']]++;
                        }
                    }
                }
            }
            
            for($i=0;$i<$nbplayers;$i++)
            {
                if($nbRolls[$playersordered[$i]['player_id']]>0)
                {
                    $this->addPendingFirst($playersordered[$i]['player_id'], null, "paradox", $nbRolls[$playersordered[$i]['player_id']]);
                }
            }
        
        }
        
        
        $this->addPendingGameFirst("note",clienttranslate('3. Power up phase'));
        
        for($i=0;$i<$nbplayers;$i++)
        {
            $this->addPendingFirst($playersordered[$i]['player_id'], null, "powerup");
        }
        
        $this->addPendingGameFirst("note",clienttranslate('4. Warp phase'));
        
        for($i=0;$i<$nbplayers;$i++)
        {
            $this->addPendingFirst($playersordered[$i]['player_id'], null, "vortex");
        }
        
        $this->addPendingGameFirst("note",clienttranslate('5. Action rounds phase'));
        
        for($i=0;$i<$nbplayers;$i++)
        {
            $this->addPendingFirst($playersordered[$i]['player_id'], null, "actionRound");
        }
        
      
        self::setGameStateValue( 'extra_cleanup', 0);
    }
    
    function note($parg1, $parg2, $varg1, $varg2)
    {
        anachrony::$instance->notifyAllPlayers( "note", clienttranslate($parg1), array() );
    }
    
    function getPlayersOrdered()
    {        
        $firstplayer_id =  self::getUniqueValueFromDB("select player_id from path where location = 'phfirstplayer'");
        $firstplayer_no = self::getUniqueValueFromDB("select player_no from player where player_id = {$firstplayer_id}");
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        $nbplayers= count($players);
        $playersordered = array();
        $nbRolls = array();
        foreach($players as $player_id => $player)
        {
            $playersordered[($player['player_no']-$firstplayer_no+$nbplayers)%$nbplayers] = $player;
        }  
        return $playersordered;
    }
    
    function stTestAuto() {
        if(self::getGameStateValue( 'path_selection') == 1)
        {
            $this->originalSetup();
            $this->gamestate->nextState( 'auto' );
        }
    }
    
  
    function stPending() {
        
        $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
        if($pending == null)
        {
            if(self::getGameStateValue( 'extra_cleanup') == 0 )
            {                
                anachrony::$instance->notifyAllPlayers( "note", clienttranslate('6. Clean Up phase'), array() );
                self::setGameStateValue( 'extra_cleanup', 1);
                
                foreach($this->getPlayersOrdered() as $player)
                {
                    $p = new ANAPlayer( $player['player_id'] );
                    $p->path->addExtraCleanup();
                }  
                $this->gamestate->nextState( 'same' );  
                
            }
            else
            {
                $this->gamestate->nextState( 'next' ); 
            }
                       
        }
        else if($pending['function'] == "vortex")
        {
            $this->gamestate->setAllPlayersMultiactive();
            $this->gamestate->nextState( 'vortex' );              
        }
        else
        {
            $args = $this->callPending($pending, false);
            if($args == null || count($args['selectable']) == 0 )
            {
                //no args required, execute
                $this->callPending($pending, true);
                self::DbQuery("delete from pending where id=".$pending['id']);
                $this->gamestate->nextState( 'same' );  
            }
            else if(count($args['selectable']) == 1 && !array_key_exists('Pass',$args['selectable']) && !array_key_exists('Undo',$args['selectable']))
            {
                //AUTO PLAY IF ONLY ONE CHOICE
                foreach($args['selectable'] as $arg1 => $argnul)
                {                    
                    $this->callPending($pending, true, $arg1);
                }
                self::DbQuery("delete from pending where id=".$pending['id']);
                $this->gamestate->nextState( 'same' );  
            }
            else
            {
               
                $this->gamestate->changeActivePlayer( $pending['player_id']);
                
                if($pending["function"] == "actionRound" || $pending["target_id"] == "extraCleanup" || ($pending["function"] == "powerup" && self::getUniqueValueFromDB( "select count(*) from exosuit where location like 'phpower%_{$pending['player_id']}'")==0))
                {
                    $this->undoSavepoint( );
                    self::setGameStateValue( 'no_undo', 0);
                }
                
                //player input required
                $this->gamestate->nextState( 'player' ); 
            }            
        }
        
    }
    
    function stSetup() {        
        $this->gamestate->nextState( 'next' );
    }
    
    function argSetup()
    {
        return $this->getAllDatas();
    }
    
    function stSelectionNext() {
        $this->activeNextPlayer();
        if(self::getUniqueValueFromDB( "SELECT count(*) FROM player where path = 0") == 0)
        {
            $this->originalSetup();
            $this->gamestate->nextState( 'setup' );
        }
        else
        {
            $this->gamestate->nextState( 'next' );
        }
    }
    
    function stSelectionLeaderNext() {
        $this->activeNextPlayer();
        if(self::getUniqueValueFromDB( "SELECT count(*) FROM player where leader = 0") == 0)
        {
            $this->gamestate->nextState( 'setup' );
        }
        else
        {
            $this->gamestate->nextState( 'next' );
        }
    }
    
    function callPending($pending, $execute, $arg1 = null, $arg2 = null)
    {
        if(class_exists($pending['function'])){ 
            $obj = new $pending['function']();
            $obj->worker_id = $pending['worker_id'];
            $obj->extra = $pending['extra'];
            if($obj->worker_id != null)
            {
                $obj->worker_type = self::getUniqueValueFromDB( "SELECT type FROM worker where id = ".$obj->worker_id);
            }
            $obj->player_id = anachrony::$instance->getActivePlayerId();
            if($pending['player_id'] != null)
            {
                $obj->player_id = $pending['player_id'];
            }
            $obj->player = new ANAPlayer($obj->player_id);
            
            $method = "";
            if($pending['target_id'] != null)
            {
                $method = $pending['target_id'];
            }            
            if(!$execute)
            {
                $name = "arg".$method;      
            }
            else
            {
                $name = "do".$method;                
            }
            $ret = $obj->$name($pending['arg'], $pending['arg2'], $arg1, $arg2); 
        }
        else
        {
            $obj = $this;
            if($pending['player_id'] != null)
            {
                $obj = new ANAPlayer($pending['player_id']);
                if($pending['target_id'] != null)
                {
                    //todo go deeper
                }
            }
            
            $fname ="";
            if(!$execute)
            {
                $fname .= "arg";
            }
            $fname .= $pending['function'];
            
            $ret = null;        
            if(method_exists($obj, $fname))
            {
                $ret = $obj->$fname($pending['arg'], $pending['arg2'], $arg1, $arg2);
            }
        }
        return $ret;
    }   
    
    function actVortex($arg1) {        
        self::checkAction( 'vortex' );
        if($arg1 != "")
        {
            //check if player has enough water
            $water = self::getUniqueValueFromDB("select res".WATER." from player where player_id=".$this->getCurrentPlayerId());
            $nbWorkers = self::getUniqueValueFromDB("select count(*) from vortex where type>=6 and type <=8 and player_id = ".$this->getCurrentPlayerId()." and id in (".$arg1.")");
            $addedwater = self::getUniqueValueFromDB("select count(*) from vortex where type=5 and player_id = ".$this->getCurrentPlayerId()." and id in (".$arg1.")");
            
            if($water+$addedwater<$nbWorkers)
            {
                throw new feException("You need to have 1 water per worker selected");
            }
            
            self::DbQuery("update vortex set chosen = 1 where player_id = ".$this->getCurrentPlayerId()." and id in (".$arg1.")");         
        }
                
        if(count($this->gamestate->getActivePlayerList())==1)
        {            
            self::DbQuery("delete from pending where function = 'vortex'");
            
            $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
            foreach($players as $player_id => $player)
            {
                $p = new ANAPlayer( $player['player_id'] );
                $p->vortex();
            }
            self::DbQuery("update vortex set chosen = 0");  
            
        }
        $this->gamestate->setPlayerNonMultiactive( $this->getCurrentPlayerId(), 'next');
    }
    
    function stCleanup() {
        $turn =  self::getGameStateValue( 'turn');  
        
        //AAAAAA        
        self::DbQuery("delete from worker where player_id IS NULL");
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        foreach($players as $player_id => $player)
        {
            $p = new ANAPlayer( $player['player_id'] );
            $p->cleanUp();
        }     
        
        $exosuits = self::getCollectionFromDb( "select * from exosuit where location not like 'hidden%' and location not like 'phpower%'");
        foreach($exosuits as $id => $exosuit)
        {
            anachrony::$instance->notifyAllPlayers( "move", '', array(
                'mobile' => 'exosuit_'.$exosuit['id'],
                'parent' => "hidden".$exosuit['player_id']
            ) );
            
            $location = $exosuit['location'];
            $blocked = self::getObjectFromDb( "select * from blocked where location = '{$location}'");
            if($blocked != null)
            {
                $blocked['type'] = intdiv($blocked['type'],10)*10+6;
                self::DbQuery("update blocked set type = ".$blocked['type']." where id = ".$blocked['id']);
                anachrony::$instance->notifyAllPlayers( "returnblocked", '', array(
                    'blocked' => $blocked,
                ) );
            }
        }
        self::DbQuery("update exosuit set location = CONCAT('hidden', player_id) where location not like 'hidden%' and location not like 'phpower%'");
              
        $paths = self::getCollectionFromDb( "select * from path where location <> 'phfirstplayer' && location not like 'phevacuate%'");
        foreach($paths as $id => $path)
        {
            anachrony::$instance->notifyAllPlayers( "remove", '', array(
                'id' => 'path_'.$path['id']
            ) );
        }
        self::DbQuery("delete from path where location <> 'phfirstplayer' and location not like 'phevacuate%'");
        
        //BBBBBB
        if($turn == 4) //IMPACT
        {
            $this->impact();
        }
        
        //CCCCCCCCCCCCCC
        if($turn == 7 || (self::getGameStateValue( 'after_impact') ==1 && self::getUniqueValueFromDB("select count(*) from blocked where (type<>0 and type%10<>6)  ") == 0) )
        {
            anachrony::$instance->notifyAllPlayers( "note", clienttranslate('<b>END OF GAME</b>'), array());
            anachrony::$instance->notifyAllPlayers( "note", 'Untangle the continuum', array());
            
            foreach($this->getPlayersOrdered() as $player)
            {          
                $p = new ANAPlayer( $player['player_id'] );
                $p->continuum();
            }  
            
            //flush pending pay workers
            $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
            while($pending != null)
            {
                $this->callPending($pending, false);
                $args = $this->callPending($pending, false);
                foreach($args['selectable'] as $arg1 => $argnul)
                {                    
                    $this->callPending($pending, true, $arg1);
                }
                self::DbQuery("delete from pending where id=".$pending['id']);
                $pending =  self::getObjectFromDB( "SELECT* FROM pending order by id desc limit 1");
            }
            
            $score = array();
            foreach($this->getPlayersOrdered() as $player)
            {
                $p = new ANAPlayer( $player['player_id'] );
                $score[] = $p->updateScore(true);                
            } 
            for($i=0;$i<count($score);$i++)
            {
                for($j=0;$j<count($score[$i]);$j++)
                {
                    if($j != 9)
                    {
                        anachrony::$instance->notifyAllPlayers( "finalscore", '', array(
                            'i' => $i,
                            'j' => $j,
                            'score' => $score[$i][$j]
                        ) );
                    }
                }
            }
            self::setGameStateValue( 'finish', 1);
            
            $this->gamestate->nextState( 'end' );
        }
        else
        {
            $this->addPendingGame("preparation");      
            
            //next turn
            self::setGameStateValue( 'turn',  $turn+1);
            $this->incStat(1, 'turns_number');
        
            //DDDDDDDDD
            $exosuits = self::getCollectionFromDb( "select * from exosuit where location not like 'hidden%'");
            foreach($exosuits as $id => $exosuit)
            {
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'exosuit_'.$exosuit['id'],
                    'parent' => "hidden".$exosuit['player_id']
                ) );
            }
            self::DbQuery("update exosuit set location = CONCAT('hidden', player_id) where location not like 'hidden%'");
            
            
            self::DbQuery("update player set chronology = ".($turn+1));
            foreach($players as $player_id => $player)
            {
                anachrony::$instance->notifyAllPlayers( "move", '', array(
                    'mobile' => 'path_'.$player['player_id'],
                    'parent' => "chronologyTokens".($turn+1)
                ) );
            }
            
            $this->gamestate->nextState( 'next' );
        }
    }
    
    function impact()
    {
        self::setGameStateValue( 'after_impact',1);
        $nbplayers =  self::getUniqueValueFromDB("select count(*) from player"); 
        
        for($i=1;$i<=3;$i++)
        {
            $blocknb = [2,2,2,3];
            for($j=1;$j<=$blocknb[$nbplayers-1];$j++)
            {
                $index = 0;
                do {
                    $index = 10*$i + bga_rand(1,5);
                }
                while(self::getUniqueValueFromDB( "select count(*) from blocked where type = ".$index)>0);
                
                self::DbQuery("INSERT INTO blocked (location, type) VALUES ('ph".(anachrony::$instance->actions[$i-1])."_".$j."',".$index.")");
            }
        }
        
        $players = self::getCollectionFromDb( "select * from player order by player_no desc" );
        foreach($players as $player_id => $player)
        {
            $p = new ANAPlayer( $player['player_id'] );
            foreach($p->path->getPowerupBlocked() as $id)
            {
                self::DbQuery("INSERT INTO blocked (location, type) VALUES ('phpower{$id}_{$p->player_id}',0)");
            }
        }
        
        $blocks = self::getCollectionFromDb( "select * from blocked");
        anachrony::$instance->notifyAllPlayers( "impact", clienttranslate('<b>IMPACT</b>'), array(
            'blocked' =>$blocks
        ) );
    }
    
//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
    	
    	
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                
                case "selection":
                    $player_id = $this->getActivePlayerId();
                    $path = self::getUniqueValueFromDB( "select path from player where player_id = ".$player_id);
                    if($path == 0)
                    {
                        $path = 0;
                        do {
                            $path++;
                        }
                        while(self::getUniqueValueFromDB( "select count(*) from player where path = ".$path)>0);
                        self::DbQuery("update player set path = {$path} where player_id = {$player_id}");
                    }
                    
                    $leader = 1;
                    self::DbQuery("update player set path = {$path}, leader = 1 where player_id = {$player_id}");
                    $p = new ANAPlayer($player_id);
                    
                    anachrony::$instance->notifyAllPlayers( "chooseleader", clienttranslate('${player_name} chooses ${leader} from path of ${path}'),
                        array(
                            "player_id" => $p->player_id,
                            "player_name" => $p->player_name,
                            "leader" => $p->path->leadernames[$leader-1],
                            "path" => $p->path->name,
                            "pathnb" => $path
                        )
                        );
                    $this->gamestate->nextState( "zombiePass" );
                    
                    break;
                
                default:
                    $player_id = $this->getActivePlayerId();
                    self::DbQuery("delete from pending where player_id = {$player_id}");
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        if( $from_version <= 2301271458 )
        {
            // ! important ! Use DBPREFIX_<table_name> for all tables
            $sql = "ALTER TABLE DBPREFIX_player ADD `evacuation_side` INT NOT NULL DEFAULT '1'";
            self::applyDbUpgradeToAllDB( $sql );
            
        }
        
        if( $from_version <= 2302101128 )
        {
            // ! important ! Use DBPREFIX_<table_name> for all tables
            $sql = "ALTER TABLE DBPREFIX_recruitsdrawn ADD `type` INT NOT NULL DEFAULT '1'";
            self::applyDbUpgradeToAllDB( $sql );
            
        }
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
