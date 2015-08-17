<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */


class CRM_Civisocial_Form_Facebooksettings extends CRM_Core_Form
{


    //build the form


    function buildQuickForm()
    {

        CRM_Utils_System::setTitle(ts('Civisocial Facebook Credentials '));


        $this->add('text', 'App_ID', ts('Facebook App ID'));
        $this->add('text', 'App_secret', ts('Facebook App Secret'));
        $this->add('text', 'App_Name', ts('Facebook App Name'));


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
     * Process the form after it has been submitted
     *
     */



    function postProcess() {
        $values = $this->exportValues();
        $this->save_CivisocialFacebooksettings($values);
        parent::postProcess();
    }



    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     *
     *
     */
    function getRenderableElementNames()
    {

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

        $this->addFormRule(array('CRM_Civisocial_Form_Facebooksettings', 'validate_appid_empty'));
        $this->addFormRule(array('CRM_Civisocial_Form_Facebooksettings', 'validate_appname_empty'));
        $this->addFormRule(array('CRM_Civisocial_Form_Facebooksettings', 'validate_appsecret_empty'));

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


    function validate_appsecret_empty($fields)
    {

        if (empty($fields['App_secret'])) {
            $errors['App_secret'] = ts('Application Secret can not be empty');
            return $errors;
        }
        return TRUE;

    }


    protected function save_CivisocialFacebooksettings($values)
    {
        $fields = CRM_Civisocial_DAO_CivisocialFacebooksettings::fields();

        foreach ($fields as $field) {

            if (isset($values[$field['name']])) {
                $params[$field['name']] = $values[$field['name']];

            }
        }

        CRM_Civisocial_BAO_CivisocialFacebooksettings::add($params);


    }


}




