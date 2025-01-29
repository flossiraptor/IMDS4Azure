<?php

namespace Flossiraptor\Imds4azure;

/**
 * Token data for a Managed Identity.
 */
class ImmutableToken {

  /**
   * Constructor.
   */
  public function __construct(
    public readonly string $access_token,
    public readonly int $expires_in,
    public readonly int $expires_on,
    public readonly string $client_id,
    public readonly string $resource,
    public readonly string $token_type,
    ) {
  }

}
