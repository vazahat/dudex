<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachments.html" */ ?>
<?php /*%%SmartyHeaderCode:19829010885349e25468c720-31615590%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '84b650ab22461edfadc864a7c76a1a08524d98b8' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachments.html',
      1 => 1397334782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19829010885349e25468c720-31615590',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'image' => 0,
    'video' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2546c5289_37233623',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2546c5289_37233623')) {function content_5349e2546c5289_37233623($_smarty_tpl) {?><?php if (!is_callable('smarty_block_block_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.block_decorator.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
?><div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">
<div  class="att-attachments-c">

    <div class="att-attachments ATT_Result" style="display: none;" >
        <?php $_smarty_tpl->smarty->_tag_stack[] = array('block_decorator', array('name'=>'tooltip','addClass'=>'att-equestions-tooltip')); $_block_repeat=true; echo smarty_block_block_decorator(array('name'=>'tooltip','addClass'=>'att-equestions-tooltip'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

            <a href="javascript://" title="<?php echo smarty_function_text(array('key'=>"equestions+attachments_delete_title"),$_smarty_tpl);?>
" class="att-close ow_miniic_delete ATT_BodyClose"></a>
            <div class="ATT_Prloader att-preloader ow_preloader_content" style="display: none;"></div>

            <div class="att-preview ATT_ResultContent"></div>
        <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_block_decorator(array('name'=>'tooltip','addClass'=>'att-equestions-tooltip'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

    </div>

    <div style="display: none;" class="ATT_Panels">

        <?php if (isset($_smarty_tpl->tpl_vars['image']->value)){?>
        <div class="ATT_ImagePanel">
           <?php echo $_smarty_tpl->tpl_vars['image']->value;?>

        </div>
        <?php }?>

        <?php if (isset($_smarty_tpl->tpl_vars['video']->value)){?>
        <div class="ATT_VideoPanel">
            <?php echo $_smarty_tpl->tpl_vars['video']->value;?>

        </div>
        <?php }?>

        <?php if (isset($_smarty_tpl->tpl_vars['link']->value)){?>
        <div class="ATT_LinkPanel">
            <?php echo $_smarty_tpl->tpl_vars['link']->value;?>

        </div>
        <?php }?>

    </div>

</div>
</div>
<?php }} ?>