<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 17:57:09
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/base/views/components/switch_language.html" */ ?>
<?php /*%%SmartyHeaderCode:9898327245349e0e541a729-26124496%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3c0e6cf6cacc5e249b39eebdf554e63f348068b5' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/base/views/components/switch_language.html',
      1 => 1389175664,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9898327245349e0e541a729-26124496',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'languages' => 0,
    'language' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e0e546a911_79908148',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e0e546a911_79908148')) {function content_5349e0e546a911_79908148($_smarty_tpl) {?><ul class="ow_console_lang">
    <?php  $_smarty_tpl->tpl_vars["language"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["language"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["language"]->key => $_smarty_tpl->tpl_vars["language"]->value){
$_smarty_tpl->tpl_vars["language"]->_loop = true;
?>
        <li class="ow_console_lang_item" onclick="location.href='<?php echo $_smarty_tpl->tpl_vars['language']->value['url'];?>
';"><span class="<?php echo $_smarty_tpl->tpl_vars['language']->value['class'];?>
"><?php echo $_smarty_tpl->tpl_vars['language']->value['label'];?>
</span></li>
    <?php } ?>
</ul><?php }} ?>