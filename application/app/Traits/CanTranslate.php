<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CanTranslate
{
    /**
     * Translate text from one language to another using the gateway API.
     *
     * @param  string  $from  Source language code (e.g., 'en')
     * @param  string  $target  Target language code (e.g., 'id')
     * @param  string  $text  The text to translate
     * @return string Translated text or original text on failure
     */
    public function translate(string $from, string $target, string $text): string
    {
        $apiUrl = config('app.translate_url') ?? env('APP_TRANSLATE_URL');

        if (empty($apiUrl)) {
            Log::warning('Translation API URL (APP_TRANSLATE_URL) is not configured.');

            return $text;
        }

        try {
            $response = Http::get($apiUrl, [
                'from' => $from,
                'target' => $target,
                'text' => $text,
            ]);

            if ($response->successful()) {
                $translatedText = $response->body();

                // If the response is empty, return original text
                return ! empty($translatedText) ? $translatedText : $text;
            }

            Log::error('Translation API request failed', [
                'status' => $response->status(),
                'message' => $response->reason(),
                'payload' => [
                    'from' => $from,
                    'target' => $target,
                    'text' => $text,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Translation API error: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        }

        return $text;
    }
}
