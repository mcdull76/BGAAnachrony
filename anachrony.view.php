<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * anachrony implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * anachrony.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in anachrony_anachrony.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
require_once( APP_BASE_PATH."view/common/game.view.php" );
  
class view_anachrony_anachrony extends game_view
{
    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "anachrony";
    }
    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        /*********** Place your code below:  ************/

        $player_positions = $this->game->getPlayerRelativePositions();
        $player_positions_inverse = array();
        foreach ($player_positions as $player_id => $dir) {
            $player_positions_inverse[$dir] = $player_id;
        }
        
                
        $this->page->begin_block("anachrony_anachrony", "player");
        foreach ($player_positions as $player_id => $dir) {
            $this->page->insert_block("player", array("PLAYER_ID" => $player_id,
                "PLAYER_NAME" => $players[$player_id]['player_name'],
                "PLAYER_COLOR" => $players[$player_id]['player_color'],
                "PLAYER_BEFORE" => $player_positions_inverse[($dir-1+$players_nbr)%$players_nbr],
                "PLAYER_AFTER" => $player_positions_inverse[($dir+1)%$players_nbr]
            ));
        }
        
        /*********** Do not change anything below this line  ************/
  	}
}
