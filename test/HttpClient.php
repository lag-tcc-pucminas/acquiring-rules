<?php

namespace Test;

use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpMessage\Uri\Uri;
use Hyperf\Testing\Client;
use Psr\Http\Message\ServerRequestInterface;

class HttpClient extends Client
{
    public function initRequest(string $method, string $path, array $options = []): ServerRequestInterface
    {
        $query = $options['query'] ?? [];
        $params = $options['form_params'] ?? [];
        $json = $options['json'] ?? [];
        $headers = $options['headers'] ?? [];
        $multipart = $options['multipart'] ?? [];

        $parsePath = parse_url($path);
        $path = $parsePath['path'];
        $uriPathQuery = $parsePath['query'] ?? [];
        if (! empty($uriPathQuery)) {
            parse_str($uriPathQuery, $pathQuery);
            $query = array_merge($pathQuery, $query);
        }

        $data = $params;

        // Initialize PSR-7 Request and Response objects.
        $uri = (new Uri($this->baseUri . ltrim($path, '/')))->withQuery(http_build_query($query));

        $content = http_build_query($params);
        if (data_get($headers, 'Content-Type') == 'application/json') {
            $content = json_encode($json, JSON_UNESCAPED_UNICODE);
            $data = $json;
        }

        $body = new SwooleStream($content);

        $request = new Psr7Request($method, $uri, $headers, $body);

        return $request->withQueryParams($query)
            ->withParsedBody($data)
            ->withUploadedFiles($this->normalizeFiles($multipart));
    }
}