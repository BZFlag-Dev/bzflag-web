<?
include "../document.php";

$doc = new Document;
$doc->begin("Client/Client communication");
print <<< end
<p>BZFlag clients send certain information to other clients during a game. This information includes: status, position, orientation, and linear and angular velocity. This state can change quickly and getting it late is no better than not getting it at all. So client to client packets are sent using UDP, a datagram protocol.</p>
<p>UDP is unreliable, meaning that packets are not guaranteed to be delivered. It's also connectionless, meaning we don't even know if anybody is listening for the message. However, it's also fast because it's so simple. UDP suits our needs because: we need fast delivery; we don't care if a message is lost because another will arrive shortly afterwards; and we don't care if the receiver isn't listening.</p>
<p>BZFlag clients need to send their information to all the other clients. There are several ways to do this. First, each client can send each message to all the other clients. If there are N clients then each message is sent N-1 times. Traffic grows as the square of clients. This is slow and wasteful.</p>
<p>Alternatively, each client can send all messages to one special computer (for example, the server) and that computer can forward the messages to all the other players. This makes life easier on the clients and much harder on the server. It also delays the deliverly of each message. And traffic from the server still grows as N squared (though traffic to the server grows linearly).</p>
<p>Another possibility is to broadcast messages. A broadcast message is sent once and received by all computers on a subnet. This scales linearly and minimizes network traffic but broadcast traffic is never routed outside the subnet, so it means only clients on the same subnet of the same local area network could play together.</p>
<p>And finally, the technique BZFlag actually uses, is multicasting. Multicast is similar to broadcast in that a packet is sent once and delivered to multiple computers. However, multicast messages can be routed, and routed efficiently. Multicast routers communicate with each other to ensure that multicast traffic is only delivered to those subnets with at least one computer interested in that traffic. Note that multicast in a subnet is automatic, being equivalent to broadcast.</p>
<p>Unfortunately, multicast routers aren't widespread yet. So BZFlag must have a fallback. It uses the second technique, using the server as a relay system. The server uses multicast as much as possible, and uses the regular client/server connection to send client traffic that can't be delivered via multicast. It would be better to use UDP for this, but the client/server connection was already there and i was lazy.</p>
end;
$doc->end();

?>
