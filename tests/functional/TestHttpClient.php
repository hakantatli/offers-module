<?php

namespace app\tests\functional;

/**
 * TestHttpClient wraps cURL to execute HTTP requests against the local Apache server.
 * Handles cookie jar (sessions), headers, status codes, and CSRF token extraction.
 */
class TestHttpClient
{
    private string $baseUrl;
    private string $cookieFile;
    private ?string $csrfToken = null;

    public function __construct(string $baseUrl = 'http://localhost')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'test_curl_');
    }

    public function __destruct()
    {
        if (file_exists($this->cookieFile)) {
            @unlink($this->cookieFile);
        }
    }

    public function getCsrfToken(): ?string
    {
        return $this->csrfToken;
    }

    public function setCsrfToken(?string $token): void
    {
        $this->csrfToken = $token;
    }

    /**
     * Executes GET request.
     *
     * @param array<string|int, string> $headers
     * @return array{statusCode: int, body: string, headers: array<string, string>, redirectUrl: string|null|false}
     */
    public function get(string $path, array $headers = [], bool $followRedirects = false): array
    {
        return $this->request('GET', $path, [], $headers, $followRedirects);
    }

    /**
     * Executes POST request.
     *
     * @param array<string, mixed> $data
     * @param array<string|int, string> $headers
     * @return array{statusCode: int, body: string, headers: array<string, string>, redirectUrl: string|null|false}
     */
    public function post(string $path, array $data = [], array $headers = [], bool $followRedirects = false): array
    {
        // Auto-inject CSRF token if present and not explicitly provided
        if ($this->csrfToken && !isset($data['_csrf'])) {
            $data['_csrf'] = $this->csrfToken;
        }

        return $this->request('POST', $path, $data, $headers, $followRedirects);
    }

    /**
     * Internal cURL executor.
     *
     * @param array<string, mixed> $data
     * @param array<string|int, string> $headers
     * @return array{statusCode: int, body: string, headers: array<string, string>, redirectUrl: string|null|false}
     */
    private function request(
        string $method,
        string $path,
        array $data = [],
        array $headers = [],
        bool $followRedirects = false
    ): array {
        $url = str_starts_with($path, 'http') ? $path : $this->baseUrl . '/' . ltrim($path, '/');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $rawResponse = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

        curl_close($ch);

        $headerText = substr($rawResponse, 0, $headerSize);
        $body = substr($rawResponse, $headerSize);

        // Extract CSRF token if present in HTML meta tag
        if (preg_match('/<meta name="csrf-token" content="([^"]+)"/i', $body, $matches)) {
            $this->csrfToken = $matches[1];
        }

        return [
            'statusCode' => $statusCode,
            'body' => $body,
            'headers' => $this->parseHeaders($headerText),
            'redirectUrl' => $redirectUrl,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function parseHeaders(string $headerText): array
    {
        $headers = [];
        foreach (explode("\r\n", $headerText) as $line) {
            if (strpos($line, ':') !== false) {
                [$key, $val] = explode(':', $line, 2);
                $headers[strtolower(trim($key))] = trim($val);
            }
        }
        return $headers;
    }
}
