<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:15
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/newsfeed/views/components/feed_list.html" */ ?>
<?php /*%%SmartyHeaderCode:9885400685349e253d43c49-67455644%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32415826439427d5102167eead6cc792d4bd3f0c' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/newsfeed/views/components/feed_list.html',
      1 => 1397335736,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9885400685349e253d43c49-67455644',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'feed' => 0,
    'item' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e253d680e4_21991505',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e253d680e4_21991505')) {function content_5349e253d680e4_21991505($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
?><li <?php if (count($_smarty_tpl->tpl_vars['feed']->value)){?>style="display: none;"<?php }?> class="ow_newsfeed_item ow_nocontent newsfeed_nocontent"><?php echo smarty_function_text(array('key'=>"newsfeed+empty_feed_message"),$_smarty_tpl);?>
</li>

<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['feed']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['item']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['item']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value){
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['item']->iteration++;
 $_smarty_tpl->tpl_vars['item']->last = $_smarty_tpl->tpl_vars['item']->iteration === $_smarty_tpl->tpl_vars['item']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['feed']['last'] = $_smarty_tpl->tpl_vars['item']->last;
?>
    <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['newsfeed_item'][0][0]->tplRenderItem(array('action'=>$_smarty_tpl->tpl_vars['item']->value,'lastItem'=>$_smarty_tpl->getVariable('smarty')->value['foreach']['feed']['last']),$_smarty_tpl);?>

<?php } ?>
<?php }} ?>