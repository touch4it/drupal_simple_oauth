<?php

namespace Drupal\simple_oauth\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Drupal\simple_oauth\Entities\UserEntity;

class UserRepository implements UserRepositoryInterface
{

    /**
   * {@inheritdoc}
   */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        if ($username === 'alex' && $password === 'whisky') {
            return new UserEntity();
        }

        return;
    }

}
