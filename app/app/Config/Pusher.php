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

    public function __construct()
    {
        parent::__construct();

        $this->appId   = env('pusher.appId', '');
        $this->key     = env('pusher.key', '');
        $this->secret  = env('pusher.secret', '');
        $this->cluster = env('pusher.cluster', 'ap1');
        $this->enabled = ! empty($this->appId) && ! empty($this->key) && ! empty($this->secret);
    }
}
