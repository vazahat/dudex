<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachment_image.html" */ ?>
<?php /*%%SmartyHeaderCode:433298905349e2545992f7-73126604%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85444e28ae35a4f2aeebae38eed7533487474990' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachment_image.html',
      1 => 1397334782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '433298905349e2545992f7-73126604',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'langs' => 0,
    'photoActive' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254611d61_88784492',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254611d61_88784492')) {function content_5349e254611d61_88784492($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?><div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">

    <div class="att-body ATT_PanelBody">

        <div class="ai-upload AI_UploadPanel AI_View">

            <div class="ai-browse-btn-c ow_smallmargin">
                <div class="att-legend ow_border">
                    <?php echo smarty_function_text(array('key'=>"equestions+attachments_upload_image_inv"),$_smarty_tpl);?>

                </div>

                <div class="ai-browse-btn">
                    <div class="att-fake-file">
                        <?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_add IA_UploadButton",'label'=>$_smarty_tpl->tpl_vars['langs']->value['chooseImage']),$_smarty_tpl);?>

                        <input type="file" name="file" class="AI_UploadInput att-fake-file-input" size="1" />
                    </div>
                </div>
            </div>

            <div class="ai-take-btn-c">
                <?php $_smarty_tpl->_capture_stack[0][] = array("takeBtn", null, null); ob_start(); ?><a class="AI_SwitchToTakeButton" href="#"><?php echo smarty_function_text(array('key'=>"equestions+attachments_take_photo_button"),$_smarty_tpl);?>
</a><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
                <?php echo smarty_function_text(array('key'=>"equestions+attachments_take_photo_inv",'takeButton'=>Smarty::$_smarty_vars['capture']['takeBtn']),$_smarty_tpl);?>

            </div>

        </div>

        <div class="ai-result AI_Result AI_View" style="display: none;"></div>

        <div class="ai-take AI_TakePanel AI_View" style="display: none;">

            <div class="ai-take-screen-c">
                <div class="ai-take-screen ow_border AI_TakeScreen"></div>
                <div class="ai-take-screen-controls-c AI_TakePhotoControls" style="display: none;">
                    <div class="ai-take-screen-controls">
                        <a class="ai-take-screen-shoot AI_TakePhotoBtn" title="<?php echo smarty_function_text(array('key'=>"equestions+attachments_take_photo_button_title"),$_smarty_tpl);?>
" href="javascript://"></a>
                        <a class="ai-take-screen-reset AI_ResetPhotoBtn" title="<?php echo smarty_function_text(array('key'=>"equestions+attachments_delete_photo_button_title"),$_smarty_tpl);?>
" href="javascript://" style="display: none;"></a>
                    </div>
                </div>
            </div>

        </div>

        <div class="ai-my  AI_MyPanel AI_View" style="display: none;">
            <div class="aim-list clearfix AI_MyPanelList">

            </div>
            <a style="display: none;" href="javascript://" class="AI_MyPanelListViewMore aim-view-more ow_border">
                <span class="ow_icon_control aim-more-label AI_MyPanelListViewMoreLabel">
                    <?php echo smarty_function_text(array('key'=>"equestions+attachments_select_my_photo_view_more"),$_smarty_tpl);?>

                </span>
            </a>
        </div>

    </div>

    <div class="att-bottom ow_border clearfix ATT_PanelControls">

        <?php if ($_smarty_tpl->tpl_vars['photoActive']->value){?>
            <div class="att-switch ow_left">
                <a class="att-switch-btn ow_icon_control ow_ic_picture AI_SwitchToMyPhotos" href="javascript://"><?php echo smarty_function_text(array('key'=>"equestions+attachments_select_my_photo"),$_smarty_tpl);?>
</a>
            </div>
        <?php }?>

        <div class="att-controls ow_right">
            <span class="AI_MySave AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_save",'label'=>$_smarty_tpl->tpl_vars['langs']->value['chooseMy']),$_smarty_tpl);?>
</span>

            <span class="AI_UploadSave AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_save",'label'=>$_smarty_tpl->tpl_vars['langs']->value['uploadSave']),$_smarty_tpl);?>
</span>
            <span class="AI_TakeSave AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_save",'label'=>$_smarty_tpl->tpl_vars['langs']->value['takeSave']),$_smarty_tpl);?>
</span>
            <span class="AI_Cancel AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['cancel']),$_smarty_tpl);?>
</span>
            <span class="AI_Close AI_MainControl"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['close'],'class'=>"ow_ic_delete"),$_smarty_tpl);?>
</span>
        </div>

    </div>

    <div class="ATT_PanelTitle">
        <?php echo smarty_function_text(array('key'=>"equestions+attachments_image_title"),$_smarty_tpl);?>

    </div>

</div>
<?php }} ?>