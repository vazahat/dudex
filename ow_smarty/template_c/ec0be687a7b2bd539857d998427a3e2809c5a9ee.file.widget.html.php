<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/yncontactimporter/views/components/widget.html" */ ?>
<?php /*%%SmartyHeaderCode:16055216675349e254b3b269-68721907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ec0be687a7b2bd539857d998427a3e2809c5a9ee' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/yncontactimporter/views/components/widget.html',
      1 => 1397334842,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16055216675349e254b3b269-68721907',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'providers' => 0,
    'provider' => 0,
    'class' => 0,
    'width' => 0,
    'height' => 0,
    'showMore' => 0,
    'viewMore' => 0,
    'authorization' => 0,
    'import_your_contacts' => 0,
    'uploadCSVTitle' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254bb82a3_90231347',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254bb82a3_90231347')) {function content_5349e254bb82a3_90231347($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
?><div id="import_form_homepage">
    <?php  $_smarty_tpl->tpl_vars["provider"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["provider"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['providers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["provider"]->key => $_smarty_tpl->tpl_vars["provider"]->value){
$_smarty_tpl->tpl_vars["provider"]->_loop = true;
?>
    	<?php if ($_smarty_tpl->tpl_vars['provider']->value['name']!='file CSV'){?>
			 <?php $_smarty_tpl->tpl_vars['class'] = new Smarty_variable('logoContact', null, 0);?>
		<?php }else{ ?>
			<?php $_smarty_tpl->tpl_vars['class'] = new Smarty_variable('logUpload_file', null, 0);?>
		<?php }?>
		<div class="<?php echo $_smarty_tpl->tpl_vars['class']->value;?>
">
			<a id ="<?php echo $_smarty_tpl->tpl_vars['provider']->value['id'];?>
" rel="<?php echo $_smarty_tpl->tpl_vars['provider']->value['name'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['provider']->value['title'];?>
" href="javascript:;">
				<img src='<?php echo $_smarty_tpl->tpl_vars['provider']->value['logo'];?>
' width="<?php echo $_smarty_tpl->tpl_vars['width']->value;?>
" height = "<?php echo $_smarty_tpl->tpl_vars['height']->value;?>
">
			</a>		
		</div>
	<?php } ?>
	<?php if ($_smarty_tpl->tpl_vars['showMore']->value){?>
	<div style="clear:both;width:100%;display:block"> </div>
	<span style="display:block; text-align: right; padding-right: 8px;	">
		<a href="<?php echo $_smarty_tpl->tpl_vars['viewMore']->value;?>
" alt="<?php echo smarty_function_text(array('key'=>'yncontactimporter+view_all_providers'),$_smarty_tpl);?>
" title="<?php echo smarty_function_text(array('key'=>'yncontactimporter+view_all_providers'),$_smarty_tpl);?>
"><?php echo smarty_function_text(array('key'=>'yncontactimporter+view_more'),$_smarty_tpl);?>
</a>
	</span>
	<?php }?>
</div>
<div style="clear:both;width:100%;display:block"> </div>
<script type="text/javascript">
	$("#import_form_homepage div.logoContact a").on("click", function(e)
	{ 
		var providerId = $(this).attr("id");
		var providerName = $(this).attr("rel");
		var height = 90;
		var title = '<?php echo $_smarty_tpl->tpl_vars['authorization']->value;?>
';
		var title = "Authorization";
		var arr_providers = ['facebook','gmail','yahoo','twitter','linkedin', 'hotmail'];
		var flag = false;
		for(var i = 0; i < arr_providers.length; i++)
		{
			if(providerName == arr_providers[i])
				flag = true;
		}
		if(!flag)
		{
			height = 160;
			title ='<?php echo $_smarty_tpl->tpl_vars['import_your_contacts']->value;?>
';
		}
		var f = providerName.charAt(0).toUpperCase();
		var name = f + providerName.substr(1);
		
       	OW.ajaxFloatBox("YNCONTACTIMPORTER_CMP_PopupAuthorization", {providerId : providerId} , 
       	{
       		width:400, 
       		height: height, 
       		iconClass: "ow_ic_user", 
       		title: name + " " + title
       	});
       	
    });
    $("#import_form_homepage div.logUpload_file a").on("click", function(e)
	{ 
		var title ='<?php echo $_smarty_tpl->tpl_vars['uploadCSVTitle']->value;?>
';
		
	   	OW.ajaxFloatBox("YNCONTACTIMPORTER_CMP_PopupUploadcsv", {providerId: 0} , 
	   	{
	   		width:400, 
	   		height: 90, 
	   		iconClass: "ow_ic_user", 
	   		title: title
	   	});
	   	
	});
</script><?php }} ?>