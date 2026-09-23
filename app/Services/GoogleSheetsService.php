<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleSheetsService
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const SHEETS_SCOPE = 'https://www.googleapis.com/auth/spreadsheets';
    private const HEADERS = [
        'Customer', 'Phone', 'Outlet', 'Items', 'Addons',
        'Appr Code', 'Coupon', 'Promo', 'Total', 'Date',
    ];

    public function appendOrder(Order $order): void
    {
        $order->loadMissing([
            'customer:id,name,telp',
            'promo:id,nama',
            'items.product:id,name',
            'items.addons.addon:id,name,price',
        ]);

        $items = $order->items
            ->map(fn ($item) => optional($item->product)->name)
            ->filter()->implode(', ');

        $addons = $order->items->flatMap(fn ($item) => $item->addons->map(
            fn ($addon) => optional($addon->addon)->name . ' x' . $addon->qty
        ))->filter()->implode(', ');

        $this->appendRow([
            optional($order->customer)->name ?? '—',
            optional($order->customer)->telp ?? '—',
            $order->outlet,
            $items,
            $addons,
            $order->apprcode,
            $order->items->map(fn ($item) => $item->code)->implode(', '),
            optional($order->promo)->nama ?? '—',
            $order->total,
            $order->created_at->format('Y-m-d H:i:s'),
        ], $this->dailySheetName($order->created_at->format('Y-m-d')));
    }

    public function appendRow(array $row, ?string $sheetName = null): void
    {
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $range = config('services.google_sheets.range', 'Transactions!A:J');
        [$configuredSheet] = explode('!', $range, 2);
        $sheetName ??= $configuredSheet;
        $this->ensureSheet($spreadsheetId, $sheetName);

        $existingRows = $this->request('get', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$this->encodeRange($sheetName . '!A:J')}", [
            'query' => ['majorDimension' => 'ROWS'],
        ])->json('values', []);

        if (empty($existingRows)) {
            $headerRange = $sheetName . '!A1:J1';

            $this->request('put', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$this->encodeRange($headerRange)}", [
                'query' => ['valueInputOption' => 'USER_ENTERED'],
                'json' => [
                    'range' => $headerRange,
                    'majorDimension' => 'ROWS',
                    'values' => [self::HEADERS],
                ],
            ]);

            $existingRows[] = self::HEADERS;
        }

        $rowNumber = count($existingRows) + 1;
        $targetRange = $sheetName . '!A' . $rowNumber . ':J' . $rowNumber;

        $this->request('put', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$this->encodeRange($targetRange)}", [
            'query' => ['valueInputOption' => 'USER_ENTERED'],
            'json' => ['range' => $targetRange, 'majorDimension' => 'ROWS', 'values' => [$row]],
        ]);
    }

    private function dailySheetName(string $date): string
    {
        return $date;
    }

    private function ensureSheet(string $spreadsheetId, string $sheetName): void
    {
        $spreadsheet = $this->request('get', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}", [
            'query' => ['fields' => 'sheets.properties.title'],
        ]);
        $titles = collect($spreadsheet->json('sheets', []))
            ->map(fn ($sheet) => $sheet['properties']['title'] ?? null)
            ->filter();

        if ($titles->contains($sheetName)) {
            return;
        }

        $this->request('post', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}:batchUpdate", [
            'json' => [
                'requests' => [[
                    'addSheet' => [
                        'properties' => ['title' => $sheetName],
                    ],
                ]],
            ],
        ]);
    }

    public function replaceRows(array $rows): void
    {
        $spreadsheetId = config('services.google_sheets.spreadsheet_id');
        $range = config('services.google_sheets.range', 'Transactions!A:J');
        $encodedRange = $this->encodeRange($range);

        $this->request('post', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$encodedRange}:clear", [
            'json' => new \stdClass(),
        ]);

        $this->request('put', "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$encodedRange}", [
            'query' => ['valueInputOption' => 'USER_ENTERED'],
            'json' => ['range' => $range, 'majorDimension' => 'ROWS', 'values' => $rows],
        ]);
    }

    private function request(string $method, string $url, array $options)
    {
        $request = Http::withToken($this->accessToken())
            ->acceptJson()
            ->withOptions([
                'verify' => $this->caBundle(),
            ]);

        if (isset($options['query'])) {
            $request = $request->withQueryParameters($options['query']);
        }

        $response = $request->{$method}($url, $options['json'] ?? []);

        if ($response->failed()) {
            throw new RuntimeException('Google Sheets request failed: ' . $response->body());
        }

        return $response;
    }

    private function accessToken(): string
    {
        $credentialsPath = config('services.google_sheets.credentials');

        if ($credentialsPath && ! str_starts_with($credentialsPath, DIRECTORY_SEPARATOR)) {
            $credentialsPath = base_path($credentialsPath);
        }

        if (! $credentialsPath || ! is_file($credentialsPath)) {
            throw new RuntimeException('Google Sheets credentials file is not configured.');
        }

        $credentials = json_decode(file_get_contents($credentialsPath), true, 512, JSON_THROW_ON_ERROR);
        $now = time();
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => self::SHEETS_SCOPE,
            'aud' => self::TOKEN_URL,
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_THROW_ON_ERROR));
        $unsignedToken = $header . '.' . $claims;

        if (! openssl_sign($unsignedToken, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Unable to sign Google Sheets authentication token.');
        }

        $response = Http::asForm()
            ->withOptions(['verify' => $this->caBundle()])
            ->post(self::TOKEN_URL, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $unsignedToken . '.' . $this->base64UrlEncode($signature),
        ])->throw();

        return $response->json('access_token');
    }

    private function caBundle(): string|bool
    {
        $caBundle = config('services.google_sheets.ca_bundle', true);

        if (is_string($caBundle) && ! str_starts_with($caBundle, DIRECTORY_SEPARATOR)) {
            return base_path($caBundle);
        }

        return $caBundle;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function encodeRange(string $range): string
    {
        return str_replace('%2F', '/', rawurlencode($range));
    }
}
