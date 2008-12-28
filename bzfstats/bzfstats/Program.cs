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

namespace bzfstats
{
    class Program
    {
        static bool runServer = true;
        static Server server;

        static void Main(string[] args)
        {
            HttpListener  httpd = new HttpListener();

            // read in some kinda prefs and setup the server

            server = new Server(args);

            foreach (string h in server.setup.hosts)
                httpd.Prefixes.Add(h);

            httpd.Start();

            bool async = true;

            if (!async)
            {
                while (!server.handleURL(httpd.GetContext()))
                {
                    httpd.GetContext().Response.OutputStream.Close();
                }
            }
            else
            {
                AsyncCallback callback = new AsyncCallback(HTTPCallback);

                IAsyncResult result = httpd.BeginGetContext(callback, httpd);

                while (runServer)
                {
                    Thread.Sleep(100);

                    if (result.IsCompleted)
                        result = httpd.BeginGetContext(callback, httpd);                      
                }
            }
            httpd.Close();
        }

        public static void HTTPCallback (IAsyncResult result )
        {
            HttpListener listener = (HttpListener)result.AsyncState;

            HttpListenerContext context = listener.EndGetContext(result);

            if (server.handleURL(context))
                runServer = false;

            context.Response.OutputStream.Close();
        }
    }
}
