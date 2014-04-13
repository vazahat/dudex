<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/newsfeed_status.html" */ ?>
<?php /*%%SmartyHeaderCode:1291029085349e25453ff20-15044268%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9bef5b552a60cb46cefd878814feb62167e1a5ab' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/newsfeed_status.html',
      1 => 1397334690,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1291029085349e25453ff20-15044268',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'attachments' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254582059_39057803',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254582059_39057803')) {function content_5349e254582059_39057803($_smarty_tpl) {?><?php if (!is_callable('smarty_block_style')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.style.php';
if (!is_callable('smarty_block_form')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.form.php';
if (!is_callable('smarty_function_input')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.input.php';
if (!is_callable('smarty_function_submit')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.submit.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('style', array()); $_block_repeat=true; echo smarty_block_style(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>


textarea.ow_newsfeed_status_input {
    height: 50px;
}

textarea.ow_newsfeed_status_input.invitation {
    height: 20px;
}

.newsfeed-attachment-preview {
    width: 95%;
}
.ow_side_preloader {
	float: right;
	padding: 0px 4px 0px 0px;
	margin-top: 6px;
}
.ow_side_preloader {
	display: inline-block;
	width: 16px;
	height: 16px;
	background-repeat: no-repeat;
}

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_style(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
" class="attp-newsfeed-status">
<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('name'=>"newsfeed_update_status")); $_block_repeat=true; echo smarty_block_form(array('name'=>"newsfeed_update_status"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<div class="form_auto_click">
		<div class="clearfix">
			<div class="newsfeed_update_status_picture">
			</div>
			<div class="newsfeed_update_status_info">
				<div class=""><?php echo smarty_function_input(array('name'=>"status",'class'=>"ow_newsfeed_status_input"),$_smarty_tpl);?>
</div>
			</div>
		</div>

		<div class="ow_submit_auto_click" style="text-align: left;">
                    <?php if (!empty($_smarty_tpl->tpl_vars['attachments']->value)){?>
                        <?php echo $_smarty_tpl->tpl_vars['attachments']->value;?>

                    <?php }?>

                    <div class="clearfix ow_status_update_btn_block attp-topmargin">
                            <span class="ow_attachment_btn"><?php echo smarty_function_submit(array('name'=>"save"),$_smarty_tpl);?>
</span>

                            <?php if (!empty($_smarty_tpl->tpl_vars['attachments']->value)){?>
                                <div class="attachments-controls ow_attachment_icons">
                                    <div class="ow_attachments">
                                        <span class="buttons clearfix">
                                            <a class="link EQ_AttachmentLink" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'attachments+attchments_link_button_label'),$_smarty_tpl);?>
"></a>
                                            <a class="image EQ_AttachmentPhoto" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'attachments+attchments_photo_button_label'),$_smarty_tpl);?>
"></a>
                                            <a class="video EQ_AttachmentVideo" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'attachments+attchments_video_button_label'),$_smarty_tpl);?>
"></a>
                                        </span>
                                    </div>
                                </div>
                            <?php }?>

                            <span class="ow_side_preloader_wrap"><span class="ow_side_preloader ow_inprogress newsfeed-status-preloader" style="display: none;"></span></span>
                    </div>
		</div>
	</div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('name'=>"newsfeed_update_status"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div><?php }} ?>