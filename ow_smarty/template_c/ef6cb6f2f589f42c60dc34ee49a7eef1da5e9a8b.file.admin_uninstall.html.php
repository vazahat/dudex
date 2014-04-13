<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:19:52
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/advancedphoto/views/controllers/admin_uninstall.html" */ ?>
<?php /*%%SmartyHeaderCode:5471925755349e6383533c8-41163959%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ef6cb6f2f589f42c60dc34ee49a7eef1da5e9a8b' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/advancedphoto/views/controllers/admin_uninstall.html',
      1 => 1397350319,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5471925755349e6383533c8-41163959',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'inprogress' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e6383caae3_75650947',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e6383caae3_75650947')) {function content_5349e6383caae3_75650947($_smarty_tpl) {?><?php if (!is_callable('smarty_block_block_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.block_decorator.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?>
<div class="ow_automargin ow_wide">
<?php if ($_smarty_tpl->tpl_vars['inprogress']->value){?>
    <?php $_smarty_tpl->smarty->_tag_stack[] = array('block_decorator', array('name'=>'box','iconClass'=>'ow_ic_clock','langLabel'=>'photo+uninstall_inprogress')); $_block_repeat=true; echo smarty_block_block_decorator(array('name'=>'box','iconClass'=>'ow_ic_clock','langLabel'=>'photo+uninstall_inprogress'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

        <?php echo smarty_function_text(array('key'=>'photo+uninstall_inprogress_desc'),$_smarty_tpl);?>

    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_block_decorator(array('name'=>'box','iconClass'=>'ow_ic_clock','langLabel'=>'photo+uninstall_inprogress'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }else{ ?>
	<?php $_smarty_tpl->smarty->_tag_stack[] = array('block_decorator', array('name'=>'box','iconClass'=>'ow_ic_warning','langLabel'=>'photo+delete_content_warning')); $_block_repeat=true; echo smarty_block_block_decorator(array('name'=>'box','iconClass'=>'ow_ic_warning','langLabel'=>'photo+delete_content_warning'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

	<form id="form_delete" method="post">
	    <input type="hidden" name="action" value="delete_content" />
	    <div class="ow_smallmargin">Are you sure you want to uninstall 'Advanced Photo' plugin?</div>
	    <div class="ow_txtright">
	    <?php echo smarty_function_decorator(array('name'=>'button','type'=>'submit','id'=>'btn-delete-content','label'=>'Yes','class'=>'ow_ic_delete'),$_smarty_tpl);?>

	    </div>
	</form>
	<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_block_decorator(array('name'=>'box','iconClass'=>'ow_ic_warning','langLabel'=>'photo+delete_content_warning'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php }?>
</div><?php }} ?>