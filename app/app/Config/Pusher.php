<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pusher extends BaseConfig
{
    public string $appId = '';
    public string $key = '';
    public string $secret = '';
    public string $cluster = 'ap1';
    public bool $enabled = false;

}
