<?php

namespace App\Libraries;

use Config\Pusher as PusherConfig;
use Pusher\Pusher;

class PusherLib
{
    private ?Pusher $pusher = null;

    private PusherConfig $config;

    public function __construct()
    {
        $this->config = config('Pusher');

        if ($this->config->enabled) {
            $this->pusher = new Pusher(
                $this->config->key,
                $this->config->secret,
                $this->config->appId,
                [
                    'cluster' => $this->config->cluster,
                    'useTLS'  => true,
                ]
            );
        }
    }

    public function trigger(string $channel, string $event, array $data): bool
    {
        if ($this->pusher === null) {
            return false;
        }

        try {
            $this->pusher->trigger($channel, $event, $data);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function isEnabled(): bool
    {
        return $this->config->enabled;
    }

    public function getKey(): string
    {
        return $this->config->key;
    }

    public function getCluster(): string
    {
        return $this->config->cluster;
    }
}
