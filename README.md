[![Build Status](https://travis-ci.org/Deathnerd/php-wtforms.svg?branch=master)](https://travis-ci.org/Deathnerd/php-wtforms)

[![codecov](https://codecov.io/gh/Deathnerd/php-wtforms/branch/master/graph/badge.svg)](https://codecov.io/gh/Deathnerd/php-wtforms)
# php-wtforms
A PHP rewrite of the fantastic Python library WTForms. 

I do not take credit for the idea behind WTForms or anything related to its original implementation. I just bastardized it and ported it to PHP. 

# Install
Add the following line to the `require` portion of your `composer.json`
```json
"deathnerd/php-wtforms":"0.5.2"
```
or if you're feeling froggy, pull in the cutting edge master release
```json
"deathnerd/php-wtforms":"dev-master"
```
or run the following command from your favorite terminal
`composer require deathnerd/php-wtforms:0.5.2`
for the stable version and 
`composer require deathnerd/php-wtforms:dev-master`
for the bleeding edge dev release.

Note: The dev-master version is not guaranteed to be stable.

# Quick Start
To create a simple login-form it's as simple as this:
###LogInForm.php
```php
<?php
namespace MyNamespace\Forms;

use WTForms\Form;
use WTForms\Fields\Simple\PasswordField;
use WTForms\Fields\Simple\SubmitField;
use WTForms\Fields\Core\StringField;
use WTForms\Validators\InputRequired;
use WTForms\Validators\Length;
use WTForms\Validators\Regexp;
use MyNamespace\Validators\NotContains;

class LogInForm extends Form {
    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->username = new StringField(["validators"=>[
            new InputRequired("You must provide a username"),
            new Length("Usernames must be between %(min)d and %(max)d characters long", ["min"=>3, "max"=>10]),
            new NotContains("Usernames may not contain the following characters: ;-/@", ["invalid_members"=>[";","-","/","@"]])
        ]]);
        $this->password = new PasswordField(["validators"=>[
            new InputRequired("Can't log in without a password!"),
            new Length("Passwords must be at least %(min)d characters in length", ["min"=>5])
        ]]);
        $this->submit = new SubmitField(["label"=>"Submit"]);
    }
}
```
###NotContains.php
```php
<?php
namespace MyNamespace\Validators;

use WTForms\Validators\Validator;
use WTForms\Form;
use WTForms\Fields\Core\Field;
use WTForms\Exceptions\ValidationError;

class NotContains extends Validator
{
    /**
     * @var string|array
     */
    public $invalid_members;

    /**
     * @param string $message Error message to raise in case of a validation error
     * @param array  $options
     */
    public function __construct($message = "", array $options = ['invalid_members' => []])
    {
        assert(!empty($options['invalid_members']), "Doesn't make sense to not have any invalid members");
        $this->invalid_members = $options['invalid_members'];
        $this->message = $message;
    }

    /**
     * @param Form   $form
     * @param Field  $field
     * @param string $message
     *
     * @return mixed True if the field passed validation, a Validation Error if otherwise
     * @throws ValidationError
     */
    public function __invoke(Form $form, Field $field, $message = "")
    {
        if (strlen($field->data) != strlen(str_replace($this->invalid_members, "", $field->data))) {
            if ($message == "") {
                if ($this->message == "") {
                    $message = "Invalid Input.";
                } else {
                    $message = $this->message;
                }
            }
            throw new ValidationError($message);
        }

        return true;
    }
}
```
###login.php
```php
<?php
require_once 'vendor/autoload.php';

use MyNamespace\Forms\LogInForm;

$form = LogInForm::create(["formdata" => $_POST]);

if ($_POST) {
    if ($form->validate()) {
        // do stuff to log in and authenticate the user
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LogIn Form</title>
</head>
<body>
<?php if ($form->errors) { ?>
    <ul class="errors">
        <?php
        foreach ($form->errors as $field_name => $errors) {
            foreach ($errors as $field_error) { ?>
                <li><?= $field_name ?> - <?= $field_error ?></li>
                <?php
            }
        }
        ?>
    </ul>
<?php } ?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <?= $form->username->label ?>
    <?= $form->username ?>
    <?= $form->password->label ?>
    <?= $form->password ?>
    <?= $form->submit ?>
</form>
</body>
</html>
```

And that's that. More in-depth examples and actual documentation are coming in the future. For now, look in the `tests` directory for ideas on how to do things
