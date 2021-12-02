<?php

namespace SumaerJolly\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class WaveResourceOwner implements ResourceOwnerInterface
{
  use ArrayAccessorTrait;

  /**
   * @var array
   */
  protected $response;

  /**
   * @param array $response
   */
  public function __construct(array $response)
  {
    $this->response = $response;
  }

  public function getId()
  {
    return $this->getValueByKey($this->response, 'data.user.id');
  }

  public function getEmail()
  {
    return $this->getValueByKey($this->response, 'data.user.defaultEmail');
  }

  public function getFirstName()
  {
    return $this->getValueByKey($this->response, 'data.user.firstName');
  }

  public function getLastName()
  {
    return $this->getValueByKey($this->response, 'data.user.lastName');
  }

  public function toArray()
  {
    return $this->response;
  }
}
