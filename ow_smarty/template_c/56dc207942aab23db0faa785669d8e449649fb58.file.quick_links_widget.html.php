<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/base/views/components/quick_links_widget.html" */ ?>
<?php /*%%SmartyHeaderCode:11500336445349e254c49f60-93589750%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '56dc207942aab23db0faa785669d8e449649fb58' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_system_plugins/base/views/components/quick_links_widget.html',
      1 => 1389175663,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11500336445349e254c49f60-93589750',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'data' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254cc02e9_77279953',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254cc02e9_77279953')) {function content_5349e254cc02e9_77279953($_smarty_tpl) {?><table class="ow_nomargin" width="100%">
	<tbody>
        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
		<tr>
			<td class="ow_label"><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['label'];?>
</a></td>
			<td class="ow_txtright"><?php if (!empty($_smarty_tpl->tpl_vars['item']->value['active_count'])){?><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['active_count_url'];?>
"><span class="ow_lbutton ow_green"><?php echo $_smarty_tpl->tpl_vars['item']->value['active_count'];?>
</span></a><?php }?></td>
			<td class="ow_txtright"><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['count_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['count'];?>
</a></td>
		</tr>
        <?php } ?>
	</tbody>
</table><?php }} ?>