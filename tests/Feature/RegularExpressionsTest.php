<?php

use Rconfig\Zabbix\Facades\Zabbix;

it('lists regular expressions', function () {
    $res = Zabbix::regularExpressions()->get(['limit' => 1]);
    expect($res)->toBeArray();
});
