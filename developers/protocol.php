<?
include "../document.php";

$doc = new Document;
$doc->begin("protocol");
print <<< end
<p><i>This document is somewhat out of date. The source code is always the best
reference to determine the protocol.</i></i>
<p>
<u>BZFlag network protocol</u><br>
[ This is a work in progress.  It is mostly done but still incomplete. ]<br><br>

--> means from client to server<br>
<-- means from server to client<br><br>

All multi-byte data is in network byte order (aka big endian);  that is, the most
significant byte comes first followed by successively less significant bytes.<br><br>

All numbers are integers unless otherwise specified.  Integers can be 8, 16, or 32
bit signed 2's compliment or unsigned (encoding is the same in regardless of signed
status).  Values in this document in byte boxes are given in hexadecimal.  Values
in the text are given in decimal unless otherwise noted.<br><br>

Bitfields are encoded as unsigned integers.<br><br>

All floating point numbers are in standard IEEE-754 single precision format.<br><br>

Multibyte values are *not* necessarily aligned on 2 or 4 byte boundaries.<br><br>

A client normally follows the following sequence during game play:<br><br>

  connect to server<br>
  get the world database<br>
  join the game<br>
  send/receive game messages<br>
  leave game<br>
  disconnect from server<br><br><br>


<u>common message structures</u><br>
Most messages begin with the length of the message in bytes followed by a 16 bit code.
The length is 16 bits and excludes itself and the code, so it's the actual length of
the message minus 4.<br><br>

Player identifiers are used in many messages and are always the same:<br><br>
<pre>
	+----+----+----+----+----+----+----+----+
	| server host addr  |  port   | number  |
	+----+----+----+----+----+----+----+----+
</pre>
The `client host addr' is the IP address of the host the server is on.  This is
superfluous for TCP connections with the server, but is necessary for UDP multicast
communication.  `Port' is the TCP port on the server the client is connected to.
Since clients have to reconnect to the server (see `connect to server'), this will be
unique to each client.  `Number' is the player number on this particular client.
Normally this is zero, but a client is allowed to have multiple players over one
server connection.<br><br>
Flag status is common to a few message types:
<pre>
	+----+----+----+----+----+----+----+----+
	| flag id | status  |  type   |
	+----+----+----+----+----+----+----+----+
	       owner id...            | position
	+----+----+----+----+----+----+----+----+
	    (x)   |    position (y)   | position
	+----+----+----+----+----+----+----+----+
	    (z)   |     launch (x)    |  launch
	+----+----+----+----+----+----+----+----+
	    (y)   |     launch (z)    | landing
	+----+----+----+----+----+----+----+----+
	    (x)   |    landing (y)    | landing
	+----+----+----+----+----+----+----+----+
	    (z)   |    flight time    |  flight
	+----+----+----+----+----+----+----+----+
	 end time | initial velocity  |
	+----+----+----+----+----+----+
</pre>
`Position,' `launch,' `landing,' `flight time,' `flight end,' and `initial velocity'
are all floating point numbers.  `Flag id' is the type of flag,enumerated in FlagId.

`Status' gives the current location of the flag:
<pre>
  FlagNoExist  -- the flag is not active (i.e. not in the game)
  FlagOnGround -- the flag is sitting on the ground ready to get picked up
  FlagOnTank   -- the flag is on a player's tank
  FlagInAir    -- the flag is in the air and will fall back down
  FlagComing   -- the flag just appeared and is falling to the ground
  FlagGoing    -- the flag was thrown in the air and is disappearing
</pre>

`Type' indicates the behavior of the flag:  FlagNormal means the flag is permanent
and can be dropped normally (e.g. a team flag);  FlagUnstable means the flag may
disappear after use (e.g. a good super flag);  FlagSticky means the flag cannot be
dropped normally (e.g. a bad super flag).<br><br>

`Player id' indicates which player has the flag when `status' is FlagOnTank.  It
isn't used for any other status.<br><br>

`Position' is the location of the flag on the ground.  It only has meaning when
`status' is FlagOnGround.  The position of a flag when on a tank is the position
of the tank (centered on and at the top of the tank)<br><br>

`Launch' is the location of the flag where it was thrown in the air.  `Landing' is
the location where the flag will/would touch ground.  These are used when `status'
is FlagInAir, FlagComing, and FlagGoing.  The client should make the flag follow a
reasonable flight path (e.g. a parabolic arc) from `launch' to `landing.'<br><br>

`Flight time' is the time elapsed since the flag was thrown into the air.  `Flight
end' the time when the flag will reach the ground.  `Initial velocity' gives the
vertical (z) velocity of the flag when lanuched.  The vertical position of the flag
at time t (ranging from 0 to `flight end') is:  z = z0 + v * t + 0.5 * g * t * t.
z0 = `launch z;' v = `initial velocity;'  g = acceleration due to gravity.<br><br>

<pre>
connect to server
-----------------
  -->	connect(server port)
  <--	+----+----+----+----+----+----+----+----+
	| 42 | 5a | 46 | 53 | 31 | 30 | 37 | 62 |	BZFS107b	version
	+----+----+----+----+----+----+----+----+
	|   port  |
	+----+----+
  -->	close
  -->	connect(port)
</pre>

Client should verify the version.  The first four bytes indicates a BZFlag server,
the next byte indicates the major version number (as an ascii digit), the two bytes
after that are the minor version number (as ascii digits), and the 8th byte is the
revision.  Major and minor version number changes indicate incompatible protocol
changes.  Different revisions of a given version are compatible and usually just
indicate client user interface changes.<br><br>

The reconnect port is the port the client should reconnect to the server at.  If
the connection is rejected for some reason then this port will be zero.  Yes, this
step is really pointless, complicates firewall tunnels, and it could just as well
use the original connection.  It's unfortunate historical cruft.  Note that the
server will not wait long for the reconnection.<br><br>

The reconnected socket should have TCP_NODELAY enabled (Nagle algorithm disabled)
to prevent the client from batching the many small packets.<br><br>

<pre>
getting the world database
--------------------------
  -->	+----+----+----+----+----+----+
	| 00 | 02 | 67 | 77 | offset  |			MsgGetWorld
	+----+----+----+----+----+----+
  <--	+----+----+----+----+----+----+----+----+
	| length  | 67 | 77 |remaining| data... |	MsgGetWorld
	+----+----+----+----+----+----+----+----+
	or
	+----+----+----+----+
	| 00 | 00 | 73 | 6b |				MsgSuperKill
	+----+----+----+----+
</pre>
Client sends MsgGetWorld requests until it has read the entire world database.
Clients should send an `offset' of 0 for the first request and increase `offset'
by however much data was sent in reply.<br><br>

Each MsgGetWorld reply includes the number of bytes left after the returned
block of data in `remaining'.  The server returns 0 in `remaining' when there
is no more data left to read.  In no event will the server return more than
MaxPacketLen - 6 bytes of data in one reply (see Appendix A for the definition
of MaxPacketLen).<br><br>

The server may return MsgSuperKill at any time during the download of the world
database.  The client should immediately close the connection to the server.<br><br>

No other messages should be returned by the server as long as the client sends
only MsgGetWorld requests.<br><br>

The `data' has the following encoding:<br><br>
<pre>
	+----+----+----+----+----+----+----+----+
	| 73 | 74 | 00 | 18 |  style  | players |	WorldCodeStyle, length of data
	+----+----+----+----+----+----+----+----+
	|  shots  |  flags  |linear acceleration|
	+----+----+----+----+----+----+----+----+
	|angulr acceleration| shake t | shake w |
	+----+----+----+----+----+----+----+----+
	|    server time    |
	+----+----+----+----+
</pre>
Values are:
<pre>
  style:	bitfield of game style
  players:	maximum number of players
  shots:	maximum number of shots at once per player
  flags:	maximum number of flags that may exist at once
  linear acc.:	linear acceleration limit (float)
  angulr acc.:	angular acceleration limit (float)
  shake t:	time to shake a bad flag (1/10ths of seconds)
  shake w:	number of wins to shake a bad flag
  server time:	seconds since midnight Jan 1, 1970 GMT on the server, used when
		synchronizing times across clients
</pre>

The rest of the world database is encoded in individual blocks decribing each
object.  Only permanent immovable objects are included.  The encodings for each
type of object are:
<pre>
  team base:
	+----+----+----+----+----+----+----+----+
	| 62 | 61 | team id |     center (x)    |
	+----+----+----+----+----+----+----+----+
	|     center (y)    |     center (z)    |
	+----+----+----+----+----+----+----+----+
	|       angle       |      size (x)     |
	+----+----+----+----+----+----+----+----+
	|      size (y)     |    safety (x)     |
	+----+----+----+----+----+----+----+----+
	|    safety (y)     |    safety (z)     |
	+----+----+----+----+----+----+----+----+

  wall:
	+----+----+----+----+----+----+----+----+
	| 77 | 6c |    center (x)     | center...
	+----+----+----+----+----+----+----+----+
	 (y)      |    center (z)     | angle...
	+----+----+----+----+----+----+----+----+
	          |       width       | height...
	+----+----+----+----+----+----+----+----+
	          |
	+----+----+

  pyramid:
	+----+----+----+----+----+----+----+----+
	| 70 | 79 |    center (x)     | center...
	+----+----+----+----+----+----+----+----+
	 (y)      |    center (z)     | angle...
	+----+----+----+----+----+----+----+----+
	          |       width       | depth...
	+----+----+----+----+----+----+----+----+
	          |      height       |
	+----+----+----+----+----+----+

  box:
	+----+----+----+----+----+----+----+----+
	| 62 | 78 |    center (x)     | center...
	+----+----+----+----+----+----+----+----+
	 (y)      |    center (z)     | angle...
	+----+----+----+----+----+----+----+----+
	          |       width       | depth...
	+----+----+----+----+----+----+----+----+
	          |      height       |
	+----+----+----+----+----+----+

  teleporter:
	+----+----+----+----+----+----+----+----+
	| 74 | 65 |    center (x)     | center...
	+----+----+----+----+----+----+----+----+
	 (y)      |    center (z)     | angle...
	+----+----+----+----+----+----+----+----+
	          |       width       | depth...
	+----+----+----+----+----+----+----+----+
	          |      height       | border...
	+----+----+----+----+----+----+----+----+
	          |
	+----+----+

  teleporter link:
	+----+----+----+----+----+----+
	| 6c | 6e |  from   |   to    |
	+----+----+----+----+----+----+

  end of data:
	+----+----+
	| 65 | 64 |
	+----+----+
</pre>
The above values are interpreted as follows:
<pre>
  angle (float):	in radians from the x-axis in the xy plane
  width (float):	half width (in xy) measured from center
  depth (float):	half depth (in xy) measured from center
  height (float):	full height (in z) measured from bottom
  border (float):	full size of the square cross section teleporter border
  center (float):	center in xy, bottom in z
  safety (float):	safety position for team flags, used when team A drops team B's
			flag on team C's base.
  from & to:		index of teleporter face starting from zero;  the N'th teleporter
			in the world data has face indices 2*N and 2*N+1.  teleporter
			links indicate which face teleports to which face;  faces may
			teleport to themselves.
</pre>

join game<br>
---------<br>
Once connected a player normally requests to join the game.  The server replies with
MsgSuperKill, MsgReject, or MsgAccept.

<pre>
  -->	+----+----+----+----+----+----+----+----+
	| length  | 65 | 6e |  player id...		MsgEnter
	+----+----+----+----+----+----+----+----+
	                    |  type   |  team   |
	+----+----+----+----+----+----+----+----+
	| call sign (CallSignLen bytes)...
	+----+----+----+----+----+----+----+----+
	...
	+----+----+----+----+----+----+----+----+
	                                        |
	+----+----+----+----+----+----+----+----+
	| email address (EmailLen bytes)...
	+----+----+----+----+----+----+----+----+
	...
	+----+----+----+----+----+----+----+----+
	                                        |
	+----+----+----+----+----+----+----+----+
  <--	+----+----+----+----+
	| 00 | 00 | 61 | 63 |				MsgAccept
	+----+----+----+----+
	or
	+----+----+----+----+----+----+
	| 00 | 02 | 72 | 6a |  code   |			MsgReject
	+----+----+----+----+----+----+
	or
	+----+----+----+----+
	| 00 | 00 | 73 | 6b |				MsgSuperKill
	+----+----+----+----+
</pre>
`Type' is one of the enumerants in PlayerType and `team' is one from TeamColor (see
appendix B).  The call sign is the player's `handle' during the game;  the call sign
is used to identify the player.  No attempt is made by the server to prevent the
duplication of call signs during a game.  The email address can be anything, but
should be the email address for human players (e.g. username@hostname).  BZFlag
players can choose `anonymous' instead of their email address.  The call sign and
email address should be NUL terminated ASCII strings, padded with NUL's to the 
required length.  `Length' is 12 + CallSignLen + EmailLen.<br><br>

If the server responds with MsgSuperKill the client should close the connection
immediately.  If the server responds with MsgReject, the client has not joined the
game but may keep the server connection open and try again.  The reason for rejection
is included with the MsgReject message:
<pre>
	RejectBadRequest	-- bogus data in MsgEnter
	RejectBadTeam		-- invalid team id
	RejectBadType		-- invalid client type
	RejectNoRogues		-- rogue team requested but rogues not allowed
	RejectTeamFull		-- no more players allowed on team
	RejectServerFull	-- no more players allowed on any team
</pre>
If the server responds with MsgAccept, the player has successfully joined the game.
The server then automatically sends updates about each flag, team, and player.  The
client should be prepared to accept these updates in any order.  The flag and team
updates are sent as MsgFlagUpdate and MsgTeamUpdate messages, respectively.  Player
updates are sent as MsgAddPlayer messages.  The client will receive a MsgAddPlayer
for the player it just added, but only after it has received a MsgAddPlayer for each
of the players already joined.<br><br>

Until it receives the MsgAddPlayer for the player it just added, the client will
only receive the following kinds of messages:
<pre>
	MsgSuperKill
	MsgFlagUpdate
	MsgTeamUpdate
	MsgAddPlayer
	MsgNetworkRelay
</pre>
Players of type ComputerPlayer are not sent team or flag updates and only get the
MsgAddPlayer for themselves.  This probably wasn't a good idea, but there it is.<br><br>

	  
leave game<br>
----------<br>
Once entered, a player can leave the game at any time.  This should be done by sending
the following message then closing the connection:
<pre>
  -->	+----+----+----+----+
	| 00 | 00 | 65 | 78 |				MsgExit
	+----+----+----+----+
</pre>
There is no reply.  Clients can also leave by simply closing the connection, but
sending MsgExit first is recommended.<br><br>


during a game<br>
-------------<br>
There are several types of messages that are delivered at any time after a player
enters a game.  Clients must be prepared to handle them in any order.
<pre>
  MsgSuperKill
  <--	+----+----+----+----+
	| 00 | 00 | 73 | 6b |
	+----+----+----+----+
	Client must immediately disconnect from the server.

  MsgTimeLeft
  <--	+----+----+----+----+----+----+
	| 00 | 02 | 74 | 6f |time left|
	+----+----+----+----+----+----+
	Gives the time remaining in the game.  This message is only sent when the
	server has time limit configured.  If the time remaining is zero, the client
	should indicate to the player that the game is over.

	`Time left' is in seconds.  It is not sent every second so clients will need
	to do their own count down between messages.

  MsgScoreOver
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 0a | 73 | 6f |     player id...
	+----+----+----+----+----+----+----+----+
	                    | team id |
	+----+----+----+----+----+----+
	Gives the player or team that won the game by reaching the target score.
	This message is only sent when the server has a target score configured.
	The client should indicate which team or player won and that the game is
	over.

	If `team id' is NoTeam then `player id' indicates which player has won.
	Otherwise `team id' indicates which team won.

  MsgSetTTL
  <--	+----+----+----+----+----+----+
	| 00 | 02 | 74 | 74 | new TTL |
	+----+----+----+----+----+----+
	Should a client join with a larger TTL than any other client, the server
	notifies all the clients of the new minimum TTL.  The TTL is the time-
	to-live for the multicast player-to-player packets.  Upon receipt of this
	message a client should change the TTL of outgoing multicast packets to
	at least `new TTL.'

  MsgNetworkRelay
  <--	+----+----+----+----+
	| 00 | 00 | 6e | 72 |
	+----+----+----+----+
	This message means that the client must start sending messages normally
	sent via multicast directly to the server.  The server will send the
	messages to each client.  The server may send this when a client first
	enters a game or later when another client requests multicast-over-tcp
	relaying.  If the server itself cannot do multicasting then it must
	force all clients to do multicast-over-tcp.

  -->	+----+----+----+----+
	| 00 | 00 | 6e | 72 |
	+----+----+----+----+
	When a client can't do multicasting it notifies the server of that fact
	and the server begins listening for multicast traffic on behalf of the
	client and forwarding it to that client via tcp.  It also sends messages
	from that client via multicast to other clients.  Send this message to
	the server soon after connecting if you cannot contact the server via
	multicast so you don't miss messages from other players.

  MsgAddPlayer
  <--	+----+----+----+----+----+----+----+----+
	| length  | 61 | 70 |     player id...
	+----+----+----+----+----+----+----+----+
	                    |  type   |  team   |
	+----+----+----+----+----+----+----+----+
	|  wins   |  losses | call sign...
	+----+----+----+----+----+----+----+----+
	        ... (CallSignLen bytes)...      |
	+----+----+----+----+----+----+----+----+
	| email address...
	+----+----+----+----+----+----+----+----+
	         ... (EmailLen bytes)...        |
	+----+----+----+----+----+----+----+----+
	Sent when another player enters the game and when joining a game if other
	players are already joined.  `Type' is enumerated in PlayerType, `team'
	is enumerated in TeamColor.  `Wins' and `losses' give the player's score.
	They are unsigned integers;  the player's score equals wins minus losses.
	`Call sign' and `email address' are NUL terminated ASCII strings.  `Length'
	is 16 + CallSignLen + EmailLen.

  MsgRemovePlayer
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 08 | 72 | 70 |     player id...
	+----+----+----+----+----+----+----+----+
	                    |
	+----+----+----+----+
	Sent when another player leaves the game.

  MsgFlagUpdate
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 40 | 66 | 75 |flag num | flag id |
	+----+----+----+----+----+----+----+----+
	| status  |  type   |     player id...
	+----+----+----+----+----+----+----+----+
	                    |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	|     launch (x)    |     launch (y)    |
	+----+----+----+----+----+----+----+----+
	|     launch (z)    |    landing (x)    |
	+----+----+----+----+----+----+----+----+
	|    landing (y)    |    landing (z)    |
	+----+----+----+----+----+----+----+----+
	|    flight time    |    flight end     |
	+----+----+----+----+----+----+----+----+
	| initial velocity  |
	+----+----+----+----+
	Sent when a flag's state changes.  Also sent to a client when it enters
	a game.  `Flag num' is the index of the flag.  Flags are indexed from
	zero up to the maximum number of flags minus one.

	Clients must update the flag positions while the flag is in flight (i.e.
	`status' is FlagInAir, FlagComing, or FlagGoing.  If FlagInAir or
	FlagComing then the client should change the flag's status to
	FlagOnGround when the flag's flight time equals or exceeds `flight end.'
	The position should be set exactly to `landing.'  If FlagGoing, the
	client should change the flag's status to FlagNoExist when the flight
	time equals or exceeds `flight end.'

	BZFlag uses the first half of the flight time for FlagComing and the
	last half of the flight time for FlagGoing to make the flag appear or
	disappear from/to nothing.  This allows players to adjust to the
	appearance/disappearance of a flag.

  MsgTeamUpdate
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 0a | 74 | 6f |  team   |  size   |
	+----+----+----+----+----+----+----+----+
	| active  |  wins   | losses  |
	+----+----+----+----+----+----+
	Gives the current state of team `team.'  `Size' is the number of
	players on the team.  `Active' is the number of active players on the
	team.  An active player is one that can participate in the game (e.g.
	grab flags, shoot opponents, etc.);  a non-active player can only
	observe the game.  Only active players are considered when deciding
	if there are any players on a team (to decide if the team flag should
	be inserted or withdrawn from the game).  `Wins' and `losses' give the
	team's score (wins minus losses).

  MsgAlive
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 20 | 61 | 6c |     player id...
	+----+----+----+----+----+----+----+----+
	                    |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	|    forward (x)    |    forward (y)    |
	+----+----+----+----+----+----+----+----+
	|    forward (z)    |
	+----+----+----+----+
	Sent when a player comes alive.  A player that has joined is not on the
	game board until it sends this message, which declares that it is in
	game and gives the starting position and orientation.  Players are removed
	from the game board when killed and do not reappear until sending this
	message again.

  -->	+----+----+----+----+----+----+----+----+
	| 00 | 18 | 61 | 6c |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	|    forward (x)    |    forward (y)    |
	+----+----+----+----+----+----+----+----+
	|    forward (z)    |
	+----+----+----+----+
	A player sends this message to enter the game board.  Note that this
	version of the message differs from the one sent by the server in that
	it lacks the player id (which is implicit in the server connection).
	Players must send this message to change their state from dead to alive.
	Players should not send MsgAlive for a certain penalty period after
	being killed (but the server does not enforce a minimum time).  The
	currently penalty is ExplodeTime.

	`Position' gives the location of the player and `forward' gives the
	player's forward direction vector.


  MsgKilled
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 12 | 6b | 6c |     victim id...
	+----+----+----+----+----+----+----+----+
	                    |     killer id...
	+----+----+----+----+----+----+----+----+
	                    | shot id |
	+----+----+----+----+----+----+
	Sent by the server when a player is killed by itself or another player.
	The victim id is the player that was destroyed and the killer id is the
	player credited with the kill.  Shot id identifies which of the killer's
	shots hit the victim;  if the shot isn't a laser or shockwave, other
	players may assume the shot has stopped and needn't check themselves
	against the shot (or they can wait until the MsgShotEnd message arrives).

  -->	+----+----+----+----+----+----+----+----+
	| 00 | 0a | 6b | 6c |     killer id...
	+----+----+----+----+----+----+----+----+
	                    | shot id |
	+----+----+----+----+----+----+
	Sent by the victim when it detects that it's been hit, naming the killer
	and which of the killer's shots was the one that hit.  Note that the
	server does not verify kills in any way so it's trivial to `cheat' by
	claiming you've been killed;  this is generally unproductive.  It's
	also easy to cheat by never sending MsgKilled, making you almost
	immortal.  Don't do this.

  MsgGrabFlag
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 48 | 67 | 66 |     grabber id...
	+----+----+----+----+----+----+----+----+
	                    |flag num | flag id |
	+----+----+----+----+----+----+----+----+
	| status  |  type   |      owner id...
	+----+----+----+----+----+----+----+----+
	                    |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	|     launch (x)    |     launch (y)    |
	+----+----+----+----+----+----+----+----+
	|     launch (z)    |    landing (x)    |
	+----+----+----+----+----+----+----+----+
	|    landing (y)    |    landing (z)    |
	+----+----+----+----+----+----+----+----+
	|    flight time    |    flight end     |
	+----+----+----+----+----+----+----+----+
	| initial velocity  |
	+----+----+----+----+
	Sent by the server when a player is allowed to grab a flag.  This is
	both the notice to the player trying to grab the flag that it succeeded
	and the notice to all other players that the flag was grabbed.  Note
	that `grabber id' is redundant;  it's always equal to `owner id.'

	`Flag num' is the index of the flag.  Flags are indexed from zero up to
	the maximum number of flags minus one.

  -->	+----+----+----+----+----+----+
	| 00 | 02 | 67 | 66 |flag num |
	+----+----+----+----+----+----+
	Sent by a player when it wants to grab a flag.  Players should only
	send this message when their tank is within FlagRadius of a flag (that's
	any part of the tank, not simply the center) and on the ground.  The
	server will not verify this, but it will verify some other stuff.  The
	player must not assume it will be able to grab the flag.  Instead it has
	to wait for a MsgGrabFlag reply.

  MsgDropFlag
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 48 | 64 | 66 |     dropper id...
	+----+----+----+----+----+----+----+----+
	                    |flag num | flag id |
	+----+----+----+----+----+----+----+----+
	| status  |  type   |      owner id...
	+----+----+----+----+----+----+----+----+
	                    |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	|     launch (x)    |     launch (y)    |
	+----+----+----+----+----+----+----+----+
	|     launch (z)    |    landing (x)    |
	+----+----+----+----+----+----+----+----+
	|    landing (y)    |    landing (z)    |
	+----+----+----+----+----+----+----+----+
	|    flight time    |    flight end     |
	+----+----+----+----+----+----+----+----+
	| initial velocity  |
	+----+----+----+----+
	Sent by the server when a player is allowed to drop a flag.  This is
	both the notice to the player trying to drop the flag that it succeeded
	and the notice to all other players that the flag was dropped.  `Owner
	id' is meaningless in this message.

	`Flag num' is the index of the flag.  Flags are indexed from zero up to
	the maximum number of flags minus one.

  -->	+----+----+----+----+----+----+----+----+
	| 00 | 0c | 64 | 66 |    position (x)   |
	+----+----+----+----+----+----+----+----+
	|    position (y)   |    position (z)   |
	+----+----+----+----+----+----+----+----+
	Sent by a player when it wants to drop a flag.  Players should only
	send this message when they have a flag and only when the flag dropping
	constraints have been met (i.e. the flag can be dropped at any time, or
	the player got the antidote flag, etc.)  The player must not assume it
	has dropped the flag until it receives a MsgDropFlag in response.

	`Position' is in floating point and is the position of the tank that
	dropped the flag when it was dropped.

  MsgCaptureFlag
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 0c | 63 | 66 |    capturer id...
	+----+----+----+----+----+----+----+----+
	                    |flag num | team id |
	+----+----+----+----+----+----+----+----+
	Sent by the server when a team's flag has been captured.  `Capturer id'
	is the player who captured the flag.  `Flag num' is the index of the
	captured flag.  Flags are indexed from zero up to the maximum number of
	flags minus one.  `Team id' is the id of the team who's flag was
	captured.  Note that the player who captures the flag may be on the team
	that was captured.

	The server makes all players on the captured team dead.  This message is
	the only notice that the players are now dead so clients should act
	accordingly.  This is also the only notice that the capturer is no longer
	carrying a flag.

  -->	+----+----+----+----+----+----+
	| 00 | 02 | 63 | 66 | team id |
	+----+----+----+----+----+----+
	Sent by a player when it captures a team flag.  `Team id' is the id of
	the team who's flag was captured.  The player should wait until the
	server sends back the MsgCaptureFlag response before assuming the
	capture was successful.  A player should send this message only when
	1) its tank is at least halfway inside a team base, 2) the tank is on
	the ground, and 3) the player has its own team flag in an enemy base
	or the player has another team's flag in its own base.  Note that the
	server will not enforce any of these requirements.

  MsgShotBegin
  <--
  -->	+----+----+----+----+----+----+----+----+
	| 00 | 2c | 73 | 62 |    shooter id...
	+----+----+----+----+----+----+----+----+
	                    | shot id | position
	+----+----+----+----+----+----+----+----+
	    (x)   |    position (y)   | position
	+----+----+----+----+----+----+----+----+
	    (z)   |    velocity (x)   | velocity
	+----+----+----+----+----+----+----+----+
	    (y)   |    velocity (z)   |  time
	+----+----+----+----+----+----+----+----+
	          |flag type|     lifetime      |
	+----+----+----+----+----+----+----+----+
	Sent by the server when a player has fired a shot.  The identical message
	is send by a player when it fires a shot.  The server does not verify
	that the player is allowed to fire a shot.

	`Shooter id' identifies the player that did the shooting.  `Shot id' is
	a number that uniquely identifies to the shooter which shot this is.
	`Position,' `velocity,' `time,' and `lifetime' are floating point
	numbers.  `Position' is the starting position of the shot.  `Velocity'
	is the starting velocity of the shot.  `Time' is the time the shot has
	existed (probably 0.0).  `Lifetime' is how long the shot exists.  `Flag
	type' is a FlagId and is the type of flag the shooter had when the shot
	was fired.

	Clients are expected to compute the flight path of shots (except Guided
	Missiles) given the information in MsgShotBegin.  That includes handling
	ricochets, building impacts, and teleportation, but not tank impacts.
	Each player decides if it itself has been hit by a shot at each time step
	based on this flight path.  Since the path of a guided missile cannot be
	predicted, updates to a guided missile's path is sent throughout its
	flight by the shooter via MsgGMUpdate messages.

	A player should test itself against a shot using whatever algorithm it
	likes, but at a minimum should test the line segments the shot moves
	along during the time step against a tank aligned bounding box.
	Accounting for the linear motion of the tank over the time step is
	better and accounting for both the linear and angular motion is better
	still.  Note that the shot has a certain radius that should be taking
	into account during intersection testing.  This is particularly
	important when testing against a tank with the narrow flag.  Ideally,
	the shot would be exactly tested against the tank geometry.  However,
	that's more work than realistically necessary.

  MsgShotEnd
  <--
  -->	+----+----+----+----+----+----+----+----+
	| 00 | 0c | 73 | 65 |    shooter id...
	+----+----+----+----+----+----+----+----+
	                    | shot id | reason  |
	+----+----+----+----+----+----+----+----+
	Sent by the server when a shot has been terminated before its lifetime
	has expired.  The identical message is sent by the player that
	terminates the shot.  This is normally the player that has just been
	hit by the shot.  The server does not verify that the player has or
	has not been hit.  This message is also sent by the player that fired
	a guided missile when the shot hits the ground or a building (note that
	guided missiles don't ricochet).

	`Shooter id' identifies the player that fired the shot and `shot id'
	is the same as in the MsgShotBegin message for the shot.  `Reason' is
	one if the other players should show an explosion for the shot and
	zero otherwise.

  MsgScore
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 0c | 73 | 63 |     player id...
	+----+----+----+----+----+----+----+----+
	                    |  wins   | losses  |
	+----+----+----+----+----+----+----+----+
	Sent by the server when a player's score changes.  `Player id' is the
	player with the new score, and `wins' and `losses' are the new scores.

  -->	+----+----+----+----+----+----+----+----+
	| 00 | 04 | 73 | 63 |  wins   | losses  |
	+----+----+----+----+----+----+----+----+
	Sent by a player when its score changes.

  MsgTeleport
  <--	+----+----+----+----+----+----+----+----+
	| 00 | 0c | 73 | 63 |     player id...
	+----+----+----+----+----+----+----+----+
	                    |  from   |   to    |
	+----+----+----+----+----+----+----+----+
	Sent by the server when a player teleports.  `From' is the index of the
	teleporter face the player entered and `to' is the index of the face
	the player exited.

  -->	+----+----+----+----+----+----+----+----+
	| 00 | 04 | 73 | 63 |  from   |   to    |
	+----+----+----+----+----+----+----+----+
	Sent by a player when it passes through a teleporter.

  MsgMessage
  <--	+----+----+----+----+----+----+----+----+
	| length  | 6d | 67 |  from player id...
	+----+----+----+----+----+----+----+----+
	                    |   to player id...
	+----+----+----+----+----+----+----+----+
	                    |  team   |message...
	+----+----+----+----+----+----+----+----+
	...(MessageLen bytes)...
	+----+----+----+----+----+----+----+----+
	                                        |
	+----+----+----+----+----+----+----+----+
	Sent by the server when one player sends a message to others.  `Length'
	is the length of the message (18 + MessageLen bytes).  `From player id'
	is the player sending the message, `to player id' is the player to
	receive the message, and `team' is the team to receive the message.
	If `to player id' is all zeros then the message is being sent to
	everyone on `team'.  If `to player id' is all zeros and `team' is
	RogueTeam then the message is being sent to all players.  If `to player
	id' is not all zeros then `team' is ignored and the message is only for
	the identified player.

  -->	+----+----+----+----+----+----+----+----+
	| length  | 6d | 67 |    player id...
	+----+----+----+----+----+----+----+----+
	                    |  team   |message...
	+----+----+----+----+----+----+----+----+
	...(MessageLen bytes)...
	+----+----+----+----+----+----+----+----+
	                                        |
	+----+----+----+----+----+----+----+----+
	Sent by player to send a message to other players.  `Length' is is the
	length of the message (10 + MessageLen bytes).  `Player id' is the
	player to receive the message (or all zeros for all players on the
	given team) and `team' is the team to receive the message (or
	RogueTeam for all teams).

  MsgPlayerUpdate
	+----+----+----+----+----+----+----+----+
	| 00 | 2a | 70 | 75 |    player id...
	+----+----+----+----+----+----+----+----+
	                    | status  | position
	+----+----+----+----+----+----+----+----+
	    (x)   |    position (y)   | position
	+----+----+----+----+----+----+----+----+
	    (z)   |    velocity (x)   | velocity
	+----+----+----+----+----+----+----+----+
	    (y)   |    velocity (z)   | azimuth
	+----+----+----+----+----+----+----+----+
	          | angular velocity  |
	+----+----+----+----+----+----+
	Sent by players to notify other players of changes in its status,
	position, or velocity.  This message may be received directly from
	other players via multicast or from the server.

	`Status' is the tank's current status.  See PlayerStatus for the
	meaning of the bitfield's bits.  The FlagActive bit indicates whether
	the player's flag's special powers are active or not.  The Phantom
	Zone flag is an example of a flag that's active only some of the time.
	The CrossingWall bit indicates if the player's tank is intersected by
	a wall or teleporter;  other players can use this to draw the player
	in some special way.  CrossingWall could be computed by each player
	from other information;  it's only provided to save the other players
	the trouble.

	`Position,' `velocity,' `azimuth,' and `angular velocity' are floating
	point numbers.  `Azimuth' is the orientation of the tank in radians
	(the +x axis is 0.0, +y is pi/2).  `Angular velocity' is the change in
	azimuth per second.  `Position' is the tank position and `velocity' is
	the change in tank position per second.

	A player normally only sends an update when its position or orientation
	as could be predicted by other players differs from its true position
	or orientation by a certain tolerance.  Other players are expected to
	use the last known player data to extrapolate the current position and
	orientation.  This technique is known as dead reckoning and has two
	primary benefits:  network traffic is decreased since updates needn't
	be sent continuously and players on systems with slower frame rates
	appear to move smoothly to players on systems with faster frame rates.
	The dead reckoning algorithm is described in Appendix C.

	Players that fail to send updates often enough should be considered
	to be not responding.  Unresponsive players should be ignored until
	they start responding again.

  MsgGMUpdate
	+----+----+----+----+----+----+----+----+
	| 00 | 2e | 67 | 6d |    shooter id...
	+----+----+----+----+----+----+----+----+
	                    | shot id | position
	+----+----+----+----+----+----+----+----+
	    (x)   |    position (y)   | position
	+----+----+----+----+----+----+----+----+
	    (z)   |    velocity (x)   | velocity
	+----+----+----+----+----+----+----+----+
	    (y)   |    velocity (z)   |  time
	+----+----+----+----+----+----+----+----+
	          |    target id....
	+----+----+----+----+----+----+----+----+
	          |
	+----+----+
	Sent by players with active guided missiles to notify other players
	of changes to the motion of the guided missile.  This message may be
	received directly from other players via multicast or from the server.

	See MsgShotBegin for the meaning of most items.  `Time' is the current
	age of the shot (the time since being fired).  `Target id' is the
	current target of the guided missile or all zeros if there is no target.

	Players are expected to use dead reckoning to update the position and
	velocity of guided missiles between MsgGMUpdate messages.  Since
	guided missiles deviate from their predicted course only when the
	target changes, these message will be fairly rare.  Note that different
	players may have different ideas of where the target is and that that
	may cause slightly different courses.  This is generally not a problem.
</pre>


Appendix A:  Protocol.h<br>
-----------------------<br>

This is Protocol.h, the main protocol header file in BZFlag.<br><br>

All protocol codes are 16 bit unsigned integers and most can be interpreted as two
lowercase ascii characters.  u_int16_t is an unsigned 16 bit integer type.
<pre>
/*
 * Copyright 1996, Chris Schoeneman.
 * All Rights Reserved.
 *
 * This is UNPUBLISHED PROPRIETARY SOURCE CODE of Chris Schoeneman;  the
 * contents of this file may not be disclosed to third parties, copied or
 * duplicated in any form, in whole or in part, without the prior written
 * permission of Chris Schoeneman.  The copyright notice above does not
 * evidence any actual or intended publication of such source code.
 *
 * Communication protocol constants
 */

#ifndef BZF_PROTOCOL_H
#define	BZF_PROTOCOL_H

#include "common.h"

// magic to identify a BZFlag server.  first four characters are the
// BZFlag magic.  the last for give the version number.  the first
// digit is the major release, the second and third digits are the
// minor release, and the last character is the revision.  The major
// number should increment when there've been sweeping changes in the
// whole program.  The minor release should increment for smaller
// changes that make the new version incompatible with older servers
// or clients.  The revision should be incremented for minor changes
// that don't cause incompatibility.
// adding new flags or changing the communication protocol require
// minor release number changes.  adding new graphics effects normally
// only require incrementing the revision number.
const char* const	ServerVersion = "BZFS107b";

// well known service port number for BZFlag server
const int		ServerPort = 5155;

// address and port for multicast communication between players
const char* const	BroadcastAddress = "224.0.1.2";
const int		BroadcastPort = 5154;

// multicast ttl's
const int		DefaultTTL = 8;
const int		MaximumTTL = 255;

// maximum size of any message (including header and length fields)
const int		MaxPacketLen = 1024;

// null message code -- should never be sent
const u_int16_t		MsgNull = 0x0000;

// multicast message codes
const u_int16_t		MsgPlayerUpdate = 0x7075;		// 'pu'
const u_int16_t		MsgGMUpdate = 0x676d;			// 'gm'
const u_int16_t		MsgAudio = 0x6175;			// 'au'
const u_int16_t		MsgVideo = 0x7669;			// 'vi'

// server message codes
const u_int16_t		MsgAccept = 0x6163;			// 'ac'
const u_int16_t		MsgAlive = 0x616c;			// 'al'
const u_int16_t		MsgAddPlayer = 0x6170;			// 'ap'
const u_int16_t		MsgAcquireRadio = 0x6172;		// 'ar'
const u_int16_t		MsgCaptureFlag = 0x6366;		// 'cf'
const u_int16_t		MsgDropFlag = 0x6466;			// 'df'
const u_int16_t		MsgEnter = 0x656e;			// 'en'
const u_int16_t		MsgExit = 0x6578;			// 'ex'
const u_int16_t		MsgFlagUpdate = 0x6675;			// 'fu'
const u_int16_t		MsgGrabFlag = 0x6766;			// 'gf'
const u_int16_t		MsgGetWorld = 0x6777;			// 'gw'
const u_int16_t		MsgKilled = 0x6b6c;			// 'kl'
const u_int16_t		MsgMessage = 0x6d67;			// 'mg'
const u_int16_t		MsgNetworkRelay = 0x6e72;		// 'nr'
const u_int16_t		MsgReject = 0x726a;			// 'rj'
const u_int16_t		MsgRemovePlayer = 0x7270;		// 'rp'
const u_int16_t		MsgReleaseRadio = 0x7272;		// 'rr'
const u_int16_t		MsgShotBegin = 0x7362;			// 'sb'
const u_int16_t		MsgScore = 0x7363;			// 'sc'
const u_int16_t		MsgScoreOver = 0x736f;			// 'so'
const u_int16_t		MsgShotEnd = 0x7365;			// 'se'
const u_int16_t		MsgSuperKill = 0x736b;			// 'sk'
const u_int16_t		MsgTimeUpdate = 0x746f;			// 'to'
const u_int16_t		MsgTeleport = 0x7470;			// 'tp'
const u_int16_t		MsgSetTTL = 0x7474;			// 'tt'
const u_int16_t		MsgTeamUpdate = 0x7475;			// 'tu'

// world database codes
const u_int16_t		WorldCodeBase = 0x6261;			// 'ba'
const u_int16_t		WorldCodeBox = 0x6278;			// 'bx'
const u_int16_t		WorldCodeEnd = 0x6564;			// 'ed'
const u_int16_t		WorldCodeLink = 0x6c6e;			// 'ln'
const u_int16_t		WorldCodePyramid = 0x7079;		// 'py'
const u_int16_t		WorldCodeStyle = 0x7374;		// 'st'
const u_int16_t		WorldCodeTeleporter = 0x7465;		// 'te'
const u_int16_t		WorldCodeWall = 0x776c;			// 'wl'

// ping packet sizes, codes and structure
const u_int16_t		PingCodeOldReply = 0x0101;
const u_int16_t		PingCodeOldRequest = 0x0202;
const u_int16_t		PingCodeReply = 0x0303;
const u_int16_t		PingCodeRequest = 0x0404;

// radio flags
const u_int16_t		RadioToAll = 0x0001;

// rejection codes
const u_int16_t		RejectBadRequest = 0x0000;
const u_int16_t		RejectBadTeam = 0x0001;
const u_int16_t		RejectBadType = 0x0002;
const u_int16_t		RejectNoRogues = 0x0003;
const u_int16_t		RejectTeamFull = 0x0004;
const u_int16_t		RejectServerFull = 0x0005;

#endif // BZF_PROTOCOL_H
</pre>

Appendix B:  Other constants<br>
----------------------------<br>
<pre>
These constants are collected from various source files in BZFlag.

// string lengths
const int		CallSignLen = 32;	// including terminating NUL
const int		EmailLen = 128;		// including terminating NUL
const int		MessageLen = 128;	// including terminating NUL


// team constants
const int		NumTeams = 5;
enum PlayerType {				// types of things we can be
			TankPlayer,
			JAFOPlayer,
			ComputerPlayer
};
enum TeamColor {				// team id numbers
			NoTeam = -1,
			RogueTeam = 0,
			RedTeam = 1,
			GreenTeam = 2,
			BlueTeam = 3,
			PurpleTeam = 4
};


// game styles
enum GameStyle {					// bitfield constants
			PlainGameStyle =	0x0000,
			TeamFlagGameStyle =	0x0001,	// capture the flag
			SuperFlagGameStyle =	0x0002,	// superflags allowed
			RoguesGameStyle =	0x0004,	// rogues allowed
			JumpingGameStyle =	0x0008,	// jumping allowed
			InertiaGameStyle =	0x0010,	// momentum for all
			RicochetGameStyle =	0x0020,	// all shots ricochet
			ShakableGameStyle =	0x0040,	// can drop bad flags
			AntidoteGameStyle =	0x0080,	// anti-bad flags
			TimeSyncGameStyle =	0x0100	// time sync'd to srvr
};


// universe info
const float		Gravity =	-9.8f;		// meters/sec/sec
const float		WorldSize =	800.0f;		// meters
const float		BaseSize =	60.0f;		// meters

// rough tank geometry
const float		TankLength =	6.0f;		// meters
const float		TankWidth =	2.8f;		// meters
const float		TankHeight =	2.05f;		// meters
const float		TankRadius =	0.72f*TankLength;// meters
const float		MuzzleHeight =	1.57f;		// meters
const float		MuzzleFront =	TankRadius+0.1f;// meters

// rough shot geometry
const float		ShotRadius =	0.5f;		// meters
const float		ShotLength =	0.5f;		// meters
const float		ShotTailLength=	10.0f;		// meters

// outer wall geometry
const float		WallPosition =	0.5f*WorldSize +// meters
						0.75f * TankLength;
const float		WallHeight =	3.0f*TankHeight;// meters

// pyramid geometry
const float		PyrBase =	4.0f*TankHeight;// meters
const float		PyrHeight =	5.0f*TankHeight;// meters

// box geometry
const float		BoxBase =	5.0f*TankLength;// meters
const float		BoxHeight =	6.0f*MuzzleHeight;// meters

// teleporter geometry (My God, it's full of stars...)
const float		TeleUnit =	0.4f * TankWidth;// meters
const float		TeleWidth =	1.0f * TeleUnit;// meters
const float		TeleBreadth =	4.0f * TeleUnit;// meters
const float		TeleHeight =	9.0f * TeleUnit;// meters

// tank performance info
const float		TankSpeed =	25.0f;		// meters/sec
const float		TankAngVel =	M_PI / 4.0f;	// radians/sec
const float		ShotSpeed =	100.0f;		// meters/sec
const float		ShotRange =	350.0f;		// meters
const float		ReloadTime =	ShotRange / ShotSpeed;	// seconds

// other game info
const float		ExplodeTime =	5.0f;		// seconds
const float		TeleportTime =	1.0f;		// seconds
const float		FlagRadius =	2.5f;		// meters

// player status constants
enum PlayerStatus {				// bitfield constants
			DeadStatus =	0x0000,	// not alive, not paused, etc.
			Alive =		0x0001,	// player is alive
			Paused =	0x0002,	// player is paused
			Exploding =	0x0004,	// currently blowing up
			Teleporting =	0x0008,	// teleported recently
			FlagActive =	0x0010,	// flag special powers active
			CrossingWall =	0x0020,	// tank crossing building wall
			Falling =	0x0040	// tank accel'd by gravity
};

// flag constants
enum FlagStatus {
			FlagNoExist = 0,
			FlagOnGround,
			FlagOnTank,
			FlagInAir,
			FlagComing,
			FlagGoing
};
enum FlagType {					// bitfield constants
			FlagNormal = 0,		// permanent flag
			FlagUnstable = 1,	// disappears after use
			FlagSticky = 2		// can't be dropped normally
};
enum FlagId {
			NullFlag = 0,		// the null flag (or rogue
			NoFlag = RogueTeam,	// team's non-existent flag)

			// team flags:
			RedFlag = RedTeam,	// red team's flag
			GreenFlag = GreenTeam,	// green team's flag
			BlueFlag = BlueTeam,	// blue team's flag
			PurpleFlag = PurpleTeam,// purple teams flag

			// the good super flags:
			VelocityFlag,		// drive faster
			QuickTurnFlag,		// turn faster
			OscOverthrusterFlag,	// go rent Buckaroo Banzai
			RapidFireFlag,		// shoot faster
			MachineGunFlag,		// shoot very fast, close range
			GuidedMissileFlag,	// seeks the 'locked on' target
			LaserFlag,		// infinite speed & range shot
			RicochetFlag,		// shots bounce off walls
			SuperBulletFlag,	// shoots through schools
			InvisibleBulletFlag,	// your shots are invisible
			StealthFlag,		// makes you invisible on radar
			TinyFlag,		// makes you very small
			NarrowFlag,		// makes you infinitely thin
			ShieldFlag,		// can get shot once harmlessly
			SteamrollerFlag,	// kill by touching
			ShockWaveFlag,		// kills all within small range
			PhantomZoneFlag,	// teleporters phantomize you
			GenocideFlag,		// kill one kills whole team
			JumpingFlag,		// lets your tank jump
			IdentifyFlag,		// lets you identify other flags
			CloakingFlag,		// make you invisible out window

			// the bad super flags
			ColorblindnessFlag,	// can't see team colors
			ObesityFlag,		// makes you really fat
			LeftTurnOnlyFlag,	// can't turn right
			RightTurnOnlyFlag,	// can't turn left
			MomentumFlag,		// gives you lots of momentum
			BlindnessFlag,		// can't see out window
			JammingFlag,		// radar doesn't work
			WideAngleFlag,		// fish eye view (sounds good
						// but isn't)

			// special flags for size of team and super-flag sets
			FirstFlag = RedFlag,
			LastFlag = WideAngleFlag,
			FirstTeamFlag =	RedFlag,
			LastTeamFlag =	PurpleFlag,
			FirstSuperFlag = VelocityFlag,
			LastSuperFlag =	LastFlag
};
// Super flag characteristic modifiers
const float		VelocityAd =	1.5f;		// 50% faster
const float		AngularAd =	1.5f;		// 50% faster turns
const float		RFireAdVel =	1.5f;		// 50% faster shots
const float		RFireAdRate =	2.0f;		// 2x faster reload
const float		RFireAdLife =	1.0f/RFireAdRate;// 1/2 normal lifetime
const float		MGunAdVel =	1.5f;		// 50% faster shots
const float		MGunAdRate =	10.0f;		// 10x faster reload
const float		MGunAdLife =	1.0f/MGunAdRate;// 1/10 normal lifetime
const float		LaserAdVel =	1000.0f;		// 1000x faster shots
const float		LaserAdRate =	0.5f;		// 1/2x faster reload
const float		LaserAdLife =	0.1f;		// 1/10 normal lifetime
const float		GMissileAng =	M_PI / 5.0f;	// max turn rate (rad/s)
const float		TinyFactor =	0.4f;		// 40% normal size
const float		ShieldFlight =	2.7f;		// flag goes 170% higher
const float		SRRadiusMult =	2.0;		// 200% normal radius
const float		ShockAdLife =	0.20f;		// 20% normal lifetime
const float		ShockInRadius =	TankLength;	// size of tank
const float		ShockOutRadius=	2.0f * BoxBase;	// size of building
const float		JumpVelocity =	19.0f;		// m/s
const float		IdentityRange =	50.0f;		// meters
const float		ObeseFactor =	2.5f;		// 250% normal size
const float		WideAngleAng =	M_PI / 1.8f;	// 100 degree fov
const float		MomentumLinAcc=	1.0f;		// 
const float		MomentumAngAcc=	1.0f;		//
const float		MagnetPower =	0.0f;		// off
</pre>

Appendix C:  Dead Reckoning<br>
---------------------------<br>
<pre>
[to do]
</pre>
</p>
end;
$doc->end();

?>
