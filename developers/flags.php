<?
include "../document.php";

$doc = new Document;
$doc->begin("flag list");
print <<< end
This is a list of flags that are being considered. Discussion/imlementation
of these is encouraged.<br><br>

Not a flag, but a heavily requested feature, is multiple flags at once. Each tank could posses up to N flags at once (N set on the server) and flag powers would combine to the extent that that makes sense. It's been suggested that you can have one offensive flag (e.g. GM) and one defensive flag (e.g. shield) at once, and any number of bad flags. This feature requires significant fleshing out and plenty of work.<br><br>

If you do implement a flag keep these things in mind:
<ul>
  <li>All powerful flags aren't fun.
  <li>Flags that ensure your immediate destruction aren't fun.
  <li>Flags that aren't useful aren't fun. 
</ul>

Based on these observations we have the following rules:<br>
<dl>
<dt><i>Flag power must be limited.</i></dt>
<dd>For example, BZFlag has stealth and cloaking flags, but no stealth-and-cloak flag. Such a tank would be virtually impossible to destroy. Nobody likes to play against someone who can't lose.<br><br>
The guided missile and laser flags are both exceptionally powerful flags, but neither make the owner invincible. Laser takes twice as long to reload and a guided missile can be dodged while still firing on the owner. You've gotta be good, but it can be done.</dd><br>
<dt><i>Flag powers can counteract/balance each other.</i></dt>
<dd>Some flags are handily defeated with another kind of flag. The oscillation overthruster can be destroyed by a super bullet or a shock wave (or a genocide). Cloaking is effective against laser and stealth is good against guided missile. Ricochet is useful against ricochet.<br><br>
Consider adding counterbalancing flags together. Or consider adding a flag that acts as a counterbalance to an existing flag or flags. Also consider making your new flag counterbalanced by an existing flag; superbullet is often a good choice.</dd><br>
<dt><i>Don't add flags that (almost) instantly kill the owner.</i></dt>
<dd>I considered adding a Death flag that would instantly kill the tank that grabbed it. I changed my mind because that's not fun and seriously dampens interest in picking up flags. Mines are similar. You're driving around and *boom* you're dead. Nothing you could've done to avoid it, no mistake you made. You're just dead. People don't mind losing so much if they know it was because they did something wrong.<br><br>
Even worse is a flag that disables your radar and vision. You're basically dead. You can shoot at random and hope to get lucky, but no amount of skill can save you. And what's worse than the Death flag is that you have to stagger around waiting for someone to kill you.<br><br>
A flag that seriously disables a tank is not as bad because the player can still use skill to defeat as many enemies as possible before getting shot. Thus we have flags like left turn only and obesity.</dd><br>
<dt><i>Flags should require new skills</i></dt>
<dd>Flags that require new skills keep the game interesting. If there's still more to learn about using ricochet or guided missile, or more to learn about fighting against them, then people will keep playing.<br><br>
Some flags seem pretty dull to some players. The steamroller and the phantom zone come to mind, or quick turn. These aren't the most powerful flags, but they do have their uses and some people enjoy them. However, i think i should have removed SR and PZ. Steamroller's usefulness is very limited and looks pretty weak compared to shock wave. PZ tends to encourage very defensive play, which slows the game down. But quick turn can be very effective when used well; put it in a tight furball and it'll do serious damage. You just have to learn how to take full advantage of it.</dd>
</dl><br><br>

The list itself has been moved to a <a href="/wiki/FlagList">wiki page</a>.

end;
$doc->end();
?>
