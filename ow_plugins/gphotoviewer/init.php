<?php
OW::getRouter()->addRoute(new OW_Route('gphotoviewer.photos_content', 'gphotoviewer/ajax/get-photos/', 'GPHOTOVIEWER_CTRL_Index', 'getPhotosContent'));
OW::getRouter()->addRoute(new OW_Route('gphotoviewer.photos_comment', 'gphotoviewer/ajax/get-photo_comment/', 'GPHOTOVIEWER_CTRL_Index', 'getPhotosComment'));
OW::getRouter()->addRoute(new OW_Route('gphotoviewer.photo_download', 'gphotoviewer/photo/download/:id/', 'GPHOTOVIEWER_CTRL_Index', 'download'));
OW::getRouter()->addRoute(new OW_Route('gphotoviewer.admin_config', 'admin/gphotoviewer', 'GPHOTOVIEWER_CTRL_Admin', 'index'));
$plugin = OW::getPluginManager()->getPlugin('gphotoviewer');
function photoviewer_script_render($event){
	$configs = OW::getConfig()->getValues('gphotoviewer');
	if(!$configs['enable_photo_viewer']) return;
	OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('gphotoviewer')->getStaticCssUrl() . 'PhotoViewer.min.css');
	OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('gphotoviewer')->getStaticCssUrl() . 'font-awesome.css');
	OW::getDocument()->addScript(OW::getPluginManager()->getPlugin('gphotoviewer')->getStaticJsUrl() . 'PhotoViewer.min.js');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'from');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'repeat');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'pause');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'play');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'slideshow');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'all_photos');
	OW::getLanguage()->addKeyForJs('gphotoviewer', 'loading');
	$slideshow_time = $configs['slideshow_time_per_a_photo'];

    if ($slideshow_time < 1 || $slideshow_time > 10){ // between 1 and 10 sec
      $slideshow_time = 3;
    }
    $slideshow_time = $slideshow_time * 1000;
	$urlPhoto = OW::getRouter()->urlForRoute('gphotoviewer.photos_content');
	$urlComment = OW::getRouter()->urlForRoute('gphotoviewer.photos_comment');
	$ajaxResponder = OW::getRouter()->urlFor('PHOTO_CTRL_Photo', 'ajaxResponder');
    $content = <<<CONTENT
$(window).ready(function (){
  PhotoViewer.options.slideshow_time = {$slideshow_time};
  PhotoViewer.options.urlPhoto = '{$urlPhoto}';
  PhotoViewer.options.urlComment = '{$urlComment}';
  PhotoViewer.options.ajaxResponder = '{$ajaxResponder}';	   
  PhotoViewer.bindPhotoViewer();
  window.wpViewerTimer = setInterval(function (){
    PhotoViewer.bindPhotoViewer();
  }, 3000);
});

CONTENT;

    OW::getDocument()->addOnloadScript($content);
}
OW::getEventManager()->bind(OW_EventManager::ON_FINALIZE, 'photoviewer_script_render');

?>
