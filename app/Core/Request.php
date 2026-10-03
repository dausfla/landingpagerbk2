<?php

namespace App\Core;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        // Parse JSON input body if Content-Type is application/json
        if ($this->isJson()) {
            $rawInput = file_get_contents('php://input');
            $jsonData = json_decode($rawInput, true);
            if (is_array($jsonData)) {
                $this->post = array_merge($this->post, $jsonData);
            }
        }
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getUri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return '/' . trim($uri, '/');
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isJson(): bool
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        return str_contains(strtolower($contentType), 'application/json');
    }

    public function isAjax(): bool
    {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest' || $this->isJson();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function getIp(): string
    {
        $ip = $this->server['HTTP_CF_CONNECTING_IP']
           ?? $this->server['HTTP_X_FORWARDED_FOR']
           ?? $this->server['REMOTE_ADDR']
           ?? '127.0.0.1';

        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        return $ip;
    }

    /**
     * Anonymize IP address with SHA-256 hash + salt (UU 27/2022 PDP Compliance)
     */
    public function getIpHash(): string
    {
        $ip = $this->getIp();
        $salt = env('IP_SALT', 'rbk-ip-hash-salt-2026');
        return hash('sha256', $ip . '|' . $salt);
    }

    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function getReferrer(): string
    {
        return $this->server['HTTP_REFERER'] ?? '';
    }

    public function getDevice(): string
    {
        $ua = strtolower($this->getUserAgent());
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }

    /**
     * Extract UTM attribution and click identifiers from request
     */
    public function getAttributionData(): array
    {
        return [
            'utm_source'   => $this->input('utm_source'),
            'utm_medium'   => $this->input('utm_medium'),
            'utm_campaign' => $this->input('utm_campaign'),
            'utm_term'     => $this->input('utm_term'),
            'utm_content'  => $this->input('utm_content'),
            'gclid'        => $this->input('gclid'),
            'fbclid'       => $this->input('fbclid'),
            'ttclid'       => $this->input('ttclid'),
            'referrer'     => $this->getReferrer(),
            'landing_url'  => $this->server['REQUEST_URI'] ?? '/',
            'device'       => $this->getDevice(),
            'ip_hash'      => $this->getIpHash(),
            'user_agent'   => $this->getUserAgent(),
        ];
    }
}
