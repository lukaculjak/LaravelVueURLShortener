<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UrlDict;
use GuzzleHttp\Client;

class UrlDictController extends Controller
{
    public function create(Request $request)
    {
    $originalUrl = $request->input('originalUrl');

    if (!$this->isUrlSafe($originalUrl)) {
        return response()->json(['error' => 'Unsafe URL'], 400);
    }

    $existingUrl = UrlDict::where('url', $originalUrl)->first();

    if ($existingUrl) {
        return response()->json([
            'originalUrl' => 'URL already exists',
            'shortenedUrl' => url($existingUrl->shortened_url)
        ]);
    }

    $hash = hash('sha256', $originalUrl);
    $shortenedHash = substr($hash, 0, 6);

    $shortenedUrl = "http://127.0.0.1:8000/api/urls/{$shortenedHash}";

    $newUrl = UrlDict::create([
        'url' => $originalUrl,
        'shortened_url' => $shortenedUrl
    ]);

    return response()->json([
        'originalUrl' => $originalUrl,
        'shortenedUrl' => $shortenedUrl
    ]);
    }

    private function isUrlSafe($url)
    {
        $client = new Client();
        $response = $client->post('https://safebrowsing.googleapis.com/v4/threatMatches:find?key=AIzaSyALg419LvOcrX7x9zQnDJDCDcQorTqNlJY', [
            'json' => [
                'client' => [
                    'clientId' => 'urlShortener',
                    'clientVersion' => '1.0.0',
                ],
                'threatInfo' => [
                    'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING', 'THREAT_TYPE_UNSPECIFIED', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'],
                    'platformTypes' => ['ANY_PLATFORM'],
                    'threatEntryTypes' => ['URL'],
                    'threatEntries' => [
                        ['url' => $url],
                    ],
                ],
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        return empty($body['matches']);
    }


    public function redirectShortenedUrl($shortenedUrl)
    {
    $urlDict = UrlDict::where('shortened_url', 'like', '%' . $shortenedUrl)->first();

    if ($urlDict != null) {
        return redirect($urlDict->url);
    }

    abort(404);
    }
}
