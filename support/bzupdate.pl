#!/usr/bin/perl -w

use warnings;
use strict;

use BZFlag::Info;
use DBI();
use Data::Dumper;
use Time::HiRes qw(gettimeofday);

my $debug = 0; # if (shift eq '-d');
$debug = 1 if (shift eq '-d');

# get settings
use vars qw($dbhost $dbname $dbuname $dbpass);
#$dbhost = "localhost";
#$dbname = "bzflag";
#$dbuser = "bzflag";
#$dbpassword = "password";
do ".mysqlconnect";
# Connect to the database
my $dbh = DBI->connect("DBI:mysql:$dbname:$dbhost",
		       "$dbuname", "$dbpass",
		       {'RaiseError' => 1});

my $bzinfo = new BZFlag::Info;

for (;;) {

    alarm(300);

    my $starttime = gettimeofday;
    my $serverlist = $bzinfo->serverlist;
    my $refreshquery = "WHERE ";

    foreach(sort keys(%{ $serverlist->{servers} })) {
	my $server = $_;
	my $numplayers = $serverlist->{servers}->{$_}->{numplayers};
	print "$$: $server $numplayers\n" if $debug;

	$dbh->do("DELETE FROM `currentplayers` WHERE server = '$server'");
	$dbh->do("DELETE FROM `teaminfo` WHERE server = '$server'");
	$dbh->do("DELETE FROM `serverinfo` WHERE server = '$server'");

	$refreshquery .= "server != '$server' AND ";

	my $query = "INSERT INTO `serverinfo` (`server` , `players` ,
	`ctf` , `superflags` , `jumping` , `inertia` ,
	`ricochet` , `shakable` , `antidoteflags` , `handicap` ,
	`rabbitchase` , `maxshots` , `shakewins` , `shaketimeout` ,
	`maxplayerscore` , `maxteamscore` , `maxtime` , `maxplayers` ,
	`roguesize` , `roguemax` , `redsize` , `redmax` , `greensize` ,
	`greenmax` , `bluesize` , `bluemax` , `purplesize` ,
	`purplemax` , `observersize` , `observermax` , `ip` ,
	`version` , `description`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,
	?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
	?, ?, ?, ?);";

	my $sth = $dbh->prepare($query);
	$sth->execute(
		      $server                                                                        ,
		      $numplayers                                                                    ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{ctf})           ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{superflags})    ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{jumping})       ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{inertia})       ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{ricochet})      ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{shakable})      ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{antidoteflags}) ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{handicap})      ,
		      &tf_yn($serverlist->{servers}->{$_}->{serverconfig}->{style}->{rabbitchase})   ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{maxshots}                       ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{shakewins}                      ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{shaketimeout}                   ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{maxplayerscore}                 ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{maxteamscore}                   ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{maxtime}                        ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{maxplayers}                     ,
		      $serverlist->{servers}->{$_}->{roguesize}                                      ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{roguemax}                       ,
		      $serverlist->{servers}->{$_}->{redsize}                                        ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{redmax}                         ,
		      $serverlist->{servers}->{$_}->{greensize}                                      ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{greenmax}                       ,
		      $serverlist->{servers}->{$_}->{bluesize}                                       ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{bluemax}                        ,
		      $serverlist->{servers}->{$_}->{purplesize}                                     ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{purplemax}                      ,
		      $serverlist->{servers}->{$_}->{observersize}                                   ,
		      $serverlist->{servers}->{$_}->{serverconfig}->{observermax}                    ,
		      $serverlist->{servers}->{$_}->{ip}                                             ,
		      $serverlist->{servers}->{$_}->{version}                                        ,
		      $serverlist->{servers}->{$_}->{description}                                    ,
		      );

	next if ($numplayers == 0);

	my $serverinfo = $bzinfo->queryserver(Server => $_);

	unless (defined($serverinfo)) {
	    print $bzinfo->geterror . "\n" if $debug;
	    next;
	}

	#print Dumper($serverinfo) if $debug;
	# Insert the team info into the database
	foreach (keys(%{ $serverinfo->{teams} })) {
	    my $team   = $_;
	    my $size   = $serverinfo->{teams}->{$_}->{size};
	    my $score  = $serverinfo->{teams}->{$_}->{score};
	    my $wins   = $serverinfo->{teams}->{$_}->{wins};
	    my $losses = $serverinfo->{teams}->{$_}->{losses};

	    $dbh->do("INSERT INTO `teaminfo` VALUES ('', '$server', '$team', '$size', '$score', '$wins', '$losses')");
	    next;
	}

	# Foreach player on the server
	foreach (keys(%{ $serverinfo->{players} })) {

	    # The default is that this player is not found in the database
	    my $newplayer = 1;

	    my $callsign = $_;
	    my $email    = $serverinfo->{players}->{$_}->{email};
	    my $score    = $serverinfo->{players}->{$_}->{score};
	    my $tks      = $serverinfo->{players}->{$_}->{tks};
	    my $team     = $serverinfo->{players}->{$_}->{team};
	    my $wins     = $serverinfo->{players}->{$_}->{wins};
	    my $losses   = $serverinfo->{players}->{$_}->{losses};

	    $callsign =~ s/\\/\\\\/g;
	    $callsign =~ s/'/\\'/g;
	    $email =~ s/\\/\\\\/g;
	    $email =~ s/'/\\'/g;

	    # Compute the raw kill ratio
	    my $killratio = (($wins != 0)?($score/$wins):(0));

	    # Compute the "strength index"
	    my $strengthindex = $killratio * abs($score / 2);

	    # Actually record the information
	    $dbh->do("INSERT INTO `currentplayers` (`callsign`, `email`, `server`, `score`, `tks`, `strengthindex`, `killratio`, `team`) VALUES ('$callsign', '$email', '$server', '$score', '$tks', '$strengthindex', '$killratio', '$team')");

	    # Look to see if they have a profile in the database
	    my $sth = $dbh->prepare("SELECT * from playerinfo WHERE `callsign` = '$callsign'");
	    $sth->execute();
	    while (my $row = $sth->fetchrow_hashref()) {
		# This player isn't new
		$newplayer = 0;

		# Update the record with new fresh info
		my @r = (
			 $row->{'id'},                # 0
			 $row->{'callsign'},          # 1
			 $row->{'ip'},                # 2
			 $row->{'email'},             # 3
			 $row->{'highscore'},         # 4
			 $row->{'lowscore'},          # 5
			 $row->{'mosttks'},           # 6
			 $row->{'leasttks'},          # 7
			 $row->{'highstrengthindex'}, # 8
			 $row->{'lowstrengthindex'},  # 9
			 $row->{'highkillratio'},     # 10
			 $row->{'lowkillratio'},      # 11
			 $row->{'mostwins'},          # 12
			 $row->{'mostlosses'},         # 13
			 $row->{'lastseen'},          # 14
			 $row->{'lastserver'},        # 15
			 );

		# Update any info that needs updating about the player
		my $updatecmd;
		$updatecmd .= "`email`             = '$email', ";
		$updatecmd .= "`highscore`         = '$score', "         unless ($r[4]  > $score);
		$updatecmd .= "`lowscore`          = '$score', "         unless ($r[5]  < $score);
		$updatecmd .= "`mosttks`           = '$tks', "           unless ($r[6]  > $tks);
		$updatecmd .= "`leasttks`          = '$tks', "           unless ($r[7]  < $tks);
		$updatecmd .= "`highstrengthindex` = '$strengthindex', " unless ($r[8]  > $strengthindex);
		$updatecmd .= "`lowstrengthindex`  = '$strengthindex', " unless ($r[9]  < $strengthindex);
		$updatecmd .= "`highkillratio`     = '$killratio', "     unless ($r[10]  > $killratio);
		$updatecmd .= "`lowkillratio`      = '$killratio', "     unless ($r[11]  < $killratio);
		$updatecmd .= "`mostwins`          = '$wins', "          unless ($r[12]  > $wins);
		$updatecmd .= "`mostlosses`        = '$losses', "        unless ($r[13]  > $losses);
		$updatecmd .= "`lastserver`        = '$server', ";

		$dbh->do("UPDATE playerinfo SET $updatecmd `lastseen` = '".time."' WHERE `callsign` = '$callsign'");

	    }

	    # If this player's new, let's make a new record for them
	    if ($newplayer) {
		$dbh->do("INSERT INTO playerinfo (`callsign`, `email`, `highscore`, `lowscore`, `mosttks`, `leasttks`, `highstrengthindex`, `lowstrengthindex`, `highkillratio`, `lowkillratio`, `mostwins`, `mostlosses`, `lastseen`, `lastserver`) VALUES ('$callsign', '$email', '$score', '$score', '$tks', '$tks', '$strengthindex', '$strengthindex', '$killratio', '$killratio', '$wins', '$losses', '".time."', '$server')");
	    }
	}

    }

    my $endtime = gettimeofday();
    my $delta = gettimeofday() - $starttime;
    print "$delta seconds to scan servers\n" if $debug;

    #print "$refreshquery\n" if $debug;

    $dbh->do("DELETE FROM `currentplayers` $refreshquery 1");
    $dbh->do("DELETE FROM `teaminfo` $refreshquery 1");
    $dbh->do("DELETE FROM `serverinfo` $refreshquery 1");
    $dbh->do("DELETE FROM `miscinfo`");
    $dbh->do("INSERT INTO `miscinfo` VALUES (".time.")");

    $endtime = gettimeofday();
    $delta = $endtime - $starttime;
    print "$delta seconds to scan and delete old records\n" if $debug;

    if ($delta < 300) {
	my $sleeptime = 300 - $delta;
	print "sleeping for $sleeptime seconds\n" if $debug;
	sleep($sleeptime)
    }
}

sub tf_yn ($) {
    my $true = shift;
    return ($true == 1 ? 'Y' : 'N');
}
