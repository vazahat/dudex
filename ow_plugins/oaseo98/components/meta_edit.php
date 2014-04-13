<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.components
 * @since 1.0
 */
class OASEO_CMP_MetaEdit extends OW_Component
{
    /**
     * @var OASEO_BOL_Service
     */
    private $metaService;

    /**
     * @return Constructor.
     */
    public function __construct( $metaData, $uri, $frontend = true )
    {
        parent::__construct();
        $this->metaService = OASEO_BOL_Service::getInstance();
        $language = OW::getLanguage();

        $uriArray = parse_url($uri);
		$uri = !empty($uriArray['path']) ? $uriArray['path'] : '';

        // need to get uri if url provided
        if ( substr($uri, 0, 7) == 'http://' )
        {
            $uri = substr($uri, strlen(OW_URL_HOME));
        }
        elseif ( UTIL_String::removeFirstAndLastSlashes(substr($uri, 0, ( strlen(OW_URL_HOME)) - 7)) == UTIL_String::removeFirstAndLastSlashes(substr(OW_URL_HOME, 7)) )
        {
            $uri = UTIL_String::removeFirstAndLastSlashes(substr($uri, (strlen(OW_URL_HOME)) - 7));
        }
        else
        {
            $uri = trim($uri);
        }

        $metaData['routeData'] = $this->metaService->getRouteData($uri);

        $dispatchAttrs = $this->metaService->getDispatchParamsForUri($uri);

        if ( $dispatchAttrs === false )
        {
            $this->assign('no_compile', true);
            return;
        }

        $entry = $this->metaService->getEntryForDispatchParams($dispatchAttrs);

        if ( $entry !== null )
        {
            $metaArr = json_decode($entry->getMeta(), true);

            if ( isset($metaArr['title']) )
            {
                $titleString = $metaArr['title'];
            }

            if ( isset($metaArr['keywords']) )
            {
                $keywords = $metaArr['keywords'];
            }

            if ( isset($metaArr['desc']) )
            {
                $descString = $metaArr['desc'];
            }
        }

        if ( !isset($titleString) )
        {
            $titleString = $metaData['title'];
        }

        if ( !isset($keywords) )
        {
            $keywords = explode(',', $metaData['keywords']);
            $keywords = array_map('trim', $keywords);
        }

        if ( !isset($descString) )
        {
            $descString = $metaData['desc'];
        }

        $form = new Form('meta_edit');
        $form->setAction(OW::getRouter()->urlFor('OASEO_CTRL_Base', 'updateMeta'));
        $form->setAjax();
        $form->setAjaxResetOnSuccess(false);
        $this->addForm($form);

        $title = new TextField('title');
        $title->setLabel($language->text('oaseo', 'meta_edit_form_title_label'));
        $title->setDescription($language->text('oaseo', 'meta_edit_form_title_fr_desc'));
        $title->setValue($titleString);
        $form->addElement($title);

        $keyword = new OA_CCLASS_TagsField('keywords');
        $keyword->setLabel($language->text('oaseo', 'meta_edit_form_keyword_label'));
        $keyword->setDescription($language->text('oaseo', 'meta_edit_form_keyword_fr_desc'));
        $keyword->setValue($keywords);
        $form->addElement($keyword);

        $desc = new Textarea('desc');
        $desc->setLabel($language->text('oaseo', 'meta_edit_form_desc_label'));
        $desc->setDescription($language->text('oaseo', 'meta_edit_form_desc_fr_desc'));
        $desc->setValue($descString);
        $form->addElement($desc);

        $hidTitle = new HiddenField('hidTitle');
        $hidTitle->setValue($titleString);
        $form->addElement($hidTitle);

        $hidKeyword = new HiddenField('hidKeywords');
        $hidKeyword->setValue(implode('+|+', $keywords));
        $form->addElement($hidKeyword);

        $hidDesc = new HiddenField('hidDesc');
        $hidDesc->setValue($descString);
        $form->addElement($hidDesc);
        
        if ( !empty($metaData['routeData']) && $uri && $dispatchAttrs['controller'] != 'BASE_CTRL_StaticDocument' )
        {
            $this->assign('urlAvail', true);

            $urlField = new OASEO_UrlField('url');
            $urlField->setValue($metaData['routeData']['path']);
            $urlField->setLabel($language->text('oaseo', 'meta_edit_form_url_label'));
            $urlField->setDescription($language->text('oaseo', 'meta_edit_form_url_desc'));
            $form->addElement($urlField);

            $routeName = new HiddenField('routeName');
            $routeName->setValue($metaData['routeData']['name']);
            $form->addElement($routeName);
        }

        $uriEl = new HiddenField('uri');
        $uriEl->setValue($uri);
        $form->addElement($uriEl);

        $submit = new Submit('submit');
        $submit->setValue($language->text('oaseo', 'meta_edit_form_submit_label'));
        $form->addElement($submit);

        $id = uniqid();
        $this->assign('id', $id);
        $this->assign('frontend', $frontend);

        $form->bindJsFunction('success', "function(data){if(data.status){OW.info(data.msg);window.oaseoFB.close();}else{OW.error(data.msg);}}");

        if ( $frontend )
        {
            OW::getDocument()->addOnloadScript("$('#aoseo_button_{$id}').click(
            function(){
                window.oaseoFB = new OA_FloatBox({
                \$title: '{$language->text('oaseo', 'meta_edit_form_cmp_title')}',
                \$contents: $('#oaseo_edit_form_{$id}'),
                width: 900,
                icon_class: 'ow_ic_gear'
            });
            }
        );");
        }
    }
}

class OASEO_UrlField extends FormElement
{

    public function getValue()
    {
        return parent::getValue();
    }

    public function renderInput( $params = null )
    {
        parent::renderInput($params);
        $output = '<div class="oa_urledit_field" id="' . $this->getId() . '"><span class="cst_nedit">' . OW_URL_HOME . '</span>';

        $pathArr = explode('/', $this->getValue());

        $js = "
            var oaCntedHandl = function(){ $('input', $(this).parent()).val($(this).html()); };
            $('.oa_urledit_field').click( function(){ $('.oa_contenteditable', this)[0].focus();} );
            $('.oa_urledit_field .oa_contenteditable').click(function(e){e.stopPropagation()}).focus(function(){ $(this).closest('.oa_urledit_field').css({backgroundColor:'#FBFCEB'});})
            .blur(function(){ $(this).closest('.oa_urledit_field').css({backgroundColor:'#fff'});})
                .keyup(oaCntedHandl).mouseup(oaCntedHandl);
            
         ";

        for ( $i = 0; $i < sizeof($pathArr); $i++ )
        {
            if ( strstr($pathArr[$i], ':') )
            {
                $output .= '<span class="cst_nedit">' . ( $i === 0 ? '' : '/' ) . ':var</span>';
            }
            else
            {
                $id = uniqid('oa_uf');
                $output .= ( $i === 0 ? '' : '<span class="cst_nedit">/</span>' ) . '<span><span class="oa_contenteditable" contenteditable="true">' . $pathArr[$i] . '</span><input class="oa_contenteditable_input" value="' . $pathArr[$i] . '" id="' . $id . '" type="hidden" name="' . $pathArr[$i] . '"></span>';
            }
        }

        OW::getDocument()->addOnloadScript($js);

        $output .= '</div>';

        return $output;
    }

    public function getElementJs()
    {
        $js = "var formElement = new OwFormElement('" . $this->getId() . "', '" . $this->getName() . "');";

        /** @var $value Validator  */
        foreach ( $this->validators as $value )
        {
            $js .= "formElement.addValidator(" . $value->getJsValidator() . ");";
        }

        $js .= "
            formElement.getValue = function(){
                var value = [];
                $.each(  $('input[class=oa_contenteditable_input]', $('#" . $this->getId() . "') ),
                    function(){
                        value.push($(this).val());
                    }
                );
                
                return JSON.stringify(value);
            };
		";

        return $js;
    }
}
