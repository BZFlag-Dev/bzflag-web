<?
include "../document.php";

$doc = new Document;
$doc->begin("playing");
print <<< end
<b>So how do I start playing?</b>
<p> After starting the game (by double clicking on the BZFlag icon or running bzflag from the shell), you're presented with the main menu. If there's no menu visible, press the Esc (Escape) key. The up and down arrows control the selection marker and the enter key selects. Use the Esc key to back up to the previous menu (and to dismiss the `Main Menu'). You cannot use the mouse to navigate the menus.</p>
<p> Select the `Join Game' menu (move the selection marker to that line and press enter). You should now enter a callsign and choose a team. Move the selection marker to `Callsign' and type in a name to identify you to other players. Now move the selection marker to `Team' and use the left and right arrows to choose a team. </p>
<p> Now you're ready to choose a server. If you know them, you can type in the name and port number of a server directly in the `Server' and `Port' fields. Or you can have BZFlag look for servers for you. Choose `Find Server' and you'll get a list of all the servers BZFlag could find. If the list isn't empty, use the arrow keys to select a server and press enter. You'll go back to the `Join Game' menu and the `Server' and `Port' fields will be filled in. </p>
<p> If the server list is empty, you'll need to get the server name and port numbers some other way and enter them manually, or you can start a server on your own system using `Start Server'. Select this menu item then choose the server settings you want using the arrow keys. When you're ready, select the `Start' item at the bottom. The message `Server Started' should appear. Press Esc to go back to the `Join Game' menu. Now go back to the `Find Server' menu and your system should appear in the server list. Select it. </p>
<p> Finally, now that you have a callsign, team, server and port, select the `Connect' item. If all goes well, you'll be connected to the game. If something goes wrong, you'll see an error message explaining what to do to correct the problem. </p>
<p> Settings are remembered from previous sessions, so you should only need to set your callsign once. If you always play on the same team and connect to the same server, you can simply choose `Connect' from the `Join Game' menu after starting up. </p>
end;
$doc->end();

?>
