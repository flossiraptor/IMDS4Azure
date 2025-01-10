<?php

namespace Flossiraptor\Imds4azure\Service;

use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use GuzzleHttp\ClientInterface;

/**
 * Request OAuth tokens using the IMDS Managed Identity capability.
 */
class Identity {

  use HttpClientAwareTrait;

  /**
   * Constructor.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   A Guzzle HTTP client.
   */
  public function __construct(ClientInterface $client) {
    $this->setHttpClient($client);
  }

}
