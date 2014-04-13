<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:17
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/vwlivestreaming/views/components/friend_vwls_online_widget.html" */ ?>
<?php /*%%SmartyHeaderCode:6989559805349e255227828-57762497%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '636e1cdd55d7c291e7b12a9e4a906b51f2afc660' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/vwlivestreaming/views/components/friend_vwls_online_widget.html',
      1 => 1397338403,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6989559805349e255227828-57762497',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e25523f462_10640839',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e25523f462_10640839')) {function content_5349e25523f462_10640839($_smarty_tpl) {?><div class="ow_custom_html_widget">
		<?php if ($_smarty_tpl->tpl_vars['content']->value){?>
		<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

	<?php }else{ ?>
No friend online in Live streaming room.
	<?php }?>
</div><?php }} ?>