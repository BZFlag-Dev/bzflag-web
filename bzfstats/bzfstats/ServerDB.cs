using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Xml;
using System.Xml.Serialization;

using bzfstats;

namespace ServerDatabase
{
    public class GamePlayer
    {
        public string callsign = string.Empty;
        public string motto = string.Empty;
        public string bzID = string.Empty;
        public string team = string.Empty;
        public int wins;
        public int losses;
        public int teamkills;
    }

    public class ServerHost
    {
        public string hostname = string.Empty;
        public int port;
        public string ip = string.Empty;
    }

    public class GameServer
    {
        public ServerHost host = new ServerHost();
        public string description = string.Empty;

        public List<GamePlayer> players = new List<GamePlayer>();
    }

    public class ServerDB
    {
        string dbDir = string.Empty;

        List<GameServer> servers = null;

        public ServerDB(Setup setup)
        {
            dbDir = (string)setup.serverDBDir.Clone();

            // read that sucker in

            FileInfo dbFile = new FileInfo(dbDir+"serverdb.xml");

            if (dbFile.Exists)
            {
                XmlSerializer xml = new XmlSerializer(typeof(List<GameServer>));

                FileStream fs = dbFile.OpenRead();
                StreamReader file = new StreamReader(fs);
                try
                {
                    servers = (List<GameServer>)xml.Deserialize(file);
                }
                catch (System.Exception e)
                {
                    Console.WriteLine("Error reading server db " + e.ToString());
                }

                file.Close();
                fs.Close();
            }
            if (servers == null)
                servers = new List<GameServer>();
        }

        public List<GameServer> findServersByHost(string host)
        {
            List<GameServer> temp = new List<GameServer>();
            foreach(GameServer g in servers)
            {
                if (g.host.hostname == host)
                    temp.Add(g);
            }
            return temp;
        }

        public List<GameServer> findServersByHost(ServerHost host)
        {
            List<GameServer> temp = new List<GameServer>();
            foreach(GameServer g in servers)
            {
                if (g.host.hostname == host.hostname && g.host.port == host.port)
                    temp.Add(g);
            }
            return temp;
        }

        public List<ServerHost> listServers()
        {
            List<ServerHost>  temp = new List<ServerHost>();
            foreach (GameServer g in servers)
            temp.Add(g.host);
            return temp;
        }

        public GameServer addServer(ServerHost host)
        {
            List<GameServer> temp = findServersByHost(host);
            if (temp.Count > 0)
                return temp[0];

            GameServer server = new GameServer();
            server.host = host;
            servers.Add(server);
            return server;
        }

        public void updateServer(GameServer server)
        {
            // flush the entire thing for now
            // later throw these into a database,
            // or at least do a file per server

            FileInfo dbFile = new FileInfo(dbDir + "serverdb.xml");

            XmlSerializer xml = new XmlSerializer(typeof(List<GameServer>));

            FileStream fs = dbFile.OpenWrite();
            StreamWriter file = new StreamWriter(fs);

            xml.Serialize(file, servers);
            file.Close();
            fs.Close();

        }
    }
}
