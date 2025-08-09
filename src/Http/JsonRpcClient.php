<?php

namespace Rconfig\Zabbix\Http;

use Rconfig\Zabbix\Contracts\ZabbixClient;

class JsonRpcClient implements ZabbixClient
{
    protected ?string $authToken = null;

    protected int $id = 1;

    public function __construct(
        protected string $baseUrl,
        protected string $endpoint,
        protected ?string $username = null,
        protected ?string $password = null,
        protected bool $hasBearerToken = false,
        protected int $timeout = 15,
        protected int $retries = 2,
        protected int $retrySleepMs = 250,
        protected ?string $bearer = null,
    ) {}

    public function call(string $method, array $params): mixed
    {
        $body = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $this->id++,
        ];

        // never include legacy auth for apiinfo.version
        if ($this->shouldUseLegacyAuth($method)) {
            $this->ensureSessionToken($method);
            $body['auth'] = $this->authToken;
        }

        try {
            $res = $this->request($method)->post($this->baseUrl.$this->endpoint, $body)->throw();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new \Rconfig\Zabbix\Exceptions\ZabbixHttpException($e->getMessage(), previous: $e);
        }

        $data = $res->json();

        if (isset($data['error'])) {
            throw new \Rconfig\Zabbix\Exceptions\ZabbixException(
                sprintf('[%s] %s', $data['error']['code'] ?? 'ERR', $data['error']['data'] ?? $data['error']['message'] ?? 'Unknown')
            );
        }

        return $data['result'] ?? $data;
    }

    protected function request(string $method): \Illuminate\Http\Client\PendingRequest
    {
        $req = \Illuminate\Support\Facades\Http::timeout($this->timeout)
            ->retry($this->retries, $this->retrySleepMs);

        // Do NOT send Authorization header for apiinfo.version
        $skipBearer = ($method === 'apiinfo.version');

        if (! $skipBearer && $this->hasBearerToken && $this->bearer) {
            $req = $req->withToken($this->bearer);
        }

        return $req;
    }

    protected function shouldUseLegacyAuth(string $method): bool
    {
        // Never send 'auth' for apiinfo.version
        return ! $this->hasBearerToken && $method !== 'user.login' && $method !== 'apiinfo.version';
    }

    protected function ensureSessionToken(string $method): void
    {
        if ($this->authToken || ! $this->username || ! $this->password) {
            return;
        }

        $login = [
            'jsonrpc' => '2.0',
            'method' => 'user.login',
            'params' => ['user' => $this->username, 'password' => $this->password],
            'id' => $this->id++,
        ];

        // user.login call should NOT carry Authorization either
        $res = $this->request($method)->post($this->baseUrl.$this->endpoint, $login)->throw()->json();
        if (! isset($res['result'])) {
            throw new \Rconfig\Zabbix\Exceptions\ZabbixException('Unable to authenticate with legacy credentials.');
        }
        $this->authToken = $res['result'];
    }
}
