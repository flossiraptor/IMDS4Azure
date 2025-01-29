<?php

namespace Flossiraptor\Imds4azure;

use Flossiraptor\Imds4azure\Service\Identity;

/**
 * Authentication token for a Managed Identity.
 */
class Token {

  /**
   * Refresh the token when it has 25% of its validity period remaining.
   */
  const REFRESH_WHEN_REMAINING = 0.25;

  /**
   * Refresh the token when the time reaches this unix-epoch.
   *
   * @var int
   */
  protected int $refresh_on;

  /**
   * Constructor.
   */
  public function __construct(
    protected ImmutableToken $token,
    protected Identity $refresh_service
    ) {
    // Establish the time when the token is ready for refresh.
    $refreshWhenRemaining = self::REFRESH_WHEN_REMAINING * $token->expires_in;
    $this->refresh_on = $token->expires_on - $refreshWhenRemaining;
  }

  /**
   * Set the refresh service.
   *
   * @param \Flossiraptor\Imds4azure\Service\Identity $identity
   *   The service which will refresh the token data when it's expired.
   */
  public function setRefreshService(Identity $identity) {
    $this->refresh_service = $identity;
  }

  /**
   * Ensure the token is valid, and refresh the token if necessary.
   */
  public function ensureTokenIsValid() : void {
    if (time() > $this->refresh_on) {
      $this->refresh();
    }
  }

  /**
   * Fetch a new token to replace the existing token.
   */
  public function refresh() : void {
    $this->token = $this
      ->refresh_service
      ->getToken($this->token->resource, $this->token->client_id)
      ->getToken();
  }

  /**
   * Get the token data.
   *
   * @return \Flossiraptor\Imds4azure\ImmutableToken
   *   The token data provided by the IMDS Managed Identity provider.
   */
  public function getToken() : ImmutableToken {
    return $this->token;
  }

  /**
   * Get the access token to use in a Managed Identity authentication.
   *
   * @return string
   *   The access token.
   */
  public function getAccessToken() : string {
    $this->ensureTokenIsValid();
    return $this->token->access_token;
  }

  /**
   * Fetch the token's authentication value.
   *
   * @return string
   *   The authentication token, to use when authenticating to a Managed
   *   Identity service.
   */
  public function __toString() {
    return $this->getAccessToken();
  }

}
