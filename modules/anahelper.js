define([
    "dojo","dojo/_base/declare"
],
function (dojo, declare) {
    return declare("bgagame.anahelper", null, {
        constructor: function(){
        	
        	this.warptiles = _('During 4. Warp Phase, Players may place Warp tiles on the current Timeline tile to bring assets from the future to the present.');
        	 
            this.buttons = {
                	'Confirm': _('Confirm'),
                	'Skip': _('Skip'),
                	'Pass': _('Pass'),
                	'Finish': _('Finish'),
                	'Undo': _('Undo'),
                	'Travel': _('Time Travel'),
                	'usebuilding401': _('Use building 401'),
                	'usebuilding402': _('Use building 402'),
                	'leader11': _('+2<div id="x" class="mineres anaicon res5"></div> +<div id="x" class="mineres anaicon res13"></div>'),
                	'leader12': _('-2<div id="x" class="mineres anaicon res5"></div> -<div id="x" class="mineres anaicon res13"></div>'),
                	'leader41': _('2<div id="x" class="mineres anaicon res5"></div> -> <div id="x" class="mineres anaicon res6"></div>'),
                	'leader42': _('<div id="x" class="mineres anaicon res6"></div> -> 2<div id="x" class="mineres anaicon res5"></div>'),
                };
            this.workers = {
                	1: _('Administrator'),
                	2: _('Engineer'),
                	3: _('Genius'),
                	4: _('Scientist')
                };
        	this.endgames = {
        			1:_('The player(s) with the <b>most Workers</b> at the end of the game receive(s) 3 VPs.'),
        			2:_('The player(s) with the <b>most Water</b> at the end of the game receive(s) 3 VPs.'),
        			3:_('The player(s) with the <b>most Breakthroughs</b> at the end of the game receive(s) 3 VPs.'),
        			4:_('The player(s) with the <b>most occupied building spots</b> at the end of the game receive(s) 3 VPs. This includes Anomalies and Superprojects.'),
        			5:_('The player(s) with his <b>Morale marker closest to the right end of the Morale track (i.e., highest Morale)</b> at the end of the game receive(s) 3 VPs.'),
        			6:_('The player(s) with the <b>highest sum of Time Travel ranges</b> at the end of the game receive(s) 3 VPs.<br/>NOTE: Lab #401 and Power Plants #112 and #113 count as 1 towards this sum. Lab #402 counts as 2, and the Temporal Tourism Superproject and Power Plant #114 count as 3.'),
        			7:_('The player(s) with the <b>most Superprojects</b> at the end of the game receive(s) 3 VPs.'),
        			8:_('The player(s) with the <b>most successful Time Travels</b> at the end of the game receive(s) 3 VPs.'),
        	};
        	this.superprojects = {
            		1:_('Administrator, spend 1 W: Move 1 step up on the Morale track.'),	
            		2:_('Passive: Every step on the Time Travel track is worth +1 VP at the end of the game.'),	
            		3:_('Immediately when built: Return up to three of your Warp tiles from up to three Timeline tiles to your supply. Do not advance on the Time Travel track.'),	
            		4:_('Passive: The total W cost of your Action is reduced by 1 in each of your Action Rounds.<br/>NOTE: This ability doesn’t work with Trade with Nomads and abilities outside of the Action Rounds.'),	
            		5:_('Passive: Reduce the total cost of each of your Construct Actions by an additional T, U or G (of your choice).'),	
            		6:_('Free Action: Receive a powered-up Exosuit.'),	
            		7:_('Free Action: Exchange any 2 T/U/G for 1 N or 1 N for any 2 T/U/G.'),	
            		8:_('Free Action: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile.'),	
            		9:_('Any Worker, spend 2 W: You may take a standard Recruit, Research or Construct Action.<br/>NOTE: The Worker restrictions and bonuses of the respective Action still apply. For example, you still receive a 1 T discount when choosing to Construct with an Engineer, and you cannot choose to Research with an Administrator.'),	
            		10:_('Passive: You do not score negative VPs for low Morale at the end of the game. You do not lose Workers when you use the Force Workers Action on the lowest spot of the Morale track.'),	
            		11:_('Free Action: You may place one of your Workers in a powered-up Exosuit on a Hex space or Hex Pool space on the Main board, and take an Action there.<br/>NOTE: With the Exocrawler, you can essentially take 2 Actions in the same round, with at least one of them being a Main board Action.'),	
            		12:_('Free Action, lose 1 Worker: Gain 1 Genius, 1 Neutronium or 1 Energy Core.'),	
            		13:_('Passive: When you take the Mine Resource Action, you may take an additional T, U or G from the supply.'),	
            		14:_('Passive: Your base Evacuation Condition is considered to be completed, regardless of its progress.<br/>NOTE: After constructing Rescue Pods, you are eligible to take the Evacuation Action. When you do, you also score points for the base Evacuation condition as though you had completed it.'),	
            		15:_('Immediately when built: You may take two standard Research Actions.'),	
            		16:_('Passive: Every Superproject you built (including The Ultimate Plan) is worth +3 VPs at the end of the game.'),	
            		17:_('Genius: Choose and take a Worker Action of any Superproject or building built by any player. The cost of the Action must be paid accordingly, and retrieve rules of the chosen Action apply to your Worker as well.'),	
            		18:_('Any Worker: Receive one Worker of the same Worker type in the Tired column.'),
            		19:_('This superproject has not been revealed yet.')	
        	};
        	
        	this.buildings = {
            		101:_('Any Worker: Set Focus to the previous Timeline tile.'),	
            		102:_('Any Worker: Set Focus to a Timeline tile up to 2 Eras before the current Era’s tile.'),	
            		103:_('Any Worker: Set Focus to a Timeline tile up to 2 Eras before the current Era’s tile.'),	
            		104:_('Scientist: Set Focus to a Timeline tile up to 2 Eras before the current Era’s tile.'),	
            		105:_('Any Worker: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile.'),	
            		106:_('Any Worker: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile.'),	
            		107:_('Any Worker, spend 1 U: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile. Receive 1 VP.'),	
            		108:_('Scientist (kept Motivated): Set Focus to a Timeline tile up to 2 Eras before the current Era’s tile.'),	
            		109:_('Scientist, spend 1 N: Set Focus to a Timeline tile up to 3 Eras before the current Era’s Tile. Receive 2 VPs.'),	
            		110:_('Any Worker, spend 1 W: Set Focus to a Timeline tile up to 4 Eras before the current Era’s tile.'),	
            		111:_('Any Worker: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile. When you construct this building, you may immediately return one of your Warp tiles from a Timeline tile to your supply (without scoring VPs).'),	
            		112:_('Any Worker, spend x W: Set Focus to a Timeline tile up to x Eras before the current Era’s tile. Receive 1 VP. Labs #401 and #402 decrease the amount of W you have to pay, to a minimum of 1.'),	
            		113:_('Any Worker, spend x T/U/G (minimum 1): Set Focus to a Timeline tile x Eras before the current Era’s tile. Receive x VPs. You may use Labs #401 and #402 to set Focus further back than x, but you still only receive x VPs.'),	
            		114:_('Scientist, spend 1 W: Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile, then repeat this process.'),	
            		115:_('Any Worker, spend 1 G : Set Focus to a Timeline tile up to 3 Eras before the current Era’s tile. Receive 1 VP.'),
            		201:_('Any Worker (kept Motivated): Receive 2 T.'),	
            		202:_('Any Worker, spend 1 W: Receive 3 T.'),	
            		203:_('Any Worker (kept Motivated), spend 1 W: Receive 1 T/U/G.'),	
            		204:_('Any Worker (kept Motivated): Receive 1 G.'),	
            		205:_('Any Worker, spend 1 W: Receive 2 G.'),	
            		206:_('Any Worker (kept Motivated): Receive 1 U.'),	
            		207:_('Any Worker, spend 1 W: Receive 2 U.'),	
            		208:_('Any Worker, spend 1 G + 1 W: Receive 1 N + 1 VP.'),	
            		209:_('Any Worker, spend 1 U + 1 W: Receive 1 N + 1 VP.'),	
            		210:_('Engineer, spend 3 W: Receive 3 T/U/G or 1 N.'),	
            		211:_('Engineer (kept Motivated), spend 1 T: Receive 1 Energy Core.'),	
            		212:_('Engineer: Receive 1 Energy Core.'),	
            		213:_('Engineer, spend 2 T/U/G: Receive 2 Energy Cores.'),	
            		214:_('Engineer, spend 3 W: Receive 2 Energy Cores.'),	
            		215:_('Free Action: Exchange 1 W for 1 T/U/G.'),
            		301:_('Free Action: Receive 1 W. When you construct this building, immediately receive 3 W.'),	
            		302:_('Free Action: Receive 1 W. When you construct this building, immediately receive 3 W.'),	
            		303:_('Free Action: Receive 2 W.'),	
            		304:_('Free Action: Receive 2 W.'),	
            		305:_('Any Worker (kept Motivated): Receive 3 W.'),	
            		306:_('Any Worker (kept Motivated): Receive 3 W.'),	
            		307:_('Administrator: Receive 5 W.'),	
            		308:_('Administrator: Receive 5 W.'),	
            		309:_('Any Worker, spend 1 N: Receive 8 W.'),	
            		310:_('Any Worker (dies when retrieved): Receive 7 W'),	
            		311:_('Passive: The Supply Action’s W cost is halved, rounded up. If you own both 311 and 312, your Supply Action has no W cost.'),	
            		312:_('Passive: The Supply Action’s W cost is halved, rounded up. If you own both 311 and 312, your Supply Action has no W cost.'),	
            		313:_('Any Worker (kept Motivated), spend 1 U: Receive 6 W + 1 VP.'),	
            		314:_('Any Worker (kept Motivated), spend 1 G: Receive 6 W + 1 VP.'),	
            		315:_('When you Construct this building, immediately receive 8 W.'),
            		401:_('Passive: The range of your Power Plants is increased by 1.'),	
            		402:_('Passive: The range of your Power Plants is increased by 2.'),	
            		403:_('Any Worker (kept Motivated), spend 1 Energy Core: Receive 1 powered-up Exosuit.'),	
            		404:_('Scientist: Return 1 Paradox from your Player board to the supply.'),	
            		405:_('Passive: You can receive 1 additional Paradox before you receive an Anomaly.'),	
            		406:_('Passive: Your Anomalies are worth 2 additional VPs each (reducing their total VP penalty).'),	
            		407:_('Scientist: Return one of your Warp tiles from a Timeline tile to your supply (without scoring VPs).'),	
            		408:_('Any Worker (Administrator is kept Motivated): Move all your Workers from your Tired column to your Active column.'),	
            		409:_('Administrator (kept Motivated), spend 2 W: Receive a Scientist or an Engineer (Active).'),	
            		410:_('Administrator (kept Motivated), spend 2 W: Receive a Genius (Active).'),	
            		411:_('Passive: When taking the Research Action, you may pay 1 W to set 1 additional die to the face of your choice instead of rolling it.'),	
            		412:_('Any Worker, spend 1 T/U/G: Receive 2 VPs.'),	
            		413:_('Any Worker: Receive 1 W and 1 VP.'),	
            		414:_('Free Action: Receive 2 VP and a Paradox.'),	
            		415:_('Scientist (dies when retrieved): Receive 2 W and 2 VPs.'),
            		515:_('Any Worker, spend either 2 Titanium/Uranium/Gold plus 2 Water or 1 Neutronium plus 2 Water to seal it. Remove the Anomaly and the Worker immediately and place them back in their respective general supplies.')	
        	};
        	
        	this.leaders = {
        		11:_('<b>Shepherd Caratacus:</b><br/> Meddle with Time (Free Action): Choose one: receive 2 Water and a Paradox, OR pay 2 Water and return 1 Paradox from your Player board to the supply.'),
        		12:_('<b>High Sunwalker Amena:</b><br/> Hardened Exosuits: When resolving the Impact, do not cover any of her Exosuit slots with Hex Unavailable tiles.'),
        		21:_('<b>Patron Valerian:</b><br/> Neuroenhancement: All of his Scientists count as Geniuses for all placement and retrieval purposes.'),
        		22:_('<b>Librarian Cornella:</b><br/> Focused Research: During the Clean up phase, she may pay 3 Water to take a Research Action.'),
        		31:_('<b>Patriarch Haulani:</b><br/> Inspiring Charisma (Free Action): He may place one of his active Workers on a Worker slot on his Player board, and take an Action there. As this is a free action, it means that you can take worker action then a second standard action immediately.'),
        		32:_('<b>Matriarch Zaida:</b><br/> Saving Grace: During the Clean up phase, she may pay 2 Water to recruit a Worker from the supply. This ability is limited to Worker types that are present on the most recent Worker pool card drawn during the Preparation phase of the same Era. She does not receive the Recruit bonus associated with that Worker.'),
        		41:_('<b>Captain Wolfe: Hydrocores:</b><br/> During the Power up phase, he may exchange 1 Energy Core for 2 Water, or 2 Water for 1 Energy Core any number of times.'),
        		42:_('<b>Treasure Hunter Samira:</b><br/> Treasure Hunt: During the Clean up phase, she receives 1 T/U/G from the supply, chosen randomly. Then, she may pay 2 Water to gain an additional T/U/G of her choice.'),
        	};
        	
        	this.paths = {
            		1:_('<b>PATH OF SALVATION</b>'),
            		2:_('<b>PATH OF PROGRESS</b>'),
            		3:_('<b>PATH OF HARMONY</b>'),
            		4:_('<b>PATH OF DOMINANCE</b>'),
        	};
        	
        	this.evacuations = {
        			1 : {
        				1:_('<b>Overwhelming Power</b><br/><b>Base condition:</b> Have 3 Power Plants in order to Evacuate (3 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 3 additional VPs for each Neutronium you have when you Evacuate.'),
        				2:_('<b>Technological Superiority</b><br/><b>Base condition:</b> Have 3 Labs in order to Evacuate (6 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 4 additional VPs for each set of one Breakthrough + two Scientists you have when you Evacuate. Tired and busy Scientists also count.'),
        				3:_('<b>Welfare and Prosperity</b><br/><b>Base condition:</b> Have 3 Life Supports in order to Evacuate (2 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 3 additional VPs for each Genius + Gold pair you have when you Evacuate. Tired and busy Geniuses also count.'),
        				4:_('<b>Industrial Revolution</b><br/> <b>Base condition:</b> Have 3 Factories in order to Evacuate (5 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 2 additional VPs for each Engineer + Titanium pair you have when you Evacuate. Tired and busy Engineers also count.'),
        			},
        			2 : {
        				1:_('<b>Masters of Time</b><br/> <b>Base condition:</b> Have at least 2 Anomalies in order to Evacuate (4 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 5 additional VPs for each pair of two Time Travel advances + two Uranium Resources.'),
        				2:_('<b>The Apex of Humanity</b><br/> <b>Base condition:</b> Have at least 8 Water in order to Evacuate (3 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 5 additional VPs for each Superproject you have when you Evacuate.'),
        				3:_('<b>Nature’s Resurgence</b><br/> <b>Base condition:</b> Have at least 6 occupied Building spots in order to Evacuate (2 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 3 additional VPs for each Building + Administrator pair you have when you Evacuate. Tired and busy Administrators also count.'),
        				4:_('<b>The Power of Unity</b><br/> <b>Base condition:</b> Have maximum Morale in order to Evacuate (3 VPs). <br/><b>Additional reward:</b> Your Evacuation Action is worth 1 additional VP for each Worker you have when you Evacuate. Tired and busy Workers also count.')
        			}
            	};
        	
        	this.boardsB = {
        		1:_('<b>Exosuit Hex Slots:</b> You have to pay 1 T/U/G to power up an Exosuit on the two left slots. You have to pay 1 Energy Core to power up an Exosuit on the two right slots. Powering up Exosuits on the two middle slots is free.<br/><b>Morale & Supply:</b> You start 1 step higher on the Morale track. Your Morale track ranges from -8 VP to 4 VP.<br/><b>Building Costs:</b> The second Power Plant spot costs 1 less G. The third Lab costs 1 N, 2 T and 2 W.<br/><b>Other:</b> The last four steps on the Time Travel track are worth 12/14/16/20 VPs each. You may place Anomalies on any empty building spot on your Player board.'),	
        		2:_('<b>Exosuit Hex Slots:</b> On the three bottom slots, you may pay 1 T/U/G instead of an Energy Core to power up an Exosuit.<br/><b>Morale & Supply:</b> The final three steps on the Morale track are worth 3/5/7 VPs each, but the Supply Action on these steps cost 7/8/8 W respectively. An Administrator taking the Supply Action is not kept Motivated.<br/><b>Building Costs:</b> The first Power Plant spot costs 1 additional G. The first Life Support spot costs 1 additional U.<br/><b>Other:</b> When you use Scientists to take Worker Actions on your Power Plants, Factories and Labs, they are kept Motivated. You need one additional Paradox to gain an Anomaly. The first three steps on the Time Travel track are worth 1/2/4 VPs each.'),
        		3:_('<b>Exosuit Hex Slots:</b> On two of the three bottom slots, you may pay 3 Water instead of an Energy Core to power up an Exosuit. At the end of the Power Up phase, you receive 2 Water for each remaining empty Hex slot. After the Impact, the middle bottom slot is also destroyed. <br/><b>Morale & Supply:</b> You start 1 step lower on the Morale track. Your Morale track ranges from -4 VP to 8 VP. When you take the Supply Action and have maximum Morale, you receive 3 VPs instead of 2. <br/><b>Building Costs:</b> The first Factory and Life Support spots cost 1 additional W each. <br/><b>Other:</b> Whenever you would gain an Anomaly, you may choose to lose one of your buildings instead. Return it to the bottom of its respective primary stack. If there was a Worker on that building, set that Worker aside and place it in the Tired column at the end of the round.'),
        		4:_('<b>Exosuit Hex Slots:</b> At the end of the Power Up phase, you do not receive Water for your remaining empty Hex slots.<br/><b>Morale & Supply:</b> Supply is a Free Action and costs 4/4/4/4/5/5/5 Water respectively. The lowest three steps on the Morale track are worth -3/-2/-1 VPs respectively.<br/><b>Building Costs:</b> The second Power Plant spot and the third Factory spot cost 1 less G each. The second Life Support spot cost 1 less U.<br/><b>Other:</b> The last three steps on the Time Travel track are worth 12 VPs each. You may build Superprojects on any two adjacent empty building spots on your Player board.')
        	};
        	
        	this.actions = {
        		"construct":_('<b>CONSTRUCT :</b></br>Choose one of the following two options: <br/>1. Select a face-up building from the top of any of the 8 building stacks (primary and secondary stacks of each building type), then place the building on the leftmost empty spot of its respective row of your Player board, paying the costs indicated on the spot.<br/></br/>2. Build the Superproject in Focus (i.e. the one above the Timeline tile where your Focus marker is). By default, this is the current Era’s Timeline, but Focus can be changed via Time Travel. Place the Superproject on the two leftmost horizontally adjacent free spots of your Player board (you may choose which row to place it on if several rows are tied). Ignore the costs printed on the spots, and instead pay the costs indicated on the Superproject itself (including Breakthroughs). If a Superproject has a Worker in its construction cost, the Worker can be paid from either the Active or Tired columns.<br/></br/>Worker specifics<br/>• May not be activated by an Administrator.<br/> • If activated by an Engineer, subtract 1 Titanium from the total cost of the Action.'),	
        		"research":_('<b>RESEARCH :</b></br>Set one Research die (shape or icon) to the face of your choice, and roll the other. Take a Breakthrough tile with the shape and icon shown by the dice. The “?” on the icon die stands for any icon of your choice.<br/>NOTE: In the unlikely case the rolled Breakthrough is not available, reroll one die of your choice.<br/><br/>Worker specifics <br/>• May only be activated by a Scientist.'),
        		"recruit":_('<b>RECRUIT :</b></br>Select a Worker from the Recruit pool and add it to the Active column of your Player board. You also receive a bonus based on the type of the Worker chosen: <br/>• Scientist: 2 Water. <br/>• Engineer: 1 Energy Core. <br/>• Administrator: 1 Victory Point. <br/>• Genius: Any one of the above three bonuses.<br/><br/>Worker specifics <br/>• May not be activated by a Scientist. <br/>• If activated by an Engineer, you may not select a Genius (only a Scientist, an Engineer or an Administrator).'),
        		"mine":_('<b>MINE :</b></br>Take 1 Resource of your choice from the Mine pool. <br/><br/>Worker specifics <br/>• If activated by an Engineer, he is kept Motivated. <br/><br/>Mine Resource has 3 available Hex spaces: <br/>1. Upper space – take a Uranium in addition to the Resource taken from the Mine pool. <br/>2. Middle space – take a Gold in addition to the Resource taken from the Mine pool. <br/>3. Lower space – take a Titanium in addition to the Resource taken from the Mine pool.'),
        		"council":_('<b>WORLD COUNCIL :</b><br/>You may choose a Capital Action (Construct, Recruit, Research) with no more available spaces and perform it.<br/>Worker restrictions and benefits of the copied Capital Action apply for the Worker placed on the World Council, but space-related features (e.g. Water costs on Hexes and Collapsing Capital tile bonuses) do not.<br/><br/>Worker specifics <br/>• Worker restrictions and/or bonuses of this Action are the same as those of the copied Capital Action.<br/><br/> World Council has 2 available Hex spaces: <br/>1. Left space – must pay 2 Water and become the First Player (replace the previous First Player’s banner on the spot next to this Action space). <br/>2. Right space – must pay 1 Water.'),
        		"purify":_('<b>PURIFY WATER :</b><br/>Take 3 Water from the supply.<br/><br/>Worker specifics<br/> <br/>• If activated by a Scientist, take 1 additional Water. <br/><br/>Purify Water has a Hex Pool space, where any number of Workers can be placed.'),
        		"trade":_('<b>TRADE WITH NOMADS :</b><br/>You may choose one of the following: <br/>• Exchange 3 Water to 1 Energy Core; or vice versa. <br/>• Exchange 1 Energy Core to 1 Neutronium; or vice versa. <br/>• Exchange 1 Neutronium to any 2 of Titanium, Uranium, or Gold; or vice versa. <br/>• Exchange any 2 of Titanium, Uranium, or Gold to 3 Water; or vice versa. <br/><br/>Worker specifics <br/>• If activated by an Administrator, you may choose from the above options twice (one after the other). Trade with Nomads has a Hex Pool space, where any number of Workers can be placed.'),
        		"evacuate": _('<b>EVACUATION :</b><br/>This Action space will only be available after the Impact. It may only be taken by each player once per game, and only if they meet the condition stated on their Path board. Place one of your Path markers on the uppermost free numbered slot on the tile and receive the Victory Points specified on your Path board under the Evacuation condition. If you placed your Path marker on the spot with the -3 Victory Points marker, you receive 3 less Victory Points for your Evacuation (to a minimum of 0). The maximum number of Victory Points that can be received for the Evacuation Action is 30.'),
        		"supply":_('<b>SUPPLY :</b><br/>Spend Water equal to the number printed on the Water symbol below your current position on the Morale track, then move all of your Workers from the Tired column to the Active column (ready to be used in later Action rounds of the same Era). Finally, advance one step on the Morale track (to the right). If you are already at maximum Morale, you receive a number of VPs indicated at the right end of the Morale track instead of advancing on it.'),
        		"force":_('<b>FORCE WORKERS :</b><br/>Force Workers is a Free Action, and requires no Worker. Place one of your Path markers on the slot, then move all of your Workers from the Tired column to the Active column (ready to be used in later Action rounds of the same Era). Finally, you fall back one step on the Morale track (to the left). If you are already at minimum Morale, you lose a Worker of your choice instead of falling back on the Morale track.')
        	};
        	
        	this.blockeds = {
        			11:_('You receive 2 VPs in addition to this Research Action.'),
        			12:_('You may take an additional Research Action.'),
        			13:_('You may set an additional die to the face of your choice when taking this Research Action.'),
        			14:_('You may return up to 2 Paradoxes from your Player board to the supply in addition to this Research Action.'),
        			15:_('After taking this Research Action, you may take an additional Construct Action. You may only construct a Superproject with this Action. If you take it with a Genius, you may treat it as an Engineer for the Construct Action.'),
        			21:_('You may take an additional Construct Action.'),
        			22:_('Reduce the total cost of this Construct Action by one additional N.'),
        			23:_('Reduce the total cost of this Construct Action by one additional T, U or G (of your choice).'),
        			24:_('If you Construct a Superproject (not building) with this Action, you receive an additional 2 VPs.'),
        			25:_('If you Construct a building (not Superproject) with this Action on the first/second/third building spot of its respective row, you receive 1/2/3 VPs.'),
        			31:_('You may take an additional Recruit Action.'),
        			32:_('You receive a powered-up Exosuit in addition to the recruited Worker.'),
        			33:_('Gain 1 Morale in addition to the recruited Worker.'),
        			34:_('You receive the Recruit bonus associated with your recruited Worker one additional time. If you recruit a Genius, you may choose a different bonus for the second time.'),
        			35:_('After recruiting the Worker, move all your Workers from the Tired column to the Active column.'),
        	};
        }
    });
});