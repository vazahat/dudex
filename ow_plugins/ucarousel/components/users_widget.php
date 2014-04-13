<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package ucarousel.components
 * @since 1.0
 */
class UCAROUSEL_CMP_UsersWidget extends BASE_CLASS_Widget
{
    const UNIQ_NAME = 'index-UCAROUSEL_CMP_UsersWidget';

    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

        $count = (int) $params->customParamList['count'];
        $size = $params->customParamList['size'];
        $language = OW::getLanguage();

        $opts = array();
        
        if ( $params->customParamList['list'] == "by_role" )
        {
            $opts = $params->customParamList['roles'];
        }
        
        if ( $params->customParamList['list'] == "by_account_type" )
        {
            $opts = $params->customParamList['account_types'];
        }
        
        $list = $this->getList($params->customParamList['list'], $count, true, $opts);

        $users = new UCAROUSEL_CMP_Users($list, $size, $params->customParamList['infoLayout']);

        if ( !empty($list) )
        {
            $users->initCarousel(array
            (
                'autoplay' => $params->customParamList['autoplay'],
                'speed' => $params->customParamList['speed'],
                'dragging' => $params->customParamList['dragging'],
                'shape' => $params->customParamList['shape']
            ));
        }

        $this->addComponent('users', $users);
    }

    public function getList( $type, $count, $withPhoto = true, $opts = array() )
    {
        $service = UCAROUSEL_BOL_Service::getInstance();

        switch ( $type )
        {
            case 'latest':
                return $service->findLatestList($count, $withPhoto);

            case 'recently':
                return $service->findRecentlyActiveList($count, $withPhoto);

            case 'online':
                return $service->findOnlineList($count, $withPhoto);

            case 'featured':
                return $service->findFeaturedList($count, $withPhoto);
                
            case 'by_role':
                return $service->findByRoles($count, $opts, $withPhoto);
                
            case 'by_account_type':
                return $service->findByAccountTypes($count, $opts, $withPhoto);
        }

        return array();
    }
    
    public static function validateSettingList( $settingList )
    {
        parent::validateSettingList($settingList);

        if ( $settingList["list"] == "by_role" && empty($settingList["roles"]) )
        {
            throw new WidgetSettingValidateException(OW::getLanguage()->text("ucarousel", "widget_settings_list_roles_error"), 'roles');
        }
        
        if ( $settingList["list"] == "by_account_type" && empty($settingList["account_types"]) )
        {
            throw new WidgetSettingValidateException(OW::getLanguage()->text("ucarousel", "widget_settings_list_roles_account_types"), 'account_types');
        }
    }

    public static function renderListTypeSelect($uniqName, $fieldName, $value)
    {
        $language = OW::getLanguage();
        $field = new Selectbox($fieldName);
        $uniqId = uniqid("select-list-");
        $field->setId($uniqId);
        $field->setOptions(array(
            'latest' => $language->text('ucarousel', 'widget_list_type_latest'),
            'recently' => $language->text('ucarousel', 'widget_list_type_recently'),
            'online' => $language->text('ucarousel', 'widget_list_type_online'),
            'featured' => $language->text('ucarousel', 'widget_list_type_featured'),
            'by_role' => $language->text('ucarousel', 'widget_list_type_by_role'),
            'by_account_type' => $language->text('ucarousel', 'widget_list_type_by_account_type')
        ));
        
        $field->setValue($value);
        
        if ($value != "by_role")
        {
            OW::getDocument()->addOnloadScript('$("#uc-role-setting").parents("tr:eq(0)").hide();');
        }
        
        if ($value != "by_account_type")
        {
            OW::getDocument()->addOnloadScript('$("#uc-account-type-setting").parents("tr:eq(0)").hide();');
        }
        
        OW::getDocument()->addOnloadScript('$("#' . $uniqId . '").change(function() { '
                . '$("#uc-role-setting").parents("tr:eq(0)").hide(); '
                . '$("#uc-account-type-setting").parents("tr:eq(0)").hide(); '
                . 'if ($(this).val() == "by_role") $("#uc-role-setting").parents("tr:eq(0)").show(); '
                . 'if ($(this).val() == "by_account_type") $("#uc-account-type-setting").parents("tr:eq(0)").show(); '
                . ' })');
        
        return $field->renderInput();
    }
    
    public static function renderRoleList($uniqName, $fieldName, $value)
    {
        $language = OW::getLanguage();
        $roleList = BOL_AuthorizationService::getInstance()->findNonGuestRoleList();
        $roleOptions = array();
        foreach ( $roleList as $role )
        {
            $roleOptions[$role->id] = $language->text("base", "authorization_role_" . $role->name);
        }
        
        $uniqId = "uc-role-setting";
       
        return '<div id="' . $uniqId . '">' . self::renderMultyCheck($fieldName, $roleOptions, empty($value) ? array() : $value) . '</div>';
    }
    
    public static function renderAccountTypeList($uniqName, $fieldName, $value)
    {
        $accountTypes = BOL_QuestionService::getInstance()->findAllAccountTypesWithLabels();
        $uniqId = "uc-account-type-setting";
     
        return '<div id="' . $uniqId . '">' . self::renderMultyCheck($fieldName, $accountTypes, empty($value) ? array() : $value) . '</div>';
    }
    
    private static function renderMultyCheck( $name, $options, $checked )
    {
        $out = array();
        
        foreach ($options as $value => $label)
        {
            $checkedAttr = in_array($value, $checked) ? 'checked="checked"' : "";
            $out[] = '<input type="checkbox" ' . $checkedAttr . ' class="ow_vertical_middle" value="' . $value . '" name="' .$name. '[]" />' . $label;
        }
        
        return implode("", $out);
    }
    
    public static function getSettingList()
    {
        $language = OW::getLanguage();

        $settingList = array();

        $settingList['list'] = array(
            'presentation' => self::PRESENTATION_CUSTOM,
            'render' => array(__CLASS__, "renderListTypeSelect"),
            'label' => $language->text('ucarousel', 'widget_list_type'),
            "value" => "latest"
        );
        
        $settingList['roles'] = array(
            'presentation' => self::PRESENTATION_CUSTOM,
            'render' => array(__CLASS__, "renderRoleList"),
            'label' => $language->text('ucarousel', 'widget_list_setting_role')
        );
        
        $settingList['account_types'] = array(
            'presentation' => self::PRESENTATION_CUSTOM,
            'render' => array(__CLASS__, "renderAccountTypeList"),
            'label' => $language->text('ucarousel', 'widget_list_setting_account_type')
        );

        $settingList['count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => $language->text('ucarousel', 'widget_user_count'),
	    'optionList' => array(
                5 => 5,
                10 => 10,
                15 => 15,
                20 => 20,
                25 => 25
            ),
            'value' => 15
        );

        $settingList['size'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => $language->text('ucarousel', 'widget_image_size'),
	    'optionList' => array(
                'small' => $language->text('ucarousel', 'widget_image_size_small'),
                'medium' => $language->text('ucarousel', 'widget_image_size_medium'),
                'big' => $language->text('ucarousel', 'widget_image_size_big')
            ),
            'value' => 'big'
        );

        $settingList['shape'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => $language->text('ucarousel', 'widget_shape'),
            'optionList' => array(
                'lazySusan' => $language->text('ucarousel', 'widget_shape_lazy_susan'),
                'waterWheel' => $language->text('ucarousel', 'widget_shape_water_wheel'),
                'figure8' => $language->text('ucarousel', 'widget_shape_figure8'),
                'square' => $language->text('ucarousel', 'widget_shape_square'),
                'conveyorBeltLeft' => $language->text('ucarousel', 'widget_shape_conveyor_belt_left'),
                'conveyorBeltRight' => $language->text('ucarousel', 'widget_shape_conveyor_belt_right'),
                'diagonalRingLeft' => $language->text('ucarousel', 'widget_shape_diagonal_ring_left'),
                'diagonalRingRight' => $language->text('ucarousel', 'widget_shape_diagonal_ring_right'),
                'rollerCoaster' => $language->text('ucarousel', 'widget_shape_roller_coaster'),
                'tearDrop' => $language->text('ucarousel', 'widget_shape_tear_drop')
            ),
            'value' => 'lazySusan'
        );

        $settingList['infoLayout'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => $language->text('ucarousel', 'widget_info_layout'),
            'optionList' => array(
                '1' => $language->text('ucarousel', 'widget_info_layout_1'),//'Name + Gender + Age',
                '2' => $language->text('ucarousel', 'widget_info_layout_2'), //'Name + Gender',
                '3' => $language->text('ucarousel', 'widget_info_layout_3'), //'Name + Age',
                '4' => $language->text('ucarousel', 'widget_info_layout_4') //'Name Only'
            ),
            'value' => '1'
        );

        $settingList['autoplay'] = array(
            'presentation' => self::PRESENTATION_CHECKBOX,
            'label' => $language->text('ucarousel', 'widget_autoplay'),
            'value' => true
        );

        $settingList['speed'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => $language->text('ucarousel', 'widget_speed'),
            'optionList' => array(
                '1000' => $language->text('ucarousel', 'widget_speed_fast'),
                '3000' => $language->text('ucarousel', 'widget_speed_avg'),
                '5000' => $language->text('ucarousel', 'widget_speed_slow')
            ),
            'value' => '3000'
        );


        $settingList['dragging'] = array(
            'presentation' => self::PRESENTATION_CHECKBOX,
            'label' => $language->text('ucarousel', 'widget_dragging'),
            'value' => true
        );

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        $settings = BOL_ComponentAdminService::getInstance()->findSettingList(self::UNIQ_NAME);
        $list = empty($settings['list']) ? 'latest' : $settings['list'];
        $title = OW::getLanguage()->text('ucarousel', 'widget_list_type_' . $list);

        return array(
            self::SETTING_TITLE => $title, //OW::getLanguage()->text('ucarousel', 'widget_title'),
            self::SETTING_ICON => self::ICON_USER,
            self::SETTING_SHOW_TITLE => false
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }
}