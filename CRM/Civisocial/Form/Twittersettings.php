<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civisocial_Form_Twittersettings extends CRM_Core_Form {

    /*
     *
     * build form
     *
     */

    function buildQuickForm() {


        //set title of form
        CRM_Utils_System::setTitle(ts('Civisocial Twitter Credentials '));



        //Add form elements


        $this->add('text', 'App_ID', ts('Twitter App ID'));
        $this->add('text', 'App_Name', ts('Twitter App Name'));
        $this->add('text', 'Consumer_key', ts('Twitter Consumer Key'));
        $this->add('text', 'Consumer_secret', ts('Twitter Consumer Secret '));

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




    /*
     *
     * process form after submission
     *
     */


    function postProcess() {
        $values = $this->exportValues();

        $this->save_CivisocialTwittersettings($values);
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


//function to validate form data before submitting


function addRules()
{

    $this->addFormRule(array('CRM_Civisocial_Form_Twittersettings', 'validate_appid_empty'));
    $this->addFormRule(array('CRM_Civisocial_Form_Twittersettings', 'validate_appname_empty'));
    $this->addFormRule(array('CRM_Civisocial_Form_Twittersettings', 'validate_consumersecret_empty'));
    $this->addFormRule(array('CRM_Civisocial_Form_Twittersettings', 'validate_consumerkey_empty'));

}


function validate_appid_empty($fields)
{
    if (empty($fields['App_ID'])) {
        $errors['App_ID'] = ts('Application ID can not be empty');
        return $errors;
    }
    return TRUE;
}

function validate_appname_empty($fields)
{

    if (empty($fields['App_Name'])) {
        $errors['App_Name'] = ts('Application Name can not be empty');
        return $errors;
    }
    return TRUE;

}


function validate_consumersecret_empty($fields)
{

    if (empty($fields['Consumer_secret'])) {
        $errors['Consumer_secret'] = ts('Application Consumer Secret can not be empty');
        return $errors;
    }
    return TRUE;

}


    function validate_consumerkey_empty($fields)
    {

        if (empty($fields['Consumer_key'])) {
            $errors['Consumer_key'] = ts('Application consumer key can not be empty');
            return $errors;
        }
        return TRUE;

    }



    protected function save_CivisocialTwittersettings($values){
    $fields = CRM_Civisocial_DAO_CivisocialTwittersettings::fields();

    foreach ($fields as $field) {

        $params = array();

        if (isset($values[$field['name']])) {
            $params[$field['name']] = $values[$field['name']];

        }

    }



    CRM_Civisocial_BAO_CivisocialTwittersettings::add($params);




}

}