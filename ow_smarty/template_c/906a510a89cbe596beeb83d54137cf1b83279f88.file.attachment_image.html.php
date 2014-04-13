<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/attachment_image.html" */ ?>
<?php /*%%SmartyHeaderCode:19041214785349e2543c8ac5-60588559%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '906a510a89cbe596beeb83d54137cf1b83279f88' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/attachment_image.html',
      1 => 1397334690,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19041214785349e2543c8ac5-60588559',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'langs' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254423f09_52044617',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254423f09_52044617')) {function content_5349e254423f09_52044617($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?><div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">

    <div class="att-body ATT_PanelBody">

        <div class="ai-upload AI_UploadPanel AI_View">

            <div class="ai-browse-btn-c ow_smallmargin">
                <div class="att-legend ow_border">
                    <?php echo smarty_function_text(array('key'=>"attachments+attachments_upload_image_inv"),$_smarty_tpl);?>

                </div>

                <div class="ai-browse-btn">
                    <div class="att-fake-file">
                        <?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_add IA_UploadButton",'label'=>$_smarty_tpl->tpl_vars['langs']->value['chooseImage']),$_smarty_tpl);?>

                        <input type="file" name="file" class="AI_UploadInput att-fake-file-input" size="1" />
                    </div>
                </div>
            </div>

            <div class="ai-take-btn-c">
                <?php $_smarty_tpl->_capture_stack[0][] = array("takeBtn", null, null); ob_start(); ?><a class="AI_SwitchToTakeButton" href="#"><?php echo smarty_function_text(array('key'=>"attachments+attachments_take_photo_button"),$_smarty_tpl);?>
</a><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
                <?php echo smarty_function_text(array('key'=>"attachments+attachments_take_photo_inv",'takeButton'=>Smarty::$_smarty_vars['capture']['takeBtn']),$_smarty_tpl);?>

            </div>

        </div>

        <div class="ai-result AI_Result AI_View" style="display: none;"></div>

        <div class="ai-take AI_TakePanel AI_View" style="display: none;">

            <div class="ai-take-screen-c">
                <div class="ai-take-screen ow_border AI_TakeScreen"></div>
                <div class="ai-take-screen-controls-c AI_TakePhotoControls" style="display: none;">
                    <div class="ai-take-screen-controls">
                        <a class="ai-take-screen-shoot AI_TakePhotoBtn" title="<?php echo smarty_function_text(array('key'=>"attachments+attachments_take_photo_button_title"),$_smarty_tpl);?>
" href="javascript://"></a>
                        <a class="ai-take-screen-reset AI_ResetPhotoBtn" title="<?php echo smarty_function_text(array('key'=>"attachments+attachments_delete_photo_button_title"),$_smarty_tpl);?>
" href="javascript://" style="display: none;"></a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="att-bottom ow_border ATT_PanelControls">

        <div class="att-controls">
            <span class="AI_UploadSave AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['uploadSave']),$_smarty_tpl);?>
</span>
            <span class="AI_TakeSave AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['takeSave']),$_smarty_tpl);?>
</span>
            <span class="AI_Cancel AI_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['cancel']),$_smarty_tpl);?>
</span>
            <span class="AI_Close AI_MainControl"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['close'],'class'=>"ow_ic_delete"),$_smarty_tpl);?>
</span>
        </div>

    </div>

    <div class="ATT_PanelTitle">
        <?php echo smarty_function_text(array('key'=>"attachments+attachments_image_title"),$_smarty_tpl);?>

    </div>

</div>
<?php }} ?>