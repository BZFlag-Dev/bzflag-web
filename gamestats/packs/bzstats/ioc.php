<?php

\Qore\IoC::Register(
    'bzstats.db.stats',
    function ($c) {
        $db = $GLOBALS['cfg']['bzstats']['dbinstance'];
        return \Qore\Factory\DbFactory::build('Bzstats', $db, 'stats', $c["db.$db.connection"]);
    },
    true
);

\Qore\IoC::Register(
    'bzstats.statsmodel',
    function ($c) {
        return new \Packs\Bzstats\Models\StatsModel($c['bzstats.db.stats']);
    },
    true
);
