<?php

namespace App\Actions;

use App\Settings\WhatsappSettings;
use Illuminate\Support\Facades\Log;

class Watzap
{
    protected $baseUrl;

    protected $apiKey;

    protected $numberKey;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->baseUrl = app(WhatsappSettings::class)->base_url;
        $this->apiKey = app(WhatsappSettings::class)->api_key;
        $this->numberKey = app(WhatsappSettings::class)->number_key;
    }

    protected function isApiKeySet(): bool
    {
        if (is_null($this->apiKey) || is_null($this->numberKey)) {
            Log::error('API Key or Number Key is not set');

            return false;
        }

        return true;
    }

    public function checkStatus(?string $apiKey = null, ?string $numberKey = null, bool $number = false)
    {
        $body = [
            'api_key' => $apiKey ?? $this->apiKey,
            'number_key' => $numberKey ?? $this->numberKey,
        ];

        return $this->sendRequest($body, $number ? 'checking_key' : 'validate_number');
    }

    public function groupContactGrabber()
    {
        if (! $this->isApiKeySet()) {
            return null;
        }

        $body = $this->createRequestBody();

        return $this->sendRequest($body, 'groups');
    }

    public function sendMessage(string $phoneNo, string $message, array $options = [])
    {
        if (! $this->isApiKeySet()) {
            return null;
        }

        $body = $this->createRequestBody([
            'phone_no' => $phoneNo,
            'message' => $message,
        ]);

        if (isset($options['group_id'])) {
            $endpoint = 'send_message_group';
            $body['group_id'] = $options['group_id'];
        } else {
            $endpoint = 'send_message';
        }

        return $this->sendRequest($body, $endpoint);
    }

    public function sendImageOrFile(string $phoneNo, string $url, bool $image = true, array $options = [])
    {
        if (! $this->isApiKeySet()) {
            return null;
        }

        $body = $this->createRequestBody([
            'phone_no' => $phoneNo,
            'url' => $url,
        ]);

        if ($image) {
            $body['caption'] = $options['caption'] ?? null;
            $body['separate_caption'] = $options['separate_caption'] ?? false;
            $endpoint = 'send_image_url';

            if (isset($options['group_id'])) {
                $endpoint = 'send_image_group';
                $body['group_id'] = $options['group_id'];
            }
        } else {
            $endpoint = 'send_file_url';

            Log::info('File URL: ' . $body['url']);

            if (isset($options['group_id'])) {
                $endpoint = 'send_file_group';
                $body['group_id'] = $options['group_id'];
            }
        }

        return $this->sendRequest($body, $endpoint);
    }

    public function webHook(string $url, string $type = 'SET')
    {
        if (! $this->isApiKeySet()) {
            return null;
        }

        $body = $this->createRequestBody();

        $endpoint = $type === 'SET' ? 'set_webhook' : 'unset_webhook';
        if ($type === 'SET') {
            $body['endpoint_url'] = $url;
        }

        return $this->sendRequest($body, $endpoint);
    }

    protected function createRequestBody(array $additionalData = []): array
    {
        return array_merge([
            'api_key' => $this->apiKey,
            'number_key' => $this->numberKey,
        ], $additionalData);
    }

    protected function sendRequest(array $body, string $endpoint)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}
