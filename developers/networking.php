<?
include "../document.php";

$doc = new Document;
$doc->begin("networking");
print <<< end
<p>BZFlag uses standard IP (Internet) networking protocols. The code is written to the BSD socket interface, with adjustments for winsock on win32 platforms. Future revisions may centralize the network code into one file behind a single class interface to simplify porting to platforms without the BSD socket API.</p>
<p>Most communication is non-blocking. That is, reads and sends return immediately instead of waiting for data to become available or to be sent. That's important since BZFlag is a real time game. It can't afford to get stuck waiting for a packet to come in. (The one exception to this is when BZFlag connects to a server, and this should get fixed someday.)</p>
<p>Communication is multiplexed using the select() call, which notifies the caller which sockets have data pending for read and/or are ready for sends. Instead of checking for incoming data by trying to read every socket and testing for success, we simply ask the system which sockets have data and read on those only. This also means that we use just a single thread for network communication.</p>
<p>BZFlag uses two distinct transport protocols to communicate with the server and with other players. Server communication uses TCP/IP for reliability. Player communication uses UDP multicast for speed and efficiency. See the other pages for more details.</p>
end;
$doc->end();

?>
