<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class ImageResource extends Resource
{
    /**
     * List Images.
     *
     * @param array $parameters List of parameters
     * 
     *     (bool)all: Show all images. Only images from a final layer (no children) are shown by default.
     *     (string)filters: A JSON encoded value of the filters (a map[string][]string) to process on the containers list
     *     (string)filter: Only return images with the specified name.
     *     (bool)digests: Show digest information, default to false
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\ImageItem[]
     */
    public function findAll($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('all', false);
        $queryParam->setDefault('filters', null);
        $queryParam->setDefault('filter', null);
        $queryParam->setDefault('digests', null);
        $url      = '/v1.21/images/json';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageItem[]', 'json');
            }
        }

        return $response;
    }

    /**
     * Build an image from Dockerfile via stdin.
     *
     * @param string $inputStream The input stream must be a tar archive compressed with one of the following algorithms: identity (no compression), gzip, bzip2, xz.
     * @param array  $parameters  List of parameters
     * 
     *     (string)dockerfile: Path within the build context to the Dockerfile. This is ignored if remote is specified and points to an individual filename.
     *     (string)t: A repository name (and optionally a tag) to apply to the resulting image in case of success.
     *     (string)remote: A Git repository URI or HTTP/HTTPS URI build source. If the URI specifies a filename, the file’s contents are placed into a file called Dockerfile.
     *     (bool)q: Suppress verbose build output.
     *     (bool)nocache: Do not use the cache when building the image.
     *     (string)pull: Attempt to pull the image even if an older image exists locally
     *     (bool)rm: Remove intermediate containers after a successful build (default behavior).
     *     (bool)forcerm: always remove intermediate containers (includes rm)Request Headers:
     *     (string)Content-type:  Set to 'application/tar'.
     *     (string)X-Registry-Config: A base64-url-safe-encoded Registry Auth Config JSON object
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function build(string $inputStream, $parameters = [], $fetch = self::FETCH_OBJECT)
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
        $url      = '/v1.21/build';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $inputStream;
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Create an image either by pulling it from the registry or by importing it.
     *
     * @param array $parameters List of parameters
     * 
     *     (string)fromImage: Name of the image to pull. The name may include a tag or digest. This parameter may only be used when pulling an image.
     *     (string)fromSrc: Source to import. The value may be a URL from which the image can be retrieved or - to read the image from the request body. This parameter may only be used when importing an image.
     *     (string)repo: Repository name given to an image when it is imported. The repo may include a tag. This parameter may only be used when importing an image.
     *     (string)tag: Tag or digest.
     *     (string)X-Registry-Config: A base64-encoded AuthConfig object
     * @param string $fetch Fetch mode (object or response)
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
        $url      = '/v1.21/images/create';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Return low-level information on the image name.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\Image
     */
    public function find($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/v1.21/images/{name}/json';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $queryParam->buildFormDataString($parameters);
        $request    = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Image', 'json');
            }
        }

        return $response;
    }

    /**
     * Return the history of the image name.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\ImageHistoryItem[]
     */
    public function history($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/v1.21/images/{name}/history';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $queryParam->buildFormDataString($parameters);
        $request    = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageHistoryItem[]', 'json');
            }
        }

        return $response;
    }

    /**
     * Push the image name on the registry.
     *
     * @param array $parameters List of parameters
     * 
     *     (string)tag: The tag to associate with the image on the registry.
     *     (string)X-Registry-Auth: A base64-encoded AuthConfig object
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function push($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('X-Registry-Auth', null);
        $url      = '/v1.21/images/{name}/push';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Tag the image name into a repository.
     *
     * @param array $parameters List of parameters
     * 
     *     (string)repo: The repository to tag in.
     *     (string)force: 1/True/true or 0/False/false, default false
     *     (string)tag: The new tag name.
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tag($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('repo', null);
        $queryParam->setDefault('force', null);
        $queryParam->setDefault('tag', null);
        $url      = '/v1.21/images/{name}/tag';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Remove the image name from the filesystem.
     *
     * @param array $parameters List of parameters
     * 
     *     (string)force: 1/True/true or 0/False/false, default false
     *     (string)noprune: 1/True/true or 0/False/false, default false.
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function remove($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('force', null);
        $queryParam->setDefault('noprune', null);
        $url      = '/v1.21/images/{name}';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('DELETE', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Search for an image on Docker Hub.
     *
     * @param array $parameters List of parameters
     * 
     *     (string)term: Term to search
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\ImageSearchResult[]
     */
    public function search($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('term', null);
        $url      = '/v1.21/images/search';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('200' == $response->getStatusCode()) {
                return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ImageSearchResult[]', 'json');
            }
        }

        return $response;
    }

    /**
     * Create a new image from a container’s changes.
     *
     * @param \Docker\API\Model\ContainerConfig $containerConfig The container configuration
     * @param array                             $parameters      List of parameters
     * 
     *     (string)container: Container id or name to commit
     *     (string)repo: Repository name for the created image
     *     (string)tag: Tag name for the create image
     *     (string)comment: Commit message
     *     (string)author: author (e.g., “John Hannibal Smith <hannibal@a-team.com>“)
     *     (string)pause: 1/True/true or 0/False/false, whether to pause the container before committing
     *     (string)changes: Dockerfile instructions to apply while committing
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\CommitResult
     */
    public function commit(\Docker\API\Model\ContainerConfig $containerConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('container', null);
        $queryParam->setDefault('repo', null);
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('comment', null);
        $queryParam->setDefault('author', null);
        $queryParam->setDefault('pause', null);
        $queryParam->setDefault('changes', null);
        $url      = '/v1.21/commit';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $this->serializer->serialize($containerConfig, 'json');
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_OBJECT == $fetch) {
            if ('201' == $response->getStatusCode()) {
                return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\CommitResult', 'json');
            }
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
        $url        = '/v1.21/images/{name}/get';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $queryParam->buildFormDataString($parameters);
        $request    = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Get a tarball containing all images and metadata for one or more repositories.
     *
     * @param array $parameters List of parameters
     * 
     *     (array)names: Image names to filter
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function saveAll($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('names', null);
        $url      = '/v1.21/images/get';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $queryParam->buildFormDataString($parameters);
        $request  = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Load a set of images and tags into a Docker repository. See the image tarball format for more details.
     *
     * @param string $imagesTarball Tar archive containing images
     * @param array  $parameters    List of parameters
     * @param string $fetch         Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function load(string $imagesTarball, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/v1.21/images/load';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $imagesTarball;
        $request    = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);

        return $response;
    }
}
