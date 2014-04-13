<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 17:57:09
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/admin/views/components/admin_menu.html" */ ?>
<?php /*%%SmartyHeaderCode:16921367015349e0e529b3c3-78113154%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '37b99f8dca95b20200336634f4c33f639c52e57f' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/admin/views/components/admin_menu.html',
      1 => 1389175658,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16921367015349e0e529b3c3-78113154',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'class' => 0,
    'data' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e0e52fbca6_06395016',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e0e52fbca6_06395016')) {function content_5349e0e52fbca6_06395016($_smarty_tpl) {?><span class="ow_tooltip_tail"><span></span></span>	
<ul class="<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
 clearfix ow_tooltip_body">
<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
	<li class="<?php echo $_smarty_tpl->tpl_vars['item']->value['class'];?>
<?php if (!empty($_smarty_tpl->tpl_vars['item']->value['active'])){?> active<?php }?>">
		<a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
"<?php if (!empty($_smarty_tpl->tpl_vars['item']->value['new_window'])){?> target="_blank"<?php }?>>
		   <span><?php echo $_smarty_tpl->tpl_vars['item']->value['label'];?>
</span>
		</a>
	</li>
<?php } ?>
</ul>
<?php }} ?>