<?php

namespace Flossiraptor\Imds4azure\Service;

use Flossiraptor\Imds4azure\Exception\IMDSNotFoundException;
use Flossiraptor\Imds4azure\Exception\InvalidResponseException;
use Flossiraptor\Imds4azure\ImmutableToken;
use Flossiraptor\Imds4azure\Token;
use Flossiraptor\Imds4azure\Utility\HttpClientAwareTrait;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;

/**
 * Request OAuth tokens using the IMDS Managed Identity capability.
 */
class Identity {

  use HttpClientAwareTrait;

  /**
   * Relative path to retrieve a managed identity token.
   */
  const RESOURCE = '/metadata/identity/oauth2/token';

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
   * Get a token for a Managed Identity resource.
   *
   * @param string $resource
   *   Name of the resource.
   * @param string $client_id
   *   (optional) Set the client ID when the Managed Identity service has
   *   access to multiple identities.
   *
   * @return \Flossiraptor\Imds4azure\Token
   *   A managed identity token to authenticate to the named resource.
   */
  public function getToken(string $resource, ?string $client_id = NULL) : Token {
    try {
      $options = [
        'query' => [
          'resource' => $resource,
        ],
      ];
      if ($client_id) {
        $options['query']['client_id'] = $client_id;
      }

      /** @var \Psr\Http\Message\ResponseInterface $result */
      $result = $this
        ->getHttpClient()
        ->request('get', self::RESOURCE, $options);

      $data = json_decode((string) $result->getBody());
      if (is_null($data)) {
        throw new InvalidResponseException('The IMDS response did not contain valid JSON.');
      }
      $token = new ImmutableToken(
         access_token: $data->access_token,
         expires_in:   $data->expires_in,
         expires_on:   $data->expires_on,
         client_id:    $data->client_id,
         resource:     $data->resource,
         token_type:   $data->token_type,
      );
      $token_wrapper = new Token($token, $this);
      return $token_wrapper;
    }
    catch (ConnectException $e) {
      throw new IMDSNotFoundException('Could not connect to the IMDS.', 0, $e);
    }
  }

}
