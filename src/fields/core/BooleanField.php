<?php
/**
 * Created by PhpStorm.
 * User: Wes
 * Date: 1/28/2016
 * Time: 8:59 PM
 */

namespace WTForms\Fields\Core;

use WTForms\Widgets\Core\CheckboxInput;

/**
 * Represents an ``<input type="checkbox">``. Set the ``checked``-status by using the
 * ``default``-option. Any value for ``default``, e.g. ``default="checked"`` puts
 * ``checked`` into the html-element and sets the ``data`` to ``true``
 *
 * @package WTForms\Fields
 */
class BooleanField extends Field
{
  /**
   * @var array A sequence of strings to be considered "false" values for the field
   */
  public $false_values = ['false', ''];

  /**
   * @var boolean
   */
  public $data;

  /**
   * Field constructor.
   *
   * @param string $label
   * @param array  $options In addition to {@link Field}'s options, you may
   *                       also pass an entry with key ``'false_values'`` which is a sequence of
   *                       strings of what is considered a "false" value. Defaults are ``['false', '']``
   *
   * @throws \TypeError
   */
  public function __construct($label = "", array $options = [])
  {
    parent::__construct($label, $options);
    $this->widget = new CheckboxInput();
    if (array_key_exists("false_values", $options) && is_array($options['false_values'])) {
      $this->false_values = $options['false_values'];
    }
  }

  /**
   * Process the data applied to this field and store the result.
   *
   * This will be called during form construction by the form's `options` or
   * `obj` argument.
   *
   * @param string|array $value
   */
  public function processData($value)
  {
    $this->data = boolval($value);
  }

  /**
   * @inheritdoc
   */
  public function processFormData(array $valuelist)
  {
    if (!$valuelist || in_array($valuelist[0], $this->false_values)) {
      $this->data = false;
    } else {
      $this->data = true;
    }
  }

  public function __get($name)
  {
    if (in_array($name, ['value'])) {
      if ($this->raw_data !== null && !empty($this->raw_data)) {
        return strval($this->raw_data[0]);
      }

      return 'y';
    }

    return null;
  }
}
