<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists templates', function () {
    $tpl = Zabbix::templates()->get(['output' => ['templateid', 'host']]);
    expect($tpl)->toBeArray();
});
