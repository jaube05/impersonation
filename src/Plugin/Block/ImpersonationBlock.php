<?php
/**
 * @file
 * Contains Drupal\impersonation\Plugin\Block\ImpersonationBlock.
 */

namespace Drupal\impersonation\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\impersonation\Form\ImpersonationForm;

/**
 * Provides a block with options to switch a role.
 *
 * @Block(
 *   id = "impersonation_block",
 *   admin_label = @Translation("Switch Role")
 * )
 */
class ImpersonationBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\impersonation\Form\ImpersonationForm');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    // return $account->hasPermission('administer permissions');
    if ( AccessResult::allowedIfHasPermission($account, 'view role impersonation') ) {
      return AccessResult::allowedIfHasPermission($account, 'view role impersonation');
    }
  }
}
