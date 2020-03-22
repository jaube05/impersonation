<?php

namespace Drupal\impersonation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Access\AccessResult;

class ImpersonationController extends ControllerBase {

  public function switchRole($rid, Request $request) {
    // Redirect to the front page if destination does not exist.
    $destination = $request->get('destination');
    $url = empty($destination) ? '/' : $destination;

    if ($rid == 'reset') {
      unset($_SESSION['impersonation_roles']);
      return new RedirectResponse($url);
    }

    /** @var AccountProxyInterface */
    $sessionUser = \Drupal::currentUser();

    /** @var UserInterface $user */
    $user = user_load($sessionUser->id());

    // Clear current roles.
    foreach ($user->getRoles() as $rolename) {
      if ($rolename != 'roleswitcher') {
        $user->removeRole($rolename);
      }
    }
    //Save user selection in session
    $_SESSION['impersonation_roles'] = $rid;
    // Assign requested role.
    $user->addRole($rid);

    $sessionUser->setAccount($user);
    return new RedirectResponse($url);
  }
  /**
  * Checks access for this controller.
  */
  public function access() {
    return AccessResult::allowed();
  }
}
