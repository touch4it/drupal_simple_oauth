<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Authentication\TokenAuthUserInterface.
 */

namespace Drupal\oauth2_token\Authentication;


use Drupal\user\UserInterface;

interface TokenAuthUserInterface extends \IteratorAggregate, UserInterface {}