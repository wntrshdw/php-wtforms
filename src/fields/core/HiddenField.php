<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/20/2016
 * Time: 10:02 PM
 */

namespace WTForms\Fields\Core;


use WTForms\Widgets\Core\HiddenInput;

class HiddenField extends StringField
{
  public function __construct($label, array $options)
  {
    parent::__construct($label, $options);
    $this->widget = new HiddenInput();
  }
}
