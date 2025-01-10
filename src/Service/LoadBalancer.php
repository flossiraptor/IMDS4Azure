<?php

namespace Flossiraptor\Imds4azure\Service;

use Flossiraptor\Imds4azure\Metadata;
use Flossiraptor\Imds4azure\MetadataInterface;
use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use Psr\Http\Client\ClientInterface;

/**
 * Query load-balancer metadata from the IMDS.
 */
class LoadBalancer implements MetadataInterface {

  use HttpClientAwareTrait;

  /**
   * Relative path to retrieve instance metadata.
   */
  const RESOURCE = '/metadata/loadbalancer';

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
   * @param \Psr\Http\Client\ClientInterface $client
   *   A PSR-18 compliant HTTP client.
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
    $result = '{
   "loadbalancer": {
    "publicIpAddresses":[
      {
         "frontendIpAddress":"51.0.0.1",
         "privateIpAddress":"10.1.0.4"
      }
   ],
   "inboundRules":[
      {
         "frontendIpAddress":"50.0.0.1",
         "protocol":"tcp",
         "frontendPort":80,
         "backendPort":443,
         "privateIpAddress":"10.1.0.4"
      },
      {
         "frontendIpAddress":"2603:10e1:100:2::1:1",
         "protocol":"tcp",
         "frontendPort":80,
         "backendPort":443,
         "privateIpAddress":"ace:cab:deca:deed::1"
      }
   ],
   "outboundRules":[
      {
         "frontendIpAddress":"50.0.0.1",
         "privateIpAddress":"10.1.0.4"
      },
      {
         "frontendIpAddress":"2603:10e1:100:2::1:1",
         "privateIpAddress":"ace:cab:deca:deed::1"
      }
    ]
   }
}';
    $this->metadata = new Metadata(json_decode($result));
  }

}
