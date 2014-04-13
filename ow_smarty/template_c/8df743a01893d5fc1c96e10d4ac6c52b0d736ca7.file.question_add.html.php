<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/question_add.html" */ ?>
<?php /*%%SmartyHeaderCode:4774792435349e2546cb478-10347987%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8df743a01893d5fc1c96e10d4ac6c52b0d736ca7' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/question_add.html',
      1 => 1397334782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4774792435349e2546cb478-10347987',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'configs' => 0,
    'attachments' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2547321d5_66189812',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2547321d5_66189812')) {function content_5349e2547321d5_66189812($_smarty_tpl) {?><?php if (!is_callable('smarty_block_form')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.form.php';
if (!is_callable('smarty_function_input')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.input.php';
if (!is_callable('smarty_block_block_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.block_decorator.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_label')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.label.php';
if (!is_callable('smarty_function_submit')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.submit.php';
?><div class="questions-add clearfix" id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('name'=>"questions_add")); $_block_repeat=true; echo smarty_block_form(array('name'=>"questions_add"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <div class="form_auto_click">
        <div class="questions-add-question">
            <?php echo smarty_function_input(array('name'=>"question",'class'=>"questions-input"),$_smarty_tpl);?>

        </div>
        <div class="ow_submit_auto_click" style="display: none;">

            <?php if ($_smarty_tpl->tpl_vars['configs']->value['attachments']){?>
            <div class="eq-attachments">
                <?php echo $_smarty_tpl->tpl_vars['attachments']->value;?>

            </div>
            <?php }?>

            <div class="questions-add-answers" style="display: none;">
                <?php $_smarty_tpl->smarty->_tag_stack[] = array('block_decorator', array('name'=>'tooltip','addClass'=>'qaa-tooltip ow_small ')); $_block_repeat=true; echo smarty_block_block_decorator(array('name'=>'tooltip','addClass'=>'qaa-tooltip ow_small '), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                    <div class="eq-add-answers-field">
                        <div class="ow_smallmargin">
                            <div class="qaa-label-c">
                                <span class="qaa-label"><strong><?php echo smarty_function_text(array('key'=>"equestions+question_add_answers_label"),$_smarty_tpl);?>
</strong></span>
                            </div>
                        </div>


                        <?php echo smarty_function_input(array('name'=>"answers"),$_smarty_tpl);?>

                    </div>
                <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_block_decorator(array('name'=>'tooltip','addClass'=>'qaa-tooltip ow_small '), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

            </div>



            <div class="clearfix qa-submit-c ow_border">
                <div class="ow_left questions-add-answers-btn-c">
                    <a href="javascript://" class="questions-add-answers-btn"><?php echo smarty_function_text(array('key'=>"equestions+question_add_show_options_btn"),$_smarty_tpl);?>
</a>
                    <div class="questions-add-answers-options" style="display: none;">
                        <?php echo smarty_function_input(array('name'=>"allowAddOprions"),$_smarty_tpl);?>
<?php echo smarty_function_label(array('name'=>"allowAddOprions"),$_smarty_tpl);?>

                    </div>
                </div>

                <div class="ow_right eq-save-c">
                    <span class="ow_attachment_btn"><?php echo smarty_function_submit(array('name'=>"save"),$_smarty_tpl);?>
</span>
                </div>

                <?php if ($_smarty_tpl->tpl_vars['configs']->value['attachments']){?>
                <div class="eq-input-controls ow_attachment_icons">
                    <div class="ow_attachments">
                        <span class="buttons clearfix">
                            <?php if ($_smarty_tpl->tpl_vars['configs']->value['attachments_link']){?>
                                <a class="link EQ_AttachmentLink" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'equestions+attchments_link_button_label'),$_smarty_tpl);?>
"></a>
                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['configs']->value['attachments_image']){?>
                                <a class="image EQ_AttachmentPhoto" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'equestions+attchments_photo_button_label'),$_smarty_tpl);?>
"></a>
                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['configs']->value['attachments_video']){?>
                                <a class="video EQ_AttachmentVideo" href="javascript://" title="<?php echo smarty_function_text(array('key'=>'equestions+attchments_video_button_label'),$_smarty_tpl);?>
"></a>
                            <?php }?>
                        </span>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('name'=>"questions_add"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div><?php }} ?>