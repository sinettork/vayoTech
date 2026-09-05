<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BrandfetchService
{
    public function search(string $name): array
    {
        $clientId = config('services.brandfetch.client_id');

        if (!$clientId) {
            throw new RuntimeException('BRANDFETCH_CLIENT_ID is not configured.');
        }

        $response = $this->client()
            ->get('/v2/search/' . rawurlencode($name), [
                'c' => $clientId,
            ])
            ->throw();

        return $response->json() ?? [];
    }

    public function logoUrl(?string $domain): ?string
    {
        $clientId = config('services.brandfetch.client_id');

        if (!$clientId || blank($domain)) {
            return null;
        }

        return 'https://cdn.brandfetch.io/domain/'
            . rawurlencode($this->normalizeDomain($domain))
            . '?c='
            . rawurlencode($clientId);
    }

    public function normalizeDomain(string $domain): string
    {
        $domain = trim($domain);
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = preg_replace('#^www\.#i', '', $domain);
        $domain = trim($domain, '/');

        return strtolower($domain);
    }

    private function client(): PendingRequest
    {
        return Http::acceptJson()
            ->baseUrl('https://api.brandfetch.io')
            ->timeout(8)
            ->retry(2, 200);
    }
}
