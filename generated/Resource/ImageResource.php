<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class ImageResource extends Resource
{
    /**
     * List Images.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findAll($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('all', false);
        $queryParam->setDefault('filters', null);
        $queryParam->setDefault('filter', null);
        $queryParam->setDefault('digests', null);
        $url      = sprintf('/v1.21/images/json?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageItem[]', 'json');
        }

        return $response;
    }

    /**
     * Build an image from Dockerfile via stdin.
     * 
     * @param mixed  $inputStream The input stream must be a tar archive compressed with one of the following algorithms: identity (no compression), gzip, bzip2, xz.
     * @param array  $parameters  List of parameters
     * @param string $fetch       Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function build($inputStream, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('dockerfile', null);
        $queryParam->setDefault('t', null);
        $queryParam->setDefault('remote', null);
        $queryParam->setDefault('q', false);
        $queryParam->setDefault('nocache', false);
        $queryParam->setDefault('pull', null);
        $queryParam->setDefault('rm', true);
        $queryParam->setDefault('forcerm', false);
        $queryParam->setDefault('Content-type', 'application/tar');
        $queryParam->setDefault('X-Registry-Config', null);
        $url      = sprintf('/v1.21/build?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $inputStream);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Create an image either by pulling it from the registry or by importing it.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('fromImage', null);
        $queryParam->setDefault('fromSrc', null);
        $queryParam->setDefault('repo', null);
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('X-Registry-Config', null);
        $url      = sprintf('/v1.21/images/create?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Return low-level information on the image name.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function find($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/images/{name}/json?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Image', 'json');
        }

        return $response;
    }

    /**
     * Return the history of the image name.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function history($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/images/{name}/history?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageHistoryItem[]', 'json');
        }

        return $response;
    }

    /**
     * Push the image name on the registry.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function push($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('X-Registry-Auth', null);
        $url      = sprintf('/v1.21/images/{name}/push?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Tag the image name into a repository.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tag($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('repo', null);
        $queryParam->setDefault('force', null);
        $queryParam->setDefault('tag', null);
        $url      = sprintf('/v1.21/images/{name}/tag?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Remove the image name from the filesystem.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function remove($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('force', null);
        $queryParam->setDefault('noprune', null);
        $url      = sprintf('/v1.21/images/{name}?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('DELETE', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Search for an image on Docker Hub.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function search($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('term', null);
        $url      = sprintf('/v1.21/images/search?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageSearchResult[]', 'json');
        }

        return $response;
    }

    /**
     * Create a new image from a container’s changes.
     * 
     * @param mixed  $containerConfig The container configuration
     * @param array  $parameters      List of parameters
     * @param string $fetch           Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function commit($containerConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('container', null);
        $queryParam->setDefault('repo', null);
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('comment', null);
        $queryParam->setDefault('author', null);
        $queryParam->setDefault('pause', null);
        $queryParam->setDefault('changes', null);
        $url      = sprintf('/v1.21/commit?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $containerConfig);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('201' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\CommitResult', 'json');
        }

        return $response;
    }

    /**
     * Get a tarball containing all images and metadata for the repository specified by name.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function save($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/images/{name}/get?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Get a tarball containing all images and metadata for one or more repositories.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function saveAll($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('names', null);
        $url      = sprintf('/v1.21/images/get?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Load a set of images and tags into a Docker repository. See the image tarball format for more details.
     * 
     * @param mixed  $imagesTarball Tar archive containing images
     * @param array  $parameters    List of parameters
     * @param string $fetch         Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function load($imagesTarball, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/images/load?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $imagesTarball);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }
}
