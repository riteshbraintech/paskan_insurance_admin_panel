<?php

namespace App\Service;

use App\Models\ViriyahToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ViriyahAuthService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl      = config('viriyah.base_url');
        $this->clientId     = config('viriyah.client_id');
        $this->clientSecret = config('viriyah.client_secret');
        $this->username     = config('viriyah.username');
        $this->password     = config('viriyah.password');
    }

    /**
     * Return valid token (auto refresh if expired)
     */
    public function getValidToken(): string|null
    {
        $token = ViriyahToken::first();

        if (!$token || $this->isExpired($token)) {
            if ($token && $token->refresh_token) {
                return $this->refreshToken();
            }
            return $this->generateToken();
        }

        return $token->access_token;
    }

    private function isExpired($token): bool
    {
        return !$token->expires_at || now()->greaterThanOrEqualTo($token->expires_at);
    }

    /**
     * Step 1: Generate new token
     */
    public function generateToken(): string|null
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "sourceTransID" => "t101",
            "clientId" => $this->clientId,
            "clientSecret" => $this->clientSecret,
            "requestTime" => now()->format('Y-m-d\TH:i:s'),
            "languagePreference" => app()->getLocale(),
            "grantType" => "password",
            "username" => $this->username,
            "password" => $this->password,
            "scope" => "profile",
        ])->post($this->baseUrl . "/api/authen/token/v1/generate");

        $data = $response->json();

        return $this->storeToken($data);
    }

    /**
     * Step 2: Refresh Token
     */
    public function refreshToken(): string|null
    {
        $token = ViriyahToken::first();

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "sourceTransID" => "t102",
            "clientId" => $this->clientId,
            "clientSecret" => $this->clientSecret,
            "grantType" => "refresh_token",
            "refreshToken" => $token->refresh_token,
        ])->post($this->baseUrl . "/api/authen/token/v1/refresh");

        $data = $response->json();

        // If refresh failed → regenerate
        if (!$response->successful() || isset($data['error'])) {
            return $this->generateToken();
        }

        return $this->storeToken($data);
    }

    /**
     * Store access token + refresh token
     */
    private function storeToken($data): string|null
    {
        $token = ViriyahToken::firstOrNew();

        $token->access_token  = $data['access_token'] ?? null;
        $token->refresh_token = $data['refresh_token'] ?? null;
        $token->expires_at    = now()->addSeconds($data['expires_in'] ?? 3600);

        $token->save();

        return $token->access_token;
    }


    /**
     * ⭐ NEW: MOTOR QUOTATION API (VMI V3)
    */
    public function getMotorQuotation(array $payload)
    {
        $token = $this->getValidToken();

        $response = Http::withHeaders([
            'Authorization'       => "Bearer ".$token,
            'Content-Type'        => 'application/json',
            'sourceTransID'       => '3dddb121-9140-4016-85d9-d9b22fabfae0',
            'clientId'            => $this->clientId,
            'clientSecret'        => $this->clientSecret,
            'requestTime'         => now()->format('Y-m-d\TH:i:s'),
            'languagePreference'  => 'TH',
        ])->post($this->baseUrl . '/api/policy/motor/vmi/v3/quotation', $payload);

        if ($response->failed()) {
            throw new \Exception("Viriyah Quotation Error: " . $response->body());
        }

        return $response->json();
    }
}
