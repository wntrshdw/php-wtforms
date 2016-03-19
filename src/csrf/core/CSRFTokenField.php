<?php
namespace WTForms\CSRF\Core;

use WTForms;
use WTForms\Fields\Core\HiddenField;
use WTForms\Form;
use WTForms\Validators\ValidationError;

class CSRFTokenField extends HiddenField
{
  /**
   * The current CSRF token
   * @var string
   */
  public $current_token = "";

  /**
   * @var CSRF
   */
  public $csrf_impl;

  /**
   * CSRFTokenField constructor.
   *
   * @param array $options
   */
  public function __construct(array $options = [])
  {
    $defaults = ['label' => 'CSRF Token', 'csrf_impl', 'WTForms\CSRF\Core\CSRF'];
    $options = array_merge($defaults, $options);

    $c = $options['csrf_impl'];
    $this->csrf_impl = new $c();
    unset($options['csrf_impl']);
    $label = $options['label'];
    unset($options['label']);
    parent::__construct($label, $options);
  }

  /**
   * We want to always return the current token on render, regardless of
   * whether a good or bad token was passed
   * @return string
   */
  public function __get($name)
  {
    if (in_array($name, ['value'])) {
      return $this->current_token;
    }
    return null;
  }

  /**
   * Don't populate objects with CSRF Token
   */
  public function populateObj($obj, $name)
  {
    return $obj;
  }

  /**
   * Handle the validation of this token field.
   *
   * @param Form $form
   *
   * @throws ValidationError
   */
  public function preValidate(Form $form)
  {
    $this->csrf_impl->validateCSRFToken($form, $this);
  }

  /**
   * @param      $formdata
   * @param null $data
   *
   * @throws WTForms\NotImplemented
   */
  public function process($formdata, $data = null)
  {
    parent::process($formdata, $data);
    $this->current_token = $this->csrf_impl->generateCSRFToken($this);
  }
}