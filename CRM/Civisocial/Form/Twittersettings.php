<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civisocial_Form_Twittersettings extends CRM_Core_Form {




    function preProcess() {
        // Perform any setup tasks you may need
        // often involves grabbing args from the url and storing them in class variables
        $this->_foo = CRM_Utils_Request::retrieve('foo', 'string');
    }



    function buildQuickForm() {

        CRM_Utils_System::setTitle(ts('Civisocial Twitter Credentials '));


        $this->add('text', 'twitterappid', ts('Twitter App ID'));
        $this->add('text', 'twitterappname', ts('Twitter App Name'));
        $this->add('text', 'twitterapp_consumerkey', ts('Twitter Consumer Key'));
        $this->add('text', 'twitterapp_ConsumerSecret', ts('Twitter Consumer Secret '));



        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => ts('Submit'),
                'isDefault' => TRUE,
            ),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());
        parent::buildQuickForm();
    }

    function postProcess() {
        $values = $this->exportValues();




        parent::postProcess();
    }



    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     *
     *
     */
    function getRenderableElementNames() {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }
}

function addRules() {

    $this->addFormRule(array('CRM_Example_Form', 'myRules'));
}
/**
 * Here's our custom validation callback
 */
function myRules($values) {
    $errors = array();
    if ($values['foo'] != 'abc') {
        $errors['foo'] = ts('You entered the wrong text!');
    }
    return empty($errors) ? TRUE : $errors;
}

function postProcess() {
    // get the submitted values as an array
    $vals = $this->controller->exportValues($this->_name);


    // Save to the database
    civicrm_api3('foo', 'create', $vals);
}
