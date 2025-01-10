<?php

namespace Flossiraptor\Imds4azure\Service;

use Flossiraptor\Imds4azure\Exception\IMDSNotFoundException;
use Flossiraptor\Imds4azure\Exception\InvalidResponseException;
use Flossiraptor\Imds4azure\Metadata;
use Flossiraptor\Imds4azure\MetadataInterface;
use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;

/**
 * Query instance metadata from the IMDS.
 */
class Instance implements MetadataInterface {

  use HttpClientAwareTrait;

  /**
   * Relative path to retrieve instance metadata.
   */
  const RESOURCE = '/metadata/instance';

  /**
   * Cache of the full instance metadata.
   *
   * Use the `Instance::refresh()` method to clear the cache.
   *
   * @var \Flossiraptor\Imds4azure\MetadataInterface
   */
  protected ?MetadataInterface $metadata = NULL;

  /**
   * Constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   A Guzzle HTTP client.
   */
  public function __construct(ClientInterface $client) {
    $this->setHttpClient($client);
  }

  /**
   * {@inheritdoc}
   */
  public function get($resource = NULL) : object|array|string|null {
    if (empty($this->metadata)) {
      $this->doFetch();
    }
    return $this->metadata->get($resource);
  }

  /**
   * {@inheritdoc}
   */
  public function has(string $resource) : bool {
    return $this->metadata->has($resource);
  }

  /**
   * {@inheritdoc}
   */
  public function keys() : array {
    return $this->metadata->keys();
  }

  /**
   * Clear any cached metadata.
   *
   * @return \Flossiraptor\Imds4azure\Service\Metadata
   *   Return this object for a fluent API.
   */
  public function refresh() : static {
    $this->metadata = NULL;
    return $this;
  }

  /**
   * Fetch the metadata from the IMDS.
   */
  protected function doFetch() : void {
    try {
      /** @var \Psr\Http\Message\ResponseInterface $result */
      $result = $this
        ->getHttpClient()
        ->request('get', self::RESOURCE);

      $data = json_decode((string) $result->getBody());
      if (is_null($data)) {
        throw new InvalidResponseException('The IMDS response did not contain valid JSON.');
      }
      $this->metadata = new Metadata($data);
    }
    catch (ConnectException $e) {
      throw new IMDSNotFoundException('Could not connect to the IMDS.', 0, $e);
    }
  }

}
