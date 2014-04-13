<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:17
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_themes/ocs_liquid/master_pages/general.html" */ ?>
<?php /*%%SmartyHeaderCode:8092475385349e255506f85-68010591%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '85cde65831a930b7c9de22202fac2f57b6699b43' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_themes/ocs_liquid/master_pages/general.html',
      1 => 1397335859,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8092475385349e255506f85-68010591',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'imageControlValues' => 0,
    'siteUrl' => 0,
    'siteName' => 0,
    'main_menu' => 0,
    'themeImagesUrl' => 0,
    'heading' => 0,
    'heading_icon_class' => 0,
    'content' => 0,
    'bottom_menu' => 0,
    'bottomPoweredByLink' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2555bcd16_35498768',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2555bcd16_35498768')) {function content_5349e2555bcd16_35498768($_smarty_tpl) {?><?php if (!is_callable('smarty_function_component')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.component.php';
if (!is_callable('smarty_function_add_content')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.add_content.php';
if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?><div class="ow_page_wrap">
<div class="ow_page_padding">
	<div class="ow_site_panel">
		<div class="ow_site_panel_wrap"><?php echo smarty_function_component(array('class'=>'BASE_CMP_Console'),$_smarty_tpl);?>
</div>
	</div>

	<div class="ow_page_container">
		<div class="ow_logo_wrap">
		  <?php if (isset($_smarty_tpl->tpl_vars['imageControlValues']->value['logoImage']['src'])){?>
		      <a href="<?php echo $_smarty_tpl->tpl_vars['siteUrl']->value;?>
" class="ow_logo_img"></a>
		  <?php }else{ ?>
		      <a href="<?php echo $_smarty_tpl->tpl_vars['siteUrl']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['siteName']->value;?>
</a>
		  <?php }?>
        </div>
		<div class="ow_header">
		<div class="ow_header_wrap">
			<div class="ow_menu_wrap"><?php echo $_smarty_tpl->tpl_vars['main_menu']->value;?>
</div>
	    </div>
	    </div>
	        
		<div class="ow_canvas">
		<div class="ow_canvas_lwrap">
		<div class="ow_canvas_rwrap">
		    <?php if (isset($_smarty_tpl->tpl_vars['imageControlValues']->value['headerImage']['src'])){?>
		        <img class="ow_header_img" src="<?php echo $_smarty_tpl->tpl_vars['imageControlValues']->value['headerImage']['src'];?>
" />
		    <?php }else{ ?>
		        <div class="ow_header_pic"><img class="ow_header_img" src="<?php echo $_smarty_tpl->tpl_vars['themeImagesUrl']->value;?>
header.png" /></div>
		    <?php }?>
			<div class="ow_bg_color">
				<div class="ow_page clearfix">
					<?php if (!empty($_smarty_tpl->tpl_vars['heading']->value)){?><h1 class="ow_stdmargin <?php echo $_smarty_tpl->tpl_vars['heading_icon_class']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['heading']->value;?>
</h1><?php }?>
					<div class="ow_content">
						<?php echo smarty_function_add_content(array('key'=>'base.add_page_top_content'),$_smarty_tpl);?>

						<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

						<?php echo smarty_function_add_content(array('key'=>'base.add_page_bottom_content'),$_smarty_tpl);?>

					</div>
					<div class="ow_sidebar">
						<?php echo smarty_function_component(array('class'=>"BASE_CMP_Sidebar"),$_smarty_tpl);?>

					</div>
				</div>
			</div>
		</div>
		</div>
		</div>
			
		<div class="ow_canvas_footer_lwrap"><div class="ow_canvas_footer_rwrap"><div class="ow_canvas_footer"></div></div></div>
	</div>
		
	<div class="ow_footer">
	    <div class="ow_canvas">
	        <div class="clearfix">
	            <?php echo $_smarty_tpl->tpl_vars['bottom_menu']->value;?>

	            <div class="ow_copyright">
	                <?php echo smarty_function_text(array('key'=>'base+copyright'),$_smarty_tpl);?>

	            </div>
	            <div class="poweredby_link">
	                <?php echo $_smarty_tpl->tpl_vars['bottomPoweredByLink']->value;?>

	            </div>
	        </div>
	    </div>
	</div>
</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
    $('input[name=userPhoto]').attr("size", "4");
    $('form[name=add-topic-form] input[name^=attachments]').attr("size", "4");
    $('form[name=GROUPS_CreateGroupForm] input[name=image]').attr("size", "4");
    $('form[name=event_add] input[name=image]').attr("size", "4");
    $('form[name=photoUploadForm] input[name^=photos]').attr("size", "4");
});

</script>
    
<?php echo smarty_function_decorator(array('name'=>'floatbox'),$_smarty_tpl);?>
<?php }} ?>