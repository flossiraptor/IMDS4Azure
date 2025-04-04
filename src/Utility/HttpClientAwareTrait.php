<?php

namespace Flossiraptor\Imds4azure\Utility;

use Flossiraptor\Imds4azure\IMDS;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * Setter and Getter methods for a Guzzle HTTP client.
 */
trait HttpClientAwareTrait {

  /**
   * Guzzle HTTP client.
   *
   * @param \GuzzleHttp\ClientInterface
   */
  protected ?ClientInterface $httpClient = NULL;

  /**
   * Set the HTTP client.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   A Guzzle HTTP client.
   */
  public function setHttpClient(ClientInterface $client) : void {
    $this->httpClient = $client;
  }

  /**
   * Get the HTTP client.
   *
   * @return \GuzzleHttp\ClientInterface
   *   A Guzzle HTTP client.
   */
  public function getHttpClient() : ClientInterface {
    if (!$this->httpClient) {
      $this->initializeHttpClient();
    }
    return $this->httpClient;
  }

  /**
   * Merge request-specific options with the defaults provided by the client.
   *
   * @param array $options
   *   Options to pass to the Guzzle client.
   *
   * @return array
   *   The requested options, merged with the client defaults.
   */
  protected function mergeDefaultOptions(array $options) : array {
    $config = $this
      ->getHttpClient()
      ->getConfig();

    return array_replace_recursive($config, $options);
  }

  /**
   * Initialize the HTTP client with a default non-proxying client.
   */
  protected function initializeHttpClient() : void {
    $this->httpClient = new Client([
      'base_uri' => sprintf('http://%s', IMDS::ENDPOINT),
      'allow_redirects' => FALSE,
      'headers' => [
        'Metadata' => 'true',
        'User-Agent' => IMDS::USER_AGENT,
      ],
      'proxy' => [
        'no' => '*',
      ],
      'query' => [
        'api-version' => IMDS::API_VERSION,
      ],
      'timeout'  => 2.0,
    ]);
  }

}
