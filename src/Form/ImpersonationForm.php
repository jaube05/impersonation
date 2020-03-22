<?php
/**
 * @file
 * Contains \Drupal\impersonation\Form\ImpersonationForm.
 */
namespace Drupal\impersonation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal;
use Drupal\user\Entity\Role;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

class ImpersonationForm extends FormBase {
  protected $listOfRoles;
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'impersonation_role_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = array(
      'query' => drupal_get_destination()
    );
    $linkGenerator = Drupal::linkGenerator();

    // Add placeholder
    $listOfRoles[] = "Role";
    $limitedRoles[] = "Role";

    //
    /** @var Role $role */


    foreach (user_roles() as $role) {
      if ($role->id() != 'impersonation') {

        $url = Url::fromRoute('impersonation.switchrole', array('rid' => $role->id()), $options);
        $url = $url->toString();
        $listOfRoles[$url] = $role->label();
        // $limitedRoles is being saved to user that does not have administrator role.
        if($role->label() == 'CSR' || $role->label() == 'Store Manager' || $role->label() == 'Market Manager'
            || $role->label() == 'Support Office Employee' || $role->label() == 'Franchise Owner' || $role->label() == 'Franchise Employee') {
              $limitedRoles[$url] = $role->label();
            }

      }
    };
    $urlBUDefault = 0;
    if(isset($_SESSION['impersonation_roles'])){
      //Gets the session url to save in the selected form list
      $url = Url::fromRoute('impersonation.switchrole', array('rid' => $_SESSION['impersonation_roles']), $options);
      $urlBUDefault = $url->toString();
    }
    //
    // Add reset roles link.
    $urlReset = Url::fromRoute('impersonation.switchrole', array('rid' => 'reset'), $options);
    $urlReset = $urlReset->toString();
    $listOfRoles[$urlReset] = 'Reset';
    $limitedRoles[$urlReset] = 'Reset';

    /** @var AccountProxyInterface */
    $sessionUser = \Drupal::currentUser()->getRoles();
    $currentUser = "";

    //Check user roles
    foreach($sessionUser as $userRole){

      $split = explode('_', $userRole);

      foreach($split as $admin){
        if($admin == 'administrator') {
          $currentUser = 'administrator';
        }
      }

      if (isset($_SESSION['impersonation_roles'])) {
        $split2 = explode('_', $_SESSION['impersonation_roles']);

        foreach($split2 as $impRole){
          if($impRole == 'administrator') {
            $currentUser = 'administrator';
          }
        }
      }

    }
    // exit;
    if($currentUser === 'administrator'){
      $form['role_impersonation'] = array(
          '#type' => 'select',
          '#options' => $listOfRoles,
          '#attributes' => array('onchange' => 'document.getElementById("impersonation-role-form").action = this.value; this.form.submit();'),
          '#default_value' => $urlBUDefault,
        );
    }
    else {
      $form['role_impersonation'] = array(
          '#type' => 'select',
          '#options' => $limitedRoles,
          '#attributes' => array('onchange' => 'document.getElementById("impersonation-role-form").action = this.value; this.form.submit();'),
          '#default_value' => $urlBUDefault,
        );
    }


    $form['#impersonation-role-form']['#attributes']['placeholder'] = 'Role';


    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Not needed as the selected form will auto submit selection
  }
}
