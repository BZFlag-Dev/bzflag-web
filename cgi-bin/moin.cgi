#!/usr/bin/python

"""
    MoinMoin - CGI Driver Script

    Copyright (c) 2000 by Jürgen Hermann <jh@web.de>
    All rights reserved, see COPYING for details.

    $Id$
"""

import sys
sys.path.append('/home/groups/b/bz/bzflag/moin/lib/python2.2/site-packages')

import os
if os.environ.get('QUERY_STRING') == 'test':
    print "Content-Type: text/plain\n\nMoinMoin CGI Diagnosis\n======================\n"

    try:
        from MoinMoin import cgimain
        print 'Package "MoinMoin" sucessfully imported.\n'
        cgimain.test()
    except:
        import sys, traceback, string, pprint
        type, value, tb = sys.exc_info()
        if type == ImportError:
            print 'Your PYTHONPATH is:\n%s' % pprint.pformat(sys.path)
        print "\nTraceback (innermost last):\n%s" % string.join(
            traceback.format_tb(tb) + traceback.format_exception_only(type, value))
else:
    from MoinMoin import cgimain
    cgimain.run()

