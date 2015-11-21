<?php

/**
 * @file
 * Contains \Drupal\token_auth\Authentication\TokenAuthUserInterface.
 */

namespace Drupal\token_auth\Authentication;


use Drupal\user\UserInterface;

interface TokenAuthUserInterface extends \IteratorAggregate, UserInterface {}