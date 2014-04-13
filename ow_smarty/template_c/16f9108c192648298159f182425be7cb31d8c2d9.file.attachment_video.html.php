<?php /* Smarty version Smarty-3.1.12, created on 2014-04-12 18:03:16
         compiled from "/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachment_video.html" */ ?>
<?php /*%%SmartyHeaderCode:17199931335349e25462a6e7-98232911%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16f9108c192648298159f182425be7cb31d8c2d9' => 
    array (
      0 => '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_plugins/equestions/views/components/attachment_video.html',
      1 => 1397334782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17199931335349e25462a6e7-98232911',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'uniqId' => 0,
    'videoActive' => 0,
    'langs' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5349e254681d52_38428559',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5349e254681d52_38428559')) {function content_5349e254681d52_38428559($_smarty_tpl) {?><?php if (!is_callable('smarty_function_text')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.text.php';
if (!is_callable('smarty_function_decorator')) include '/home/agilekod/domains/baksmaker.com/public_html/fulfill/ow_smarty/plugin/function.decorator.php';
?><div id="<?php echo $_smarty_tpl->tpl_vars['uniqId']->value;?>
">

    <div class="att-body ATT_PanelBody">

        <div class="av-home AV_Home AV_View">

            <div class="av-youtube av-icon av-ic-youtube AV_YT_SearchHomeInputC clearfix">
                <div class="ow_left av-youtube-input-c">
                    <input name="ytQuery" type="text" class="av-youtube-input invitation ATT_Invitatio AV_YT_SearchHomeInput" value="<?php echo smarty_function_text(array('key'=>"equestions+attachments_search_video_inv"),$_smarty_tpl);?>
" inv="<?php echo smarty_function_text(array('key'=>"equestions+attachments_search_video_inv"),$_smarty_tpl);?>
" />
                </div>
                <a href="javascript://" class="av-icon-button av-yt-icon-button ow_ic_lens AV_YT_SearchHomeBtn"></a>
            </div>

            <div class="av-delim ow_smallmargin att-legend ow_border">
                <?php echo smarty_function_text(array('key'=>"equestions+attachments_embed_video_inv"),$_smarty_tpl);?>

            </div>

            <div class="av-embed-c ow_smallmargin">
                <div class="av-embed av-icon av-ic-embed AV_EmbedHomeC">
                    <textarea class="ATT_Invitation AV_EmbedHomeInput"></textarea>
                </div>
            </div>

        </div>

        <div class="av-result AV_Result AV_View" style="display: none;">

        </div>

        <div class="av-my  AV_MyPanel AV_View" style="display: none;">
            <div class="avm-list clearfix AV_MyPanelList">

            </div>
            <a style="display: none;" href="javascript://" class="AV_MyPanelListViewMore avm-view-more ow_border">
                <span class="ow_icon_control avm-more-label AV_MyPanelListViewMoreLabel">
                    <?php echo smarty_function_text(array('key'=>"equestions+attachments_select_my_video_view_more"),$_smarty_tpl);?>

                </span>
            </a>
        </div>

    </div>



    <div class="att-bottom ow_border clearfix ATT_PanelControls">

        <?php if ($_smarty_tpl->tpl_vars['videoActive']->value){?>
            <div class="att-switch ow_left">
                <a class="att-switch-btn ow_icon_control ow_ic_video AV_SwitchToMyVideos" href="javascript://"><?php echo smarty_function_text(array('key'=>"equestions+attachments_select_my_video"),$_smarty_tpl);?>
</a>
            </div>
        <?php }?>

        <div class="att-controls ow_right">

            <span class="AV_MySave AV_MainControl" style="display: none;"><?php echo smarty_function_decorator(array('name'=>"button",'class'=>"ow_ic_save",'label'=>$_smarty_tpl->tpl_vars['langs']->value['chooseMy']),$_smarty_tpl);?>
</span>

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
        <?php echo smarty_function_text(array('key'=>"equestions+attachments_video_title"),$_smarty_tpl);?>

    </div>

</div><?php }} ?>