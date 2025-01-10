<?php

namespace Flossiraptor\Imds4azure;

use Flossiraptor\Imds4azure\Service\Identity;
use Flossiraptor\Imds4azure\Service\Instance;
use Flossiraptor\Imds4azure\Service\LoadBalancer;
use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use GuzzleHttp\Client;

/**
 * Query the Azure IMDS.
 */
class IMDS {

  use HttpClientAwareTrait;

  /**
   * Current supported API version.
   */
  const API_VERSION = '2021-02-01';

  /**
   * The Instance Meta-Data Service operates on a fixed link-local address.
   */
  const ENDPOINT = '169.254.169.254';

  /**
   * User-agent string to provide with all requests.
   */
  const USER_AGENT = 'Flossiraptor/Imds4Azure';

  /**
   * Constructor.
   *
   * @param bool $initialize
   *   (optional) Set to FALSE to skip initialization of the HTTP client and
   *   metadata services.
   */
  public function __construct(bool $initialize = TRUE) {
    if ($initialize) {
      $this->initialize();
    }
  }

  /**
   * Request OAuth tokens from the IMDS.
   *
   * @var \Flossiraptor\Imds4azure\Service\Identity
   */
  protected ?Identity $identity;

  /**
   * Expose information about the instance.
   *
   * @var \Flossiraptor\Imds4azure\MetadataInterface
   */
  protected ?MetadataInterface $instance;

  /**
   * Expose information about the load-balancer(s) attached to the instance.
   *
   * @var \Flossiraptor\Imds4azure\MetadataInterface
   */
  protected ?MetadataInterface $loadbalancer;

  /**
   * Set the Identity service.
   *
   * @param \Flossiraptor\Imds4azure\Service\Identity $identity
   *   Service to manage IAM requests.
   *
   * @return \Flossiraptor\Imds4azure\IMDS
   *   Return $this for fluent method chaining.
   */
  public function setIdentity(Identity $identity) : static {
    $this->identity = $identity;
    return $this;
  }

  /**
   * Set the instance metadata service.
   *
   * @param \Flossiraptor\Imds4azure\MetadataInterface $instance
   *   Service to query instance metadata.
   *
   * @return \Flossiraptor\Imds4azure\IMDS
   *   Return $this for fluent method chaining.
   */
  public function setInstance(MetadataInterface $instance) : static {
    $this->instance = $instance;
    return $this;
  }

  /**
   * Set the load-balancer metadata service.
   *
   * @param \Flossiraptor\Imds4azure\MetadataInterface $loadbalancer
   *   Service to query load-balancer metadata.
   *
   * @return \Flossiraptor\Imds4azure\IMDS
   *   Return $this for fluent method chaining.
   */
  public function setLoadBalancer(MetadataInterface $loadbalancer) : static {
    $this->loadbalancer = $loadbalancer;
    return $this;
  }

  /**
   * Get the Identity service.
   *
   * @return \Flossiraptor\Imds4azure\Service\Identity
   *   Service to manage IAM requests.
   */
  public function identity() : Identity {
    return $this->identity;
  }

  /**
   * Get the instance metadata service.
   *
   * @return \Flossiraptor\Imds4azure\MetadataInterface
   *   Service to query instance metadata.
   */
  public function instance() : MetadataInterface {
    return $this->instance;
  }

  /**
   * Get the load-balancer metadata service.
   *
   * @return \Flossiraptor\Imds4azure\MetadataInterface
   *   Service to query load-balancer metadata.
   */
  public function loadbalancer() : MetadataInterface {
    return $this->loadbalancer;
  }

  /**
   * Initialize all services with a standard non-proxying HTTP client.
   */
  protected function initialize() : void {
    $client = $this->getHttpClient();

    $this->identity = new Identity($client);
    $this->instance = new Instance($client);
    $this->loadbalancer = new LoadBalancer($client);
  }

}
