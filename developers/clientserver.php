<?
include "../document.php";

$doc = new Document;
$doc->begin("Client/Server communication");
print <<< end
<p>The BZFlag server manages games. It is the judge and arbitrar between clients. It manages game-global resources and information, such as flags and scores. It does <b>not</b> enforce game rules. Clients are trusted to follow the rules, saving the server the trouble of validating kills and client manouvering. </p>
<p>For example, the server is not told of changes to a client's position on the game board. Therefore the server cannot enfore the rule that players can't drive through walls (without the OO or PH flags). This allows each client to run freely. If it had to wait for the server to okay each movement, it would be impossible to play BZFlag except on fast or dedicated networks. <p>
<p>Furthermore, clients inform the server when they've killed another client player. The server doesn't verify the kill was legal beyond checking to see if someone has already claimed a kill on the same player. If more than one player claims a kill or tries to grab a particular flag, the server decides which player gets the kill or the flag. </p>
<p>Client/server communication is mostly limited to those messages that must be successfully delivered. These include, among others, notifications of players coming alive or being killed, notifications of shots being fired, flags coming or going or being grabbed or dropped. </p>
<p>Client/server communication in BZFlag is through TCP/IP. TCP/IP has the advantages of being bidirectional, reliable, and sequential. Bidirectional means each side of the connection can send to the other. Reliable means that each message will be delivered to the destination. Sequential means that messages (on a particular connection) will be received in the order they were sent.</p>
<p>The main disadvantage of TCP/IP is that it's slow. Reliability may require a given message to be sent several times, until the receiver acknowledges its receipt. While delays can cause problems, there really isn't a way around it. Messages between the client and server are specifically those messages that must not be lost. If we didn't use TCP/IP we'd simply have to implement something that did the same thing, and which would necessarily suffer from the same drawbacks.</p>
<p>There are some instances where client/server communication doesn't use TCP/IP. These all involve client attempts to locate servers on the network. Clients send broadcast and multicast `pings' which the servers listen for and respond to in kind. These messages are not part of normal game communication, which all takes place using TCP/IP. </p>
end;
$doc->end();

?>
