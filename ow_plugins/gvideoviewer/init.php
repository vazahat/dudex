<?php
OW::getRouter()->addRoute(new OW_Route('gvideoviewer.videos_content', 'gvideoviewer/ajax/get-videos/', 'GVIDEOVIEWER_CTRL_Index', 'getVideosContent'));
OW::getRouter()->addRoute(new OW_Route('gvideoviewer.videos_comment', 'gvideoviewer/ajax/get-video_comment/', 'GVIDEOVIEWER_CTRL_Index', 'getVideosComment'));
OW::getRouter()->addRoute(new OW_Route('gvideoviewer.admin_config', 'admin/gvideoviewer', 'GVIDEOVIEWER_CTRL_Admin', 'index'));
$plugin = OW::getPluginManager()->getPlugin('gvideoviewer');
function videoviewer_script_render($event){
	if(!OW::getPluginManager()->isPluginActive('video')) return; //check video plugin is active
	$configs = OW::getConfig()->getValues('gvideoviewer');
	if(!$configs['enable_video_viewer']) return;
	OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('gvideoviewer')->getStaticCssUrl() . 'VideoViewer.min.css');
	OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('gvideoviewer')->getStaticCssUrl() . 'font-awesome.css');
	OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('gvideoviewer')->getStaticJsUrl() . 'VideoViewer.min.js');
	OW::getLanguage()->addKeyForJs('gvideoviewer', 'from');
	OW::getLanguage()->addKeyForJs('gvideoviewer', 'all_videos');
	OW::getLanguage()->addKeyForJs('gvideoviewer', 'loading');

	$urlVideo = OW::getRouter()->urlForRoute('gvideoviewer.videos_content');
	$urlComment = OW::getRouter()->urlForRoute('gvideoviewer.videos_comment');
	$ajaxResponder = OW::getRouter()->urlFor('VIDEO_CTRL_Video', 'ajaxResponder');
    $content = <<<CONTENT
$(window).ready(function (){
  VideoViewer.options.urlVideo = '{$urlVideo}';
  VideoViewer.options.urlComment = '{$urlComment}';
  VideoViewer.options.ajaxResponder = '{$ajaxResponder}';	   
  VideoViewer.bindVideoViewer();
  window.wpViewerTimer = setInterval(function (){
    VideoViewer.bindVideoViewer();
  }, 3000);
});

CONTENT;

    OW::getDocument()->addOnloadScript($content);
}
OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, 'videoviewer_script_render');

?>
