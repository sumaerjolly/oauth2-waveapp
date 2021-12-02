<?php

namespace SumaerJolly\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use SumaerJolly\OAuth2\Client\Provider\WaveResourceOwner;

class Wave extends AbstractProvider
{
  const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'id';

  /**
   * Constructs an OAuth 2.0 service provider.
   *
   * @param array $options An array of options to set on this provider.
   *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
   *     Individual providers may introduce more options, as needed.
   * @param array $collaborators An array of collaborators that may be used to
   *     override this provider's default behavior. Collaborators include
   *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
   *     Individual providers may introduce more collaborators, as needed.
   */
  public function __construct(array $options = [], array $collaborators = [])
  {
    parent::__construct($options, $collaborators);
  }

  public function getBaseAuthorizationUrl()
  {
    return 'https://api.waveapps.com/oauth2/authorize/';
  }

  public function getBaseAccessTokenUrl(array $params)
  {
    return 'https://api.waveapps.com/oauth2/token/';
  }

  public function getResourceOwnerDetailsUrl(AccessToken $token)
  {
    return "https://gql.waveapps.com/graphql/public";
  }

  public function getDefaultScopes()
  {
    return '';
  }

  protected function getAuthorizationHeaders($token = null)
  {
    return [
      'X-Access-Token' => $token,
      'X-Client-ID' => $this->clientId,
    ];
  }


  public function checkResponse(ResponseInterface $response, $data)
  {
    if (!empty($data['errors'])) {
      throw new IdentityProviderException($data['errors'], 0, $data);
    }

    return $data;
  }

  protected function createResourceOwner(array $response, AccessToken $token)
  {
    return new WaveResourceOwner($response);
  }

  protected function fetchResourceOwnerDetails(AccessToken $token)
  {
    $url = $this->getResourceOwnerDetailsUrl($token);

    // GraphQL query
    $query = 'query { user { id firstName lastName defaultEmail } }';

    $options = [
      'body' => json_encode(['query' => $query]),
      'headers' => [
        'content-type' => 'application/json',
      ],
    ];
    $request = $this->getAuthenticatedRequest(self::METHOD_POST, $url, $token, $options);

    $response = $this->getParsedResponse($request);

    return $response;
  }
}
