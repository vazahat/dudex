<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 17:57:08
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/cacheextreme/views/controllers/admin_index.html" */ ?>
<?php /*%%SmartyHeaderCode:9082494265349e0e4f2d0b5-13914113%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1ce44065759f02b1b9d73fe522ffd7b763da7128' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/cacheextreme/views/controllers/admin_index.html',
      1 => 1397338316,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9082494265349e0e4f2d0b5-13914113',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menu' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e0e505d416_94689138',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e0e505d416_94689138')) {function content_5349e0e505d416_94689138($_smarty_tpl) {?><?php if (!is_callable('smarty_block_form')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.form.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_label')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.label.php';
if (!is_callable('smarty_function_input')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.input.php';
if (!is_callable('smarty_function_error')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.error.php';
if (!is_callable('smarty_function_submit')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.submit.php';
?><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>


<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('name'=>'cacheControlForm')); $_block_repeat=true; echo smarty_block_form(array('name'=>'cacheControlForm'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>


<table class="ow_table_1 ow_form ow_stdmargin">
    <tr class="ow_tr_first">
        <th class="ow_name ow_txtleft" colspan="3">
            <span class="ow_section_icon ow_ic_gear_wheel"><?php echo smarty_function_text(array('key'=>'cacheextreme+cache_settings'),$_smarty_tpl);?>
</span>
        </th>
    </tr>
    <tr class="ow_alt1">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'templateCache'),$_smarty_tpl);?>
</td>
        <td class="ow_value">
            <?php echo smarty_function_input(array('name'=>'templateCache','class'=>'ow_settings_input'),$_smarty_tpl);?>
 <?php echo smarty_function_error(array('name'=>'templateCache'),$_smarty_tpl);?>

        </td>
        <td class="ow_desc ow_small"><?php echo smarty_function_text(array('key'=>'cacheextreme+templateCache_setting_desc'),$_smarty_tpl);?>
</td>
    </tr>
    <tr class="ow_alt1">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'backendCache'),$_smarty_tpl);?>
</td>
        <td class="ow_value">
            <?php echo smarty_function_input(array('name'=>'backendCache','class'=>'ow_settings_input'),$_smarty_tpl);?>
 <?php echo smarty_function_error(array('name'=>'backendCache'),$_smarty_tpl);?>

        </td>
        <td class="ow_desc ow_small"><?php echo smarty_function_text(array('key'=>'cacheextreme+backendCache_setting_desc'),$_smarty_tpl);?>
</td>
    </tr>
    <tr class="ow_alt1">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'themeStatic'),$_smarty_tpl);?>
</td>
        <td class="ow_value">
            <?php echo smarty_function_input(array('name'=>'themeStatic','class'=>'ow_settings_input'),$_smarty_tpl);?>
 <?php echo smarty_function_error(array('name'=>'themeStatic'),$_smarty_tpl);?>

        </td>
        <td class="ow_desc ow_small"><?php echo smarty_function_text(array('key'=>'cacheextreme+themeStatic_setting_desc'),$_smarty_tpl);?>
</td>
    </tr>
    <tr class="ow_alt1">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'pluginStatic'),$_smarty_tpl);?>
</td>
        <td class="ow_value">
            <?php echo smarty_function_input(array('name'=>'pluginStatic','class'=>'ow_settings_input'),$_smarty_tpl);?>
 <?php echo smarty_function_error(array('name'=>'pluginStatic'),$_smarty_tpl);?>

        </td>
        <td class="ow_desc ow_small"><?php echo smarty_function_text(array('key'=>'cacheextreme+pluginStatic_setting_desc'),$_smarty_tpl);?>
</td>
    </tr>    
</table>
<div class="clearfix ow_stdmargin"><div class="ow_center"><?php echo smarty_function_submit(array('name'=>'clean','class'=>'ow_ic_trash ow_positive'),$_smarty_tpl);?>
</div></div>



<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('name'=>'cacheControlForm'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>