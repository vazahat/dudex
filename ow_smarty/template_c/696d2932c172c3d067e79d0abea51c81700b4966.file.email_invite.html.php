<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/contact_importer/views/components/email_invite.html" */ ?>
<?php /*%%SmartyHeaderCode:17452681845349e2548d2a00-43057878%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '696d2932c172c3d067e79d0abea51c81700b4966' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/contact_importer/views/components/email_invite.html',
      1 => 1389175670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17452681845349e2548d2a00-43057878',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254913c91_37639437',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254913c91_37639437')) {function content_5349e254913c91_37639437($_smarty_tpl) {?><?php if (!is_callable('smarty_block_form')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.form.php';
if (!is_callable('smarty_function_input')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.input.php';
if (!is_callable('smarty_function_submit')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('name'=>'inite-friends')); $_block_repeat=true; echo smarty_block_form(array('name'=>'inite-friends'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

    <?php echo smarty_function_input(array('name'=>'emailList'),$_smarty_tpl);?>

    <div class="ow_smallmargin"><?php echo smarty_function_input(array('name'=>'text'),$_smarty_tpl);?>
</div>
    <div class="clearfix ow_smallmargin">
        <div class="ow_right"><?php echo smarty_function_submit(array('name'=>'submit'),$_smarty_tpl);?>
</div>
    </div>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('name'=>'inite-friends'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>