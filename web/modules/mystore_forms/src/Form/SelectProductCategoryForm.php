<?php

namespace Drupal\mystore_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\commerce_product\Entity\Product;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_store\Entity\Store;

/**
 * Provides a MyStore Forms form.
 */
class SelectProductCategoryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mystore_forms_select_product_category';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $options = [];
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => 'catalog',
      ]);

    foreach ($terms as $term) {
      $options[$term->id()] = $term->label();
    }

    $form['category'] = [
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Select category and continue'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $term = Term::load($form_state->getValue('category'));
    $product_type = $term->get('field_product_type');

    if ($product_type->isEmpty()) {
      $form_state->setErrorByName('category', $this->t('You can\'t select this option.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $category = Term::load($form_state->getValue('category'));
    
    $product_type = $category
      ->get('field_product_type')
      ->first()
      ->get('entity')
      ->getTarget()
      ->getValue()
      ->getVariationTypeId();

      
    $stores = \Drupal::entityTypeManager()
      ->getStorage('commerce_store')
      ->loadByProperties([
        'uid' => \Drupal::currentUser()->id(),
      ]);

    $product = Product::create([
      'type' => $product_type,
      'field_category' => $category,
      'stores' => $stores,
    ]);

    $product->save();
    $url = Url::fromRoute('entity.commerce_product.edit_form', ['commerce_product' => $product->id()])->toString();
    $response = new RedirectResponse($url);
    $response->send();
  }

}
