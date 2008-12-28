using System;
using System.Collections.Generic;
using System.Text;
using System.Net;
using System.IO;
using System.Net.Mail;
using System.Web;
using System.Security.Cryptography;
using System.Xml;
using System.Xml.Serialization;
using System.Threading;
using System.ComponentModel;

using HTTPConnections;
using ServerDatabase;
using PlayerDatabase;

namespace bzfstats
{
    public class MimeTypePair
    {
        public string key;
        public string value;

        public MimeTypePair()
        {
            key = string.Empty;
            value = string.Empty;
        }

        public MimeTypePair(string k, string v)
        {
            key = k;
            value = v;
        }
    }

    public class Setup
    {
        public List<string> hosts = new List<string>();
        public List<MimeTypePair> mimeTypes = new List<MimeTypePair>();
        public string serverDBDir = string.Empty;
        public string playerDBDir = string.Empty;
    }

    class Server
    {
        public Setup setup = new Setup();

        public ServerDB serverDB;
        public PlayerDB playerDB;

        public Server(string[] args)
        {
            string configFile;
            string argConf = string.Empty;

            // load the setup from the args
            if (args.Length > 0)
                configFile = argConf = args[0];
            else
                configFile = "./config.xml";

            bool saveConfig = false;

            FileInfo conf = new FileInfo(configFile);

            bool confRead = false;

            if (conf.Exists)
            {
                XmlSerializer xml = new XmlSerializer(typeof(Setup));

                FileStream fs = conf.OpenRead();
                StreamReader file = new StreamReader(fs);
                try
                {
                    setup = (Setup)xml.Deserialize(file);
                    confRead = true;
                }
                catch (System.Exception e)
                {
                    Console.WriteLine("Error reading config " + e.ToString());
                }

                file.Close();
                fs.Close();
            }

            if (!confRead)
            {
                // default setups?
                setup.hosts.Add("http://localhost:88/");
                if (argConf.Length > 0)// try to save out the conf if they wanted one
                    saveConfig = true;

                setup.serverDBDir = "./";
                setup.playerDBDir = "./";
            }

            if (setup.mimeTypes.Count == 0)
            {
                setup.mimeTypes.Add(new MimeTypePair("html", "text/html"));
                setup.mimeTypes.Add(new MimeTypePair("htm", "text/html"));
                setup.mimeTypes.Add(new MimeTypePair("txt", "text/plain"));
                setup.mimeTypes.Add(new MimeTypePair("css", "text/css"));
                setup.mimeTypes.Add(new MimeTypePair("png", "image/png"));
                setup.mimeTypes.Add(new MimeTypePair("ico", "image/vnd.microsoft.icon"));
                setup.mimeTypes.Add(new MimeTypePair("*", "application/octet-stream"));
            }

            if (saveConfig)
            {
                XmlSerializer xml = new XmlSerializer(typeof(Setup));

                FileStream fs = conf.OpenWrite();
                StreamWriter file = new StreamWriter(fs);

                xml.Serialize(file, setup);
                file.Close();
                fs.Close();
            }

            serverDB = new ServerDB(setup);
            playerDB = new PlayerDB(setup);

            testXMLDump();
        }

        private void testXMLDump()
        {
            List<GamePlayer> test = new List<GamePlayer>();
            GamePlayer item = new GamePlayer();
            item.bzID = "1";
            item.callsign = "item1";
            test.Add(item);
            item = new GamePlayer();
            item.bzID = "2";
            item.callsign = "item2";
            test.Add(item);


            FileInfo outFile = new FileInfo("testfile.xml");

            XmlSerializer xml = new XmlSerializer(typeof(List<GamePlayer>));

            FileStream fs = outFile.OpenWrite();
            StreamWriter file = new StreamWriter(fs);

            xml.Serialize(file, test);
            file.Close();
            fs.Close();
        }

        private bool pushError(HTTPConnection connection, string error)
        {
            connection.writeToContext("\nError: " + error);
            return false;
        }

        private bool pushOK(HTTPConnection connection)
        {
            connection.writeToContext("\nOK");
            return false;
        }

        private bool dataError(HTTPConnection connection, string error)
        {
            connection.writeToContext("\nQuery Error: " + error);
            return false;
        }

        private bool dataOK(HTTPConnection connection)
        {
            connection.writeToContext("\nQuery OK");
            return false;
        }

        private bool handleDataPush(HTTPConnection connection)
        {
            // it's a server that is updating data
            string command = connection.getArg("action");

            // commands
            if (command == "add")
            {
                //string host = connection.getArg("host");
                string port = connection.getArg("port");
                if (port == string.Empty)
                    port = "5154";

                ServerHost host = new ServerHost();
                host.hostname = connection.getArg("host");
                host.port = 5154;
                if (connection.hasArg(port))
                     int.TryParse(connection.getArg(port),out host.port);

                GameServer server = null;
                List<GameServer> servers = serverDB.findServersByHost(host);
                if (servers.Count != 0)
                    server = servers[0];
                else
                    server = serverDB.addServer(host);

                if (server == null)
                    return pushError(connection, "invalid server");

                server.description = connection.getArg("desc");
                string playerBlob = connection.getArg("players");
                if (playerBlob != null && playerBlob != string.Empty) 
                {
                    // parse players
                    XmlSerializer xml = new XmlSerializer(typeof(List<GamePlayer>));
                    StreamReader reader = new StreamReader(playerBlob);
                    server.players = (List<GamePlayer>)xml.Deserialize(reader);;
                    reader.Close();
                }
                serverDB.updateServer(server);

                return pushOK(connection);
            }
            //update

            return pushError(connection,"unknown command");
        }

        private bool handleDataRequest(HTTPConnection connection)
        {
            string command = connection.getArg("action");

            // commands
            if (command == "list")
            {
                List<ServerHost> hosts = serverDB.listServers();
                if( hosts.Count == 0)
                    connection.writeToContext("No Results\n");
                else
                {
                    // parse players
                    XmlSerializer xml = new XmlSerializer(typeof(List<ServerHost>));

                    MemoryStream memStream = new MemoryStream();

                    StreamWriter writer = new StreamWriter(memStream);
                    xml.Serialize(writer, hosts);
                    writer.Close();

                    memStream.Seek(0, SeekOrigin.Begin);
                    StreamReader reader = new StreamReader(memStream);
                    string temp = reader.ReadToEnd();
                 //   string temp = new string(System.Text.ASCIIEncoding.ASCII.GetChars(memStream.GetBuffer(),0,(int)memStream.Length));
                    reader.Close();
                    memStream.Close();

                    connection.writeToContext(temp);
                }
                return dataOK(connection);
            }

            return dataError(connection,"unknown request");
        }

        public bool handleURL(HttpListenerContext context)
        {
            HTTPConnection connection = new HTTPConnection(context);

            if (connection.hasArg("isgameserver"))
                return handleDataPush(connection);

            return handleDataRequest(connection);
        }
    }

}
