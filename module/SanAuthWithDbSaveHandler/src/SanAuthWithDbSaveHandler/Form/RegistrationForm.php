<?php
//filename : module/SanAuthWithDbSaveHandler/src/SanAuthWithDbSaveHandler/Form/Registration.php
namespace SanAuthWithDbSaveHandler\Form;

use Zend\Form\Form;
use Zend\InputFilter;

class RegistrationForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'options' => array(
                'label' => 'New User : '
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'options' => array(
                'label' => 'Password : '
            ),
        ));

         $this->add(array(
            'name' => 'Registrationsubmit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Submit',
                'id' => 'Registrationsubmit',
            ),
        ));

        $this->setInputFilter($this->createInputFilter());
    }

    public function createInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        //username
        $username = new InputFilter\Input('username');
        $username->setRequired(true);
        $inputFilter->add($username);

        //password
        $password = new InputFilter\Input('password');
        $password->setRequired(true);
        $inputFilter->add($password);

        return $inputFilter;
    }
}
