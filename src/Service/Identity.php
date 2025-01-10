<?php

namespace Flossiraptor\Imds4azure\Service;

use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use Psr\Http\Client\ClientInterface;

/**
 * Request OAuth tokens using the IMDS Managed Identity capability.
 */
class Identity {

  use HttpClientAwareTrait;

  /**
   * Constructor.
   *
   * @param \Psr\Http\Client\ClientInterface $client
   *   A PSR-18 compliant HTTP client.
   */
  public function __construct(ClientInterface $client) {
    $this->setHttpClient($client);
  }

}
