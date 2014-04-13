<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:17
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/vwvideoconference/views/components/friend_vwvc_online_widget.html" */ ?>
<?php /*%%SmartyHeaderCode:8807191155349e2551e6971-58090306%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '21f377a033dad8acc5ca44037141def4b584291b' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/vwvideoconference/views/components/friend_vwvc_online_widget.html',
      1 => 1397338418,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8807191155349e2551e6971-58090306',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2551fef28_91914363',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2551fef28_91914363')) {function content_5349e2551fef28_91914363($_smarty_tpl) {?><div class="ow_custom_html_widget">
	<?php if ($_smarty_tpl->tpl_vars['content']->value){?>
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	<?php }else{ ?>
No friend online in Video conference room.
	<?php }?>
</div><?php }} ?>