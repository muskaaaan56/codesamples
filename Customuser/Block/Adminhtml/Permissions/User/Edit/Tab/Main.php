<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms page edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magentothem_Customuser_Block_Adminhtml_Permissions_User_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('permissions_user');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('user_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('adminhtml')->__('Account Information')));
        $readonly = '';

        if ($model->getUserId()) {
            $fieldset->addField('user_id', 'hidden', array('name' => 'user_id',));
            $readonly = 'readonly';
        } else {
            if (! $model->hasData('is_active')) {
                $model->setIsActive(1);
            }
        }

        $fieldset->addField(
            'username',
            'text',
            array(
              'name'  => 'username',
              'label' => Mage::helper('adminhtml')->__('User Name'),
              'id'    => 'username',
              'title' => Mage::helper('adminhtml')->__('User Name'),
              'required' => TRUE,
              $readonly => '',
            )
        );

        $fieldset->addField(
            'firstname',
            'text',
            array(
              'name'  => 'firstname',
              'label' => Mage::helper('adminhtml')->__('First Name'),
              'id'    => 'firstname',
              'title' => Mage::helper('adminhtml')->__('First Name'),
              'required' => TRUE,
              $readonly => '',
            )
        );

        $fieldset->addField(
            'lastname',
            'text',
            array(
              'name'  => 'lastname',
              'label' => Mage::helper('adminhtml')->__('Last Name'),
              'id'    => 'lastname',
              'title' => Mage::helper('adminhtml')->__('Last Name'),
              'required' => TRUE,
              $readonly => '',
            )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
              'name'  => 'email',
              'label' => Mage::helper('adminhtml')->__('Email ID'),
              'id'    => 'customer_email',
              'title' => Mage::helper('adminhtml')->__('User Email ID'),
              'class' => 'required-entry validate-email',
              'required' => TRUE,
              $readonly => '',
            )
        );

        if ($model->getUserId()) {
            $fieldset->addField(
                'password',
                'password',
                array(
                  'name'  => 'new_password',
                  'label' => Mage::helper('adminhtml')->__('New Password'),
                  'id'    => 'new_pass',
                  'title' => Mage::helper('adminhtml')->__('New Password'),
                  'class' => 'input-text validate-admin-password',
                )
            );

            $fieldset->addField(
                'confirmation',
                'password',
                array(
                  'name'  => 'password_confirmation',
                  'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                  'id'    => 'confirmation',
                  'class' => 'input-text validate-cpassword',
                )
            );
        }
        else {
           $fieldset->addField(
               'password',
               'password',
               array(
                  'name'  => 'password',
                  'label' => Mage::helper('adminhtml')->__('Password'),
                  'id'    => 'customer_pass',
                  'title' => Mage::helper('adminhtml')->__('Password'),
                  'class' => 'input-text required-entry validate-admin-password',
                  'required' => TRUE,
               )
           );
           $fieldset->addField(
               'confirmation',
               'password',
               array(
                  'name'  => 'password_confirmation',
                  'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                  'id'    => 'confirmation',
                  'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                  'class' => 'input-text required-entry validate-cpassword',
                  'required' => TRUE,
               )
           );
        }

        if (Mage::getSingleton('admin/session')->getUser()->getId() != $model->getUserId()) {
            $fieldset->addField(
                'is_active',
                'select',
                array(
                  'name'    => 'is_active',
                  'label'   => Mage::helper('adminhtml')->__('This account is'),
                  'id'      => 'is_active',
                  'title'   => Mage::helper('adminhtml')->__('Account Status'),
                  'class'   => 'input-select',
                  'style'    => 'width: 80px',
                  'options'  => array(
                                '1' => Mage::helper('adminhtml')->__('Active'),
                                '0' => Mage::helper('adminhtml')->__('Inactive')
                                ),
                )
            );
        }

        $fieldset->addField(
            'user_roles',
            'hidden',
            array(
              'name' => 'user_roles',
              'id'   => '_user_roles',
            )
        );

        $data = $model->getData();

        unset($data['password']);

        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
