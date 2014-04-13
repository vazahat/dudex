<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/attachment_video.html" */ ?>
<?php /*%%SmartyHeaderCode:8687492235349e2544ba854-93696719%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ebf97f75c49fb1c2930c308448f0d48a366abae1' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/attachments41/views/components/attachment_video.html',
      1 => 1397334690,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8687492235349e2544ba854-93696719',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'langs' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e2544f66b8_64861785',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e2544f66b8_64861785')) {function content_5349e2544f66b8_64861785($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?><div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">

    <div class="att-body ATT_PanelBody">

        <div class="av-home AV_Home AV_View">

            <div class="av-youtube av-icon av-ic-youtube AV_YT_SearchHomeInputC clearfix">
                <div class="ow_left av-youtube-input-c">
                    <input name="ytQuery" type="text" class="av-youtube-input invitation ATT_Invitatio AV_YT_SearchHomeInput" value="<?php echo smarty_function_text(array('key'=>"attachments+attachments_search_video_inv"),$_smarty_tpl);?>
" inv="<?php echo smarty_function_text(array('key'=>"attachments+attachments_search_video_inv"),$_smarty_tpl);?>
" />
                </div>
                <a href="javascript://" class="av-icon-button av-yt-icon-button ow_ic_lens AV_YT_SearchHomeBtn"></a>
            </div>

            <div class="av-delim ow_smallmargin att-legend ow_border">
                <?php echo smarty_function_text(array('key'=>"attachments+attachments_embed_video_inv"),$_smarty_tpl);?>

            </div>

            <div class="av-embed-c ow_smallmargin">
                <div class="av-embed av-icon av-ic-embed AV_EmbedHomeC">
                    <textarea class="ATT_Invitation AV_EmbedHomeInput"></textarea>
                </div>
            </div>

        </div>

        <div class="av-result AV_Result AV_View" style="display: none;">

        </div>

    </div>



    <div class="att-bottom ow_border ATT_PanelControls">

        <div class="att-controls">
            <span class="AV_Save AV_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['addVideo']),$_smarty_tpl);?>
</span>

            

            <span class="AV_YT_SearchBtn AV_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['search']),$_smarty_tpl);?>
</span>

            <span class="AV_Cancel AV_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['cancel']),$_smarty_tpl);?>
</span>
            <span class="AV_Close AV_MainControl"><?php echo smarty_function_decorator(array('name'=>"button",'label'=>$_smarty_tpl->tpl_vars['langs']->value['close'],'class'=>"ow_ic_delete"),$_smarty_tpl);?>
</span>
        </div>

    </div>

    <div class="ATT_PanelTitle">
        <?php echo smarty_function_text(array('key'=>"attachments+attachments_video_title"),$_smarty_tpl);?>

    </div>

</div><?php }} ?>