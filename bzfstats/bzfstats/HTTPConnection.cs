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

namespace  HTTPConnections
{
    public class HTTPConnection
    {
        public HTTPConnection(HttpListenerContext c)
        {
            context = c;

            HttpListenerRequest request = context.Request;

            arguments.Clear();

            // get our base keys
            if (request.QueryString.HasKeys())
            {
                for (int i = 0; i < request.QueryString.Count; i++)
                {
                    string name = request.QueryString.GetKey(i);
                    string value = string.Empty;

                    if (request.QueryString.GetValues(i).GetLength(0) > 0)
                        value = request.QueryString.GetValues(i)[0];

                    arguments.Add(name, value);
                }
            }

            if (request.HttpMethod == "POST")
            {
                StreamReader reader = new System.IO.StreamReader(request.InputStream, request.ContentEncoding);
                string body = reader.ReadToEnd();

                while (body.Length < request.ContentLength64) // go untill we have the entire thing
                {
                    body += reader.ReadToEnd();
                    Thread.Sleep(100);
                    Console.WriteLine("readloop body read " + body.Length.ToString() + " : expected size " + request.ContentLength64.ToString() + "request flag " + request.HasEntityBody.ToString());
                }

                reader.Close();
                request.InputStream.Close();

                string[] pairs = body.Split('&');

                foreach (string s in pairs)
                {
                    string[] item = s.Split('=');

                    string name = item[0];
                    string value = string.Empty;

                    if (item.Length > 1 && item[1].Length > 0)
                        value = HttpUtility.UrlDecode(item[1]);

                    if (!arguments.ContainsKey(name))
                        arguments.Add(name, value);
                    else
                        arguments[name] = value;

                    Console.WriteLine("argument " + name + " : " + value);

                }
            }
        }

        public string getArg(string key)
        {
            if (arguments.ContainsKey(key))
                return arguments[key];
            return null;
        }

        public bool hasArgs()
        {
            return arguments.Count > 0;
        }

        public bool hasArg(string key)
        {
            return hasArgs() && arguments.ContainsKey(key);
        }

        public HttpListenerContext context;
        Dictionary<string, string> arguments = new Dictionary<string, string>();

        private void writeToContext ( string text, HttpListenerContext context )
        {
            context.Response.OutputStream.Write(System.Text.ASCIIEncoding.ASCII.GetBytes(text), 0, text.Length);
        }

        public void writeToContext ( string text )
        {
            writeToContext(text,context);
        }

        public void writeToContext(string text, string type)
        {
            setMimeType(type);
            writeToContext(text, context);
        }

        public bool writeToContext(FileInfo file, bool binary)
        {
            if (!file.Exists)
                return false;

            FileStream fs = file.OpenRead();
            int size = (int)fs.Length;

            if (size == 0)
                return false;

            if (binary)
            {
                byte[] data = new byte[size];
                fs.Read(data, 0, size);
                context.Response.OutputStream.Write(data, 0, size);
            }
            else
            {
                StreamReader sr = new StreamReader(fs);
                writeToContext(sr.ReadToEnd(),context);
                sr.Close();
            }

            fs.Close();
            return true;
        }

        public bool writeToContext(FileInfo file)
        {
            return writeToContext(file, true);
        }

        public bool writeToContext(FileInfo file, string type)
        {
            setMimeType(type);
            return writeToContext(file, true);
        }

        public bool writeToContext(FileInfo file,string type, bool binary)
        {
            setMimeType(type);
            return writeToContext(file, binary);
        }

        public void setMimeType ( string type )
        {
            context.Response.ContentType = "type";
        }
        
        public void set404 ( )
        {
            context.Response.StatusCode = (int)HttpStatusCode.NotFound;
        }
    }
}