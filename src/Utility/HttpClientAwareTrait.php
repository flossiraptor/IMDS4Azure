<?php

namespace Flossiraptor\Imds4azure\Utility;

use Flossiraptor\Imds4azure\IMDS;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

/**
 * Setter and Getter methods for a PSR-18 HTTP client.
 */
trait HttpClientAwareTrait {

  /**
   * PSR-18 compliant HTTP client.
   *
   * @param \Psr\Http\Client\ClientInterface
   */
  protected ?ClientInterface $httpClient = NULL;

  /**
   * Set the HTTP client.
   *
   * @param \Psr\Http\Client\ClientInterface $client
   *   A PSR-18 compliant HTTP client.
   */
  protected function setHttpClient(ClientInterface $client) : void {
    $this->httpClient = $client;
  }

  /**
   * Get the HTTP client.
   *
   * @return \Psr\Http\Client\ClientInterface
   *   A PSR-18 compliant HTTP client.
   */
  protected function getHttpClient() : ClientInterface {
    if (!$this->httpClient) {
      $this->initializeHttpClient();
    }
    return $this->httpClient;
  }

  /**
   * Initialize the HTTP client with a default non-proxying client.
   */
  protected function initializeHttpClient() : void {
    $this->client = new Client([
      'base_uri' => IMDS::ENDPOINT,
      'allow_redirects' => FALSE,
      'headers' => [
        'Metadata' => 'true',
        'User-Agent' => IMDS::USER_AGENT,
      ],
      'proxy' => [
        'no' => '*',
      ],
      'query' => [
        'api_version' => IMDS::API_VERSION,
      ],
      'timeout'  => 2.0,
    ]);
  }

}
