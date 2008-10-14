using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Xml;
using System.Xml.Serialization;

using bzfstats;

namespace PlayerDatabase
{
    public class PlayerID
    {
        public string callsign = string.Empty;
        public string bzID = string.Empty;
    }

    public class Player
    {
        public string lastRecordUpdate = string.Empty;

        public PlayerID playerID = new PlayerID();
        public string motto = string.Empty;
        public string lastServer = string.Empty;
        public string currentServer = string.Empty;

        public string timePlayed = string.Empty;
        public string lastJoin = string.Empty;
        public string lastPart = string.Empty;

        public string totalWins = string.Empty;
        public string totalLosses = string.Empty;
        public string totalTKs = string.Empty;

        public string averageGame = string.Empty;
    }

    public class PlayerDB
    {
        public PlayerDB(Setup setup)
        {
        }

        public List<Player> findPlayerByCallsign(string callsign)
        {
            return new List<Player>();
        }

        public List<Player> findPlayerByBZID(string bzid)
        {
            return new List<Player>();
        }

        public List<PlayerID> listPlayers()
        {
            return new List<PlayerID>();
        }

        public Player addPlayer(PlayerID playerID)
        {
            return new Player();
        }

        public void updatePlayer(Player player)
        {
        }
    }
}
