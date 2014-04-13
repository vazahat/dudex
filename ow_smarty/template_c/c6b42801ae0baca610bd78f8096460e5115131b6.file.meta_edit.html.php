<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:17
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/oaseo98/views/components/meta_edit.html" */ ?>
<?php /*%%SmartyHeaderCode:13512095195349e255387d29-02416738%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c6b42801ae0baca610bd78f8096460e5115131b6' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/oaseo98/views/components/meta_edit.html',
      1 => 1397335446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13512095195349e255387d29-02416738',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'no_compile' => 0,
    'frontend' => 0,
    'id' => 0,
    'urlAvail' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e25544d650_43070957',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e25544d650_43070957')) {function content_5349e25544d650_43070957($_smarty_tpl) {?><?php if (!is_callable('smarty_block_style')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.style.php';
if (!is_callable('smarty_block_form')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/block.form.php';
if (!is_callable('smarty_function_cycle')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_libraries/smarty3/plugins/function.cycle.php';
if (!is_callable('smarty_function_label')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.label.php';
if (!is_callable('smarty_function_input')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.input.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_submit')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.submit.php';
?><?php $_smarty_tpl->smarty->_tag_stack[] = array('style', array()); $_block_repeat=true; echo smarty_block_style(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

.oa_form input[type=text],
.oa_form textarea{
    border: 1px solid #ced1da !important;
	border-top: 1px solid #abadb3 !important;
	border-left:1px solid #abadb3 !important;
    color: #333 !important;
    font-family: "Lucida Grande", "Verdana" !important;
    font-size: 13px !important;
    padding: 4px !important;
    background:#fff !important;
    border-radius:0 !important;
    -moz-border-radius:0 !important;
    -webkit-border-radius:0 !important;

}

.oa_form input[type=text]:focus,
.oa_form textarea:focus,
.oa_form ul.oa_tags_field.focused{
	border: 1px solid #b5cfe7 !important;
	border-top: 1px solid #3d7bad !important;
	border-left:1px solid #3d7bad !important;
	background: #fbfceb !important;
}

.oa_form ul.oa_tags_field input[type=text]{
    background:none !important;
    border:none !important;
}

.oa_form ul.oa_tags_field, .oa_form textarea{
    height:160px !important;
    overflow-y:scroll !important;
}

.cst_meta_edit textarea,
.cst_meta_edit input[type=text]{
    width:729px !important;
}

.cst_meta_edit ul.oa_tags_field input[type=text]{
    width:170px !important;
}

.oa_form .ow_alt1, .oa_form tr.ow_alt1 td{
    background-color: #e0e3eb !important;
}

.oa_form .ow_alt2, .oa_form tr.ow_alt2 td{
    background-color: #eef0f5 !important;
}

.cst_meta_edit table.ow_form td.ow_label{
    width:80px !important;
}

.cst_meta_edit table.ow_form td.ow_value{
    
}

.cst_meta_edit table.ow_form td.ow_desc{
    padding-left:0 !important;
    width:115px !important;
}

.oaseo_pos_button{
    position:fixed !important;
    z-index:100000000000000000 !important;
    top:45% !important;
    right:0 !important;
    cursor: pointer !important;
    box-shadow:0 0 10px #555555 !important;
    background: #292d3a url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACe0lEQVR4nI3PS08TARQF4NPOjBRNS2OBoBjCw8L0KYatINAoGxNpjI8N0YULtrIyLiDUGF7+C0xMTEoT05CgGxeaYJGVBgtBAwN0ZrDt0GnpTOfpQqwiVL3JXX7nnmvBf8zkRPScpnELmi54AYCwOhiKrH/w8NGdOcu/cGR89kZOXHne2Rmgurouwm63g+N5vH71xrCi8eZfA55Ox0Li/urCwNXLRF19HRiGgcPhAEmS4Dgey0urDFkJz0zNhapPavFAsJfw+TzQDR08z2P9y1dQJAlVVaEbYtOxATNTc6EqmxJvbWu2eTxtYBgBgAQrQYAgCPC7uwAAgiRw5IXpyWjoRFUp3t7RbPP73MjlJKTTIlj2GyxEDi0tjVBVFYuLCZQkaoX8E5OUFHe7D2OOy2B7iwPLpeCwn0J1NQU2tavX13UNlBtMTURDBFGI055WWzBIH8JbTAosx8IwZAgCC0kWjIB/4Nbo2FD0VwNr/kXDmQab399REWcFFqWSpHg9V8KjY0PzAEACwMST2aGaGqv9bGMNCgUZ2WzhCM5kUpBLkuL19oTHI3fny3cB4P1StP+8u4EqSntgttYhinmkdrgyTmd2IMlFxevpDkd+w+UGNE0Ptrvbkc5ksPxhGWJehZjHD5zeRlEuKgFfbzjy+N4hDAAkTdPXaZp2KqoCTdNwutaFz8l32N/XIO7JkOSiEgz0HYt/BgzGYjFsbG7iUncPVEVFba0LGxsfdZg2KeDvvV0JA4BlZGRESCQSTpfLhb7+fnAsKyaTyZcXgtcWxiP3n1WC5YDh4eFPTqezaW1t7e3PBaAfrGkYRhaAYRhGAYBsmqZhmqZ04PXvnxJcjThBj5sAAAAASUVORK5CYII=) no-repeat 8px 7px !important;
    padding: 5px 7px 7px 30px !important;
    color:#fff !important;
    border-bottom-left-radius:7px !important;
    -moz-border-bottom-left-radius:7px !important;
    -webkit-border-bottom-left-radius:7px !important;
    border-top-left-radius:7px !important;
    -moz-border-top-left-radius:7px !important;
    -webkit-border-top-left-radius:7px !important;
}

.oaseo_pos_button:hover{
    padding-right:10px !important;
}

<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_style(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

<?php if (!empty($_smarty_tpl->tpl_vars['no_compile']->value)){?>
Empty
<?php }else{ ?>
<?php if (!empty($_smarty_tpl->tpl_vars['frontend']->value)){?>
<div style="display: none;"><?php }?>
<div id="oaseo_edit_form_<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" class="oa_form cst_meta_edit">
<?php $_smarty_tpl->smarty->_tag_stack[] = array('form', array('name'=>"meta_edit")); $_block_repeat=true; echo smarty_block_form(array('name'=>"meta_edit"), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

<table class="ow_form ow_table_1">
    <tr class="<?php echo smarty_function_cycle(array('values'=>'ow_alt1, ow_alt2'),$_smarty_tpl);?>
">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'title'),$_smarty_tpl);?>
</td>
        <td class="ow_value"><?php echo smarty_function_input(array('name'=>'title'),$_smarty_tpl);?>
</td>
        
    </tr>
    <tr class="<?php echo smarty_function_cycle(array('values'=>'ow_alt1, ow_alt2'),$_smarty_tpl);?>
">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'keywords'),$_smarty_tpl);?>
</td>
        <td class="ow_value"><?php echo smarty_function_input(array('name'=>'keywords'),$_smarty_tpl);?>
</td>
        
    </tr>
    <tr class="<?php echo smarty_function_cycle(array('values'=>'ow_alt1, ow_alt2'),$_smarty_tpl);?>
">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'desc'),$_smarty_tpl);?>
</td>
        <td class="ow_value"><?php echo smarty_function_input(array('name'=>'desc'),$_smarty_tpl);?>
</td>
        
    </tr>
    <?php if (!empty($_smarty_tpl->tpl_vars['urlAvail']->value)){?>
    <tr class="<?php echo smarty_function_cycle(array('values'=>'ow_alt1, ow_alt2'),$_smarty_tpl);?>
">
        <td class="ow_label"><?php echo smarty_function_label(array('name'=>'url'),$_smarty_tpl);?>
</td>
        <td class="ow_value"><?php echo smarty_function_input(array('name'=>'url'),$_smarty_tpl);?>
</td>
        
    </tr>
    <?php }else{ ?>
    <tr>
        <td colspan="3"><?php echo smarty_function_text(array('key'=>'oaseo+url_not_editable'),$_smarty_tpl);?>
</td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="2" class="ow_center">
            <?php echo smarty_function_submit(array('name'=>"submit"),$_smarty_tpl);?>

        </td>
    </tr>
</table>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_form(array('name'=>"meta_edit"), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

</div>
<?php if (!empty($_smarty_tpl->tpl_vars['frontend']->value)){?>
</div>
<div class="oaseo_pos_button" id="aoseo_button_<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
"><?php echo smarty_function_text(array('key'=>'oaseo+frontend_button_label'),$_smarty_tpl);?>
</div>
<?php }?>
<?php }?><?php }} ?>