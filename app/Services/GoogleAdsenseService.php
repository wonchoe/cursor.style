<?php

namespace App\Services;

use GuzzleHttp\Client;

class GoogleAdsenseService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://adsense.googleapis.com/v2/',
            'headers' => ['Accept' => 'application/json']
        ]);
    }

    public function getAccessToken(): ?string
    {
        try {
        $refreshToken = config('services.adsense.refresh_token');
        if (empty($refreshToken)) {
            logger()->warning('No AdSense refresh token set in config.');
            return null;
        }

        //logger()->info('Refresh token: '. $refreshToken);

            $response = $this->client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => config('services.adsense.client_id'),
                    'client_secret' => config('services.adsense.client_secret'),
                    'refresh_token' => config('services.adsense.refresh_token'),
                    'grant_type' => 'refresh_token',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? null;

        } catch (\Throwable $e) {
            logger()->error('Failed to get AdSense access token', [
                'message' => $e->getMessage(),
                'client_id' => config('services.adsense.client_id') ? 'set' : 'unset',
                'refresh_token' => config('services.adsense.refresh_token') ? 'set' : 'unset',
            ]);
            return null;
        }
    }

    public function fetchEarnings(string $dateRange = 'LAST_7_DAYS'): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            logger()->error('No access token available for AdSense API.');
            return null;
        }

        try {
            // 1. Retrieve AdSense account
            $accountList = $this->client->get('accounts', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
            ]);

            $accounts = json_decode($accountList->getBody()->getContents(), true);
            $accountFullPath = $accounts['accounts'][0]['name'] ?? null;
            if (!$accountFullPath) {
                logger()->error('No AdSense account found.', ['response' => $accounts]);
                return null;
            }

            // logger()->info('Retrieved AdSense account', [
            //     'accountFullPath' => $accountFullPath,
            // ]);

            // 2. Retrieve AFC ad client
            $adClientsResp = $this->client->get("$accountFullPath/adclients", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
            ]);

            $adClients = json_decode($adClientsResp->getBody()->getContents(), true);
            $adClientResource = null;
            $adClientId = null;

            foreach ($adClients['adClients'] as $client) {
                if ($client['productCode'] === 'AFC') {
                    $adClientResource = $client['name'];
                    $adClientId = $client['reportingDimensionId'];
                    break;
                }
            }

            if (!$adClientResource || !$adClientId) {
                logger()->error('No AdSense for Content (AFC) ad client found.', [
                    'account' => $accountFullPath,
                    'adClients' => $adClients,
                ]);
                return null;
            }

            // logger()->info('Retrieved AdSense ad client', [
            //     'adClientResource' => $adClientResource,
            //     'adClientId' => $adClientId,
            // ]);

            // 3. Generate report
            $reportUrl = "$accountFullPath/reports:generate";
            $fullUrl = (string) $this->client->getConfig('base_uri') . $reportUrl;

            // Define query parameters
            $queryParams = [
                'dateRange' => $dateRange,
                'metrics' => [
                    'ESTIMATED_EARNINGS',
                    'CLICKS',
                    'IMPRESSIONS',
                    'PAGE_VIEWS',
                    'IMPRESSIONS_RPM',
                    'COST_PER_CLICK',
                ],
                'dimensions' => 'DATE',
                'filters' => "AD_CLIENT_ID==$adClientId",
            ];

            // Build query string manually to match curl
            $queryArray = [
                'dateRange' => $queryParams['dateRange'],
                'dimensions' => $queryParams['dimensions'],
                'filters' => $queryParams['filters'],
            ];
            $metricsQuery = implode('&', array_map(function ($metric) {
                return 'metrics=' . urlencode($metric);
            }, $queryParams['metrics']));
            $queryString = http_build_query($queryArray) . '&' . $metricsQuery;

            $fullUrlWithQuery = $fullUrl . '?' . $queryString;

            // logger()->info('Calling AdSense reports:generate', [
            //     'url' => $fullUrlWithQuery,
            //     'account' => $accountFullPath,
            //     'ad_client' => $adClientId,
            //     'dateRange' => $dateRange,
            //     'query_string' => $queryString,
            // ]);

            $response = $this->client->get($fullUrlWithQuery, [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            logger()->error('AdSense report generation failed', [
                'status' => $e->getResponse()?->getStatusCode(),
                'body' => $e->getResponse()?->getBody()->getContents(),
                'account' => $accountFullPath ?? 'N/A',
                'ad_client' => $adClientId ?? 'N/A',
                'url' => $fullUrlWithQuery ?? $fullUrl ?? 'N/A',
                'dateRange' => $dateRange,
                'query_string' => $queryString ?? null,
            ]);
            return null;
        } catch (\Throwable $e) {
            logger()->error('Unexpected error in AdSense report generation', [
                'message' => $e->getMessage(),
                'account' => $accountFullPath ?? 'N/A',
                'ad_client' => $adClientId ?? 'N/A',
                'url' => $fullUrlWithQuery ?? $fullUrl ?? 'N/A',
                'dateRange' => $dateRange,
                'query_string' => $queryString ?? null,
            ]);
            return null;
        }
    }
}