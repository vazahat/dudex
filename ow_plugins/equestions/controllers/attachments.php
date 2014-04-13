<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.controllers
 */
class EQUESTIONS_CTRL_Attachments extends OW_ActionController
{
    /**
     *
     * @var EQUESTIONS_BOL_Service
     */
    private $service;

    public function __construct()
    {
        parent::__construct();
    }

    public function uploader()
    {
        $uniqId = $_POST['uniqId'];

        $language = OW::getLanguage();
        $error = false;

        if ( empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name']) )
        {
            $error = $language->text('base', 'upload_file_fail');
        }
        else if ( $_FILES['file']['error'] != UPLOAD_ERR_OK )
        {
            switch ( $_FILES['file']['error'] )
            {
                case UPLOAD_ERR_INI_SIZE:
                    $error = $language->text('base', 'upload_file_max_upload_filesize_error');
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $error = $language->text('base', 'upload_file_file_partially_uploaded_error');
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $error = $language->text('base', 'upload_file_no_file_error');
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = $language->text('base', 'upload_file_no_tmp_dir_error');
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $error = $language->text('base', 'upload_file_cant_write_file_error');
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $error = $language->text('base', 'upload_file_invalid_extention_error');
                    break;

                default:
                    $error = $language->text('base', 'upload_file_fail');
            }
        }


        if ( $error !== false )
        {
            $response = array(
                'type' => 'uploadError',
                'error' => $error,
                'result' => empty($_FILES['file']) ? false : $_FILES['file']
            );
        }
        else
        {
            $query = json_decode($_POST['query'], true);
            $file = $_FILES['file'];
            $method = trim($_POST['command']);

            $response = call_user_func(array($this, $method), $file, $query);
        }

        $attachSelector = 'window.parent.CORE.ObjectRegistry[' . json_encode($uniqId) . ']';

        $out = '<html><head><script>
            ' . $attachSelector . '.uploadComplete(' . json_encode($response) . ');
        </script></head><body></body></html>';

        echo $out;
        exit;
    }

    public function imageUploader( $file, $query )
    {
        $error = false;
        $language = OW::getLanguage();

        if ( !UTIL_File::validateImage($file['name']) )
        {
            $error = $language->text('base', 'upload_file_extension_is_not_allowed');
        }

        if ( (int) $file['size'] > (float) OW::getConfig()->getValue('base', 'tf_max_pic_size') * 1024 * 1024 )
        {
            $error = $language->text('base', 'upload_file_max_upload_filesize_error');
        }

        if ( $error )
        {
            return array(
                'type' => 'uploadError',
                'error' => $error,
                'result' => $file
            );
        }

        $service = BOL_AttachmentService::getInstance();

        $attachDto = new BOL_Attachment();
        $attachDto->setUserId(OW::getUser()->getId());
        $attachDto->setAddStamp(time());
        $attachDto->setStatus(0);
        $service->saveAttachment($attachDto);

        $fileName = 'attach_' . $attachDto->getId() . '.' . UTIL_File::getExtension($file['name']);

        $attachDto->setFileName($fileName);
        $service->saveAttachment($attachDto);

        $uploadPath = $service->getAttachmentsTempDir() . $fileName;
        $uploadUrl = $service->getAttachmentsTempUrl() . $fileName;

        if( !move_uploaded_file($file['tmp_name'], $uploadPath) )
        {
            return array(
                'type' => 'uploadError',
                'error' => $language->text('base', 'upload_file_fail'),
                'result' => $file
            );
        }

        @chmod($uploadPath, 0666);

        $markup = array(
            'html' => '<img src="' . $uploadUrl . '" />',
            'js' => '',
            'css' => ''
        );

        $content = new EQUESTIONS_CMP_AttPhotoPreview($uploadUrl);

        $result = array();
        $result['content'] = array(
            'html' => $content->render(),
            'js' => '',
            'css' => ''
        );

        $result['oembed'] = array(
            "type" => "file",
            'filePath' => $uploadPath,
            "fileId" => $attachDto->getId()
        );

        $response = array(
            'content' => $markup,
            'type' => 'imageUploader',
            'result' => $result
        );

        return $response;
    }


    private function log( $msg )
    {
        $path = OW::getPluginManager()->getPlugin('equestions')->getPluginFilesDir() . 'log.txt';

        file_put_contents($path, $msg);
    }

    public function webcamHandler()
    {
        if ( !OW::getRequest()->isPost() )
        {
            throw new Redirect404Exception();
        }

        $service = BOL_AttachmentService::getInstance();

        $attachDto = new BOL_Attachment();
        $attachDto->setUserId(OW::getUser()->getId());
        $attachDto->setAddStamp(time());
        $attachDto->setStatus(0);
        $service->saveAttachment($attachDto);

        $fileName = 'attach_' . $attachDto->getId() . '.jpg';

        $attachDto->setFileName($fileName);
        $service->saveAttachment($attachDto);

        $uploadPath = $service->getAttachmentsTempDir() . $fileName;
        $uploadUrl = $service->getAttachmentsTempUrl() . $fileName;

        // The JPEG snapshot is sent as raw input:
        $input = file_get_contents('php://input');

        if( md5($input) == '7d4df9cc423720b7f1f3d672b89362be' )
        {
            // Blank image. We don't need this one.
            echo json_encode(array(
                'type' => 'takeError',
                'error' => 'Empty photo',
                'result' => array()
            ));

            exit;
        }

        $result = file_put_contents($uploadPath, $input);
        if ( !$result )
        {
            echo json_encode(array(
                'type' => 'takeError',
                'error' => 'Failed save the image. Make sure you chmod the uploads folder and its subfolders to 777',
                'result' => array()
            ));

            exit;
        }

        @chmod($uploadPath, 0666);

        $info = getimagesize($uploadPath);
        if($info['mime'] != 'image/jpeg')
        {
            @unlink($uploadPath);

            echo json_encode(array(
                'type' => 'takeError',
                'error' => 'Wrong file',
                'result' => array()
            ));

            exit;
        }

        $content = new EQUESTIONS_CMP_AttPhotoPreview($uploadUrl);

        $xml = "<content><html><![CDATA[" . $content->render() . "]]></html><js></js></content><filePath>" . $uploadPath . "</filePath><fileId>" . $attachDto->getId() . "</fileId>";

        $out = '<root>' . $xml . '</root>';

        echo $out;

        exit;
    }


    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $command = trim($_POST['command']);
        $query = json_decode($_POST['params'], true);

        $responce = call_user_func(array($this, $command), $query);

        echo json_encode($responce);
        exit;
    }

    public function videoPreview( $query )
    {
        $query['oembed']['html'] = EQUESTIONS_CLASS_VideoTools::validateEmbedCode($query['oembed']['html']);

        if ( empty($query['oembed']['html']) )
        {
            return array(
                "error" => 'Not valid video embed',
            );
        }

        $cmp = new EQUESTIONS_CMP_AttVideoPreview($query['oembed']);

        $markup = array(
            'html' => $cmp->render(),
            'js' => OW::getDocument()->getOnloadScript(),
            'css' => ''
        );

        return array(
            "oembed" => $query['oembed'],
            "content" => $markup
        );
    }

    private function ytEmptySearchRsult()
    {
        $noVideo = OW::getLanguage()->text('equestions', 'attacments_yt_no_video');

        $markup = array(
            'html' => '<div class="yt-empty-result ow_nocontent">' . $noVideo . '</div>',
            'js' => '',
            'css' => ''
        );

        return array(
            'content' => $markup,
            'fb' => array(
                "width" => 600,
                "top" => 50
            )
        );
    }

    public function ytSearch( $query )
    {
        $document = OW::getDocument();

        $queryUrl = OW::getRequest()->buildUrlQueryString("http://gdata.youtube.com/feeds/api/videos", array(
            'q' => $query['query'],
            'alt' => 'json',
            'format' => 5,
            'v' => 2,
            'start-index' => 1,
            'max-results' => EQUESTIONS_CMP_YoutubeSearchResult::ITEMS_COUNT
        ));

        $response = file_get_contents($queryUrl);

        $cmp = new EQUESTIONS_CMP_YoutubeSearchResult($query['uniqId'], $query['query'], $response);

        $cmp->setWindowOptions($query['window']);

        $markup = array(
            'html' => $cmp->render(),
            'js' => $document->getOnloadScript(),
            'css' => ''
        );

        return array(
            'content' => $markup,
            'fb' => array(
                "width" => 600,
                "top" => 50
            )
        );
    }


    public function ytSearchList( $query )
    {
        $queryUrl = OW::getRequest()->buildUrlQueryString("http://gdata.youtube.com/feeds/api/videos", array(
            'q' => $query['query'],
            'alt' => 'json',
            'format' => 5,
            'v' => 2,
            'start-index' => $query['data']['start'],
            'max-results' => $query['data']['offset']
        ));

        $response = file_get_contents($queryUrl);

        if ( empty($response) )
        {
            return $this->ytEmptySearchRsult();
        }

        $response = json_decode($response, true);

        if ( empty($response['feed']['entry']) )
        {
            return $this->ytEmptySearchRsult();
        }

        $query['data']['query'] = $query['query'];

        $cmp = new EQUESTIONS_CMP_YoutubeList($response);

        $markup = array(
            'html' => $cmp->render(),
            'js' => '',
            'css' => ''
        );

        return array(
            'content' => $markup,
            'viewMore' => $cmp->getItemsCount() == $query['data']['offset'],
            'data' => $query['data']
        );
    }

    public function ytMore( $query )
    {
        if ( empty($query['data']['query']) )
        {
            return array(
                'viewMore' => false
            );
        }

        $query['data']['start'] = $query['data']['start'] + $query['data']['offset'];

        $queryUrl = OW::getRequest()->buildUrlQueryString("http://gdata.youtube.com/feeds/api/videos", array(
            'q' => $query['data']['query'],
            'alt' => 'json',
            'format' => 5,
            'v' => 2,
            'start-index' => $query['data']['start'],
            'max-results' => $query['data']['offset']
        ));

        $response = file_get_contents($queryUrl);
        $response = json_decode($response, true);

        $cmp = new EQUESTIONS_CMP_YoutubeList($response);

        $markup = array(
            'html' => $cmp->render(),
            'js' => '',
            'css' => ''
        );

        return array(
            'more' => $markup,
            'viewMore' => $cmp->getItemsCount() == $query['data']['offset'],
            'data' => $query['data']
        );
    }

    private function queryLink( $query )
    {
        $url = $query['link'];

        $urlInfo = parse_url($url);
        if ( empty($urlInfo['scheme']) )
        {
            $url = 'http://' . $url;
        }

        $oembed = @UTIL_HttpResource::getOEmbed($url);

        if ( empty($oembed) || ( isset($oembed['result']) && $oembed['result'] == false )
            || ( $oembed['type'] == 'link' && empty($oembed['title']) && empty($oembed['description']) ) )
        {
            $response = array(
                'type' => 'link',
                'result' => false,
                'href' => $url
            );

            return $response;
        }

        $attacmentUniqId = uniqid('att');

        switch ( $oembed['type'] )
        {
            case 'video':
                $oembedCmp = new EQUESTIONS_CMP_AttVideoPreview($oembed);
                break;

            case 'photo':
                $oembedCmp = new EQUESTIONS_CMP_AttPhotoPreview($oembed['url']);
                break;

            default:
                $oembedCmp = new EQUESTIONS_CMP_AttLinkPreview($oembed);
                $attacmentUniqId = $oembedCmp->initJs($query['uniqId']);
        }

        unset($oembed['allImages']);

        $content = '<div class="al-result-preview">' . $oembedCmp->render() . '</div>';

        $response = array(
            'content' => $this->getMarkup($content),
            'type' => 'link',
            'result' => $oembed,
            'attachment' => $attacmentUniqId,
            'processedUrl' => $query['link']
        );

        return $response;
    }

    private function getMarkup( $html )
    {
        /* @var $document OW_AjaxDocument */
        $document = OW::getDocument();

        $markup = array();
        $markup['html'] = $html;

        $onloadScript = $document->getOnloadScript();
        $markup['js'] = empty($onloadScript) ? null : $onloadScript;

        $styleDeclarations = $document->getStyleDeclarations();
        $markup['css'] = empty($styleDeclarations) ? null : $styleDeclarations;

        return $markup;
    }


    private function getMyPhotos( $query )
    {
        $userId = OW::getUser()->getId();
        $bridge = EQUESTIONS_CLASS_PhotoBridge::getInstance();

        if ( !$bridge->isActive() )
        {
            return false;
        }

        $count = $query['offset'] == 0 ? 25 : 10;

        $photos = $bridge->findUserPhotos($userId, $query['offset'], $count + 6);

        $viewMore = ( count($photos) - $count ) > 5;

        if ( $viewMore )
        {
            $photos = array_slice($photos, 0, $count);
        }

        $cmp = new EQUESTIONS_CMP_MyPhotos($photos);

        return array(
            'myPanel' => array(
                'html' => $cmp->render(),
                'viewMore' => $viewMore,
                'offset' => $query['offset'] + count($photos),
                'itemsCount' => count($photos)
            )
        );
    }

    private function saveMyPhoto( $query )
    {
        $oembed = json_decode($query['oembed'], true);

        $markup = array(
            'html' => '<img src="' . $oembed['url'] . '" />',
            'js' => '',
            'css' => ''
        );

        $content = new EQUESTIONS_CMP_AttPhotoPreview($oembed['url']);

        $result = array();
        $result['content'] = array(
            'html' => $content->render(),
            'js' => '',
            'css' => ''
        );

        $result['oembed'] = $oembed;

        $response = array(
            'content' => $markup,
            'result' => $result
        );

        return $response;
    }

    private function getMyVideos( $query )
    {
        $userId = OW::getUser()->getId();
        $bridge = EQUESTIONS_CLASS_VideoBridge::getInstance();

        if ( !$bridge->isActive() )
        {
            return false;
        }

        $count = $query['offset'] == 0 ? 15 : 10;

        $videos = $bridge->findUserVideos($userId, $query['offset'], $count + 6);

        $viewMore = ( count($videos) - $count ) > 5;

        if ( $viewMore )
        {
            $videos = array_slice($videos, 0, $count);
        }

        $cmp = new EQUESTIONS_CMP_MyVideos($videos);

        return array(
            'myPanel' => array(
                'html' => $cmp->render(),
                'viewMore' => $viewMore,
                'offset' => $query['offset'] + count($videos),
                'itemsCount' => count($videos)
            )
        );
    }

    private function saveMyVideo( $query )
    {
        $oembed = json_decode($query['oembed'], true);

        $_oembed = $oembed;
        //unset($_oembed['thumbnail_url']);
        $cmp = new EQUESTIONS_CMP_AttVideoPreview($_oembed);
        $markup = array(
            'html' => $cmp->render(),
            'js' => '',
            'css' => ''
        );

        $result = array();
        $result['content'] = array(
            'html' => $cmp->render(),
            'js' => '',
            'css' => ''
        );

        return array(
            "oembed" => $oembed,
            "content" => $markup
        );
    }

}