<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/contact_importer/views/components/widget.html" */ ?>
<?php /*%%SmartyHeaderCode:2812844215349e25491c691-09679132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4ddc7ddb21ab84842ba6828cc2387a18fe367f79' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/contact_importer/views/components/widget.html',
      1 => 1389175670,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2812844215349e25491c691-09679132',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'btns' => 0,
    'b' => 0,
    'eicmp' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2549678b3_46536724',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2549678b3_46536724')) {function content_5349e2549678b3_46536724($_smarty_tpl) {?><?php if (!is_callable('smarty_block_style')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.style.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('style', array()); $_block_repeat=true; echo smarty_block_style(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>


.contactimporter_provider_button
{
	margin: 3px;
        display: inline-block;
	cursor: pointer;
}

.contactimporter_provider_button img
{
    width: 45px;
}

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_style(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>


<center>
    <?php  $_smarty_tpl->tpl_vars["b"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["b"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['btns']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["b"]->key => $_smarty_tpl->tpl_vars["b"]->value){
$_smarty_tpl->tpl_vars["b"]->_loop = true;
?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['b']->value['href'];?>
" onclick="<?php echo $_smarty_tpl->tpl_vars['b']->value['onclick'];?>
" class="<?php echo $_smarty_tpl->tpl_vars['b']->value['class'];?>
 contactimporter_provider_button">
            <img src="<?php echo $_smarty_tpl->tpl_vars['b']->value['iconUrl'];?>
" />
        </a>
    <?php } ?>
</center>
<div style="display:none;">
<div class="contactimporter_email_invite_cont">
<?php echo $_smarty_tpl->tpl_vars['eicmp']->value;?>

</div>
</div><?php }} ?>