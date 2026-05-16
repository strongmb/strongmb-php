<?php

declare(strict_types=1);

namespace Strongmb;

use Strongmb\Exceptions\ApiException;
use Strongmb\Exceptions\AuthException;
use Strongmb\Exceptions\StrongmbException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client
{
    private const LIVE_URL = 'https://api.strongmb.ng/v1/';
    private const SANDBOX_URL = 'https://sandbox.api.strongmb.ng/v1/';

    private HttpClientInterface $http;

    public function __construct(string $apiKey, bool $sandbox = false)
    {
        $this->http = HttpClient::create([
            'base_uri' => $sandbox ? self::SANDBOX_URL : self::LIVE_URL,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint): Response
    {
        return $this->request('GET', $endpoint);
    }

    public function post(string $endpoint, array $body = []): Response
    {
        return $this->request('POST', $endpoint, $body);
    }

    private function request(string $method, string $endpoint, ?array $body = null): Response
    {
        try {
            $options = [];
            if ($body !== null) {
                $options['json'] = $body;
            }

            $httpResponse = $this->http->request($method, $endpoint, $options);

            $status = $httpResponse->getStatusCode();
            $data = $httpResponse->toArray(false);

            if ($status === 401) {
                throw new AuthException($data['message'] ?? 'Unauthorized.', $data);
            }

            if ($status >= 400) {
                throw new ApiException(
                    $data['message'] ?? 'API error.',
                    $data['metadata']['code'] ?? 'ERR_UNKNOWN',
                    $status,
                    $data,
                );
            }

            return new Response($data, $status);
        } catch (ApiException $e) {
            throw $e;
        } catch (TransportExceptionInterface $e) {
            throw new StrongmbException('Network error: ' . $e->getMessage(), 0, $e);
        } catch (\JsonException $e) {
            throw new StrongmbException('Invalid JSON response: ' . $e->getMessage(), 0, $e);
        }
    }
}
