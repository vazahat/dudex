<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

require_once OW_DIR_LIB . 'oembed' . DS . 'oembed.php';

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.classes
 */
class EQUESTIONS_CLASS_OembedProvider extends OEmbedProvider
{
    public function __construct()
    {

    }

    public function check( $url )
    {
        return true;
    }

    private function getType( $url )
    {
        $urlInfo = parse_url($url);

        if ( empty($urlInfo['path']) )
        {
            return 'link';
        }

        $foo = explode('.', $urlInfo['path']);
        $ext = end($foo);

        switch ( trim($ext) )
        {
           case 'gif':
           case 'jpeg':
           case 'jpg':
           case 'png':
                return 'image';

            default :
                return 'link';
        }
    }

    private function parsePage( $url )
    {
        $content = @UTIL_HttpResource::getContents($url);

        $matches = array();
        preg_match('/<\s*meta\s*[^\>]*?http-equiv=[\'"]content-type[\'"][^\>]*?\s*>/i',$content,$matches);
        $meta = empty($matches[0]) ? null : $matches[0];

        preg_match('/content=[\'"][^\'"]*?charset=([\w-]+)(:[^\w-][^\'"])*?[\'"]/i',$meta,$matches);
        $encoding = empty($matches[1]) ? 'UTF-8' : $matches[1];

        preg_match('/<\s*title\s*>([\s\S]*?)<\s*\/\s*title\s*>/i',$content,$matches);
        $title = empty($matches[1]) ? null : mb_convert_encoding($matches[1], 'UTF-8', $encoding);

        $matches = array();
        $meta = "";
        preg_match('/<\s*meta\s*[^\>]*?name=[\'"]description[\'"][^\>]*?\s*>/i',$content,$matches);
        $meta = empty($matches[0]) ? null : $matches[0];

        $matches = array();
        preg_match('/content=[\'"](.*?)[\'"]/i',$meta,$matches);
        $description = empty($matches[1]) ? null : mb_convert_encoding($matches[1], 'UTF-8', $encoding);

        $matches = array();
        preg_match_all('/<\s*img\s*.*?src=[\'"](.+?)[\'"].*?>/i',$content, $matches);

        $images = array();

        foreach ( $matches[1] as $img )
        {
            $urlInfo = parse_url($url);
            $imgInfo = parse_url($img);

            if ( empty($imgInfo['host']) )
            {
                $imgDir = dirname($imgInfo['path']);

                $urlScheme = empty($urlInfo['scheme']) ? '' : $urlInfo['scheme'] . '://';
                $urlAddr = $urlScheme . $urlInfo['host'];

                if ( strpos($imgDir, '/') === 0 )
                {
                    $img = $urlAddr . $imgInfo['path'];
                }
                elseif ( !empty($urlInfo['path']) )
                {
                    $pp = pathinfo($urlInfo['path']);
                    $urlPath = $pp['dirname'] . ( empty($pp['extension']) ? $pp['basename'] . '/' : '' );
                    $img = $urlAddr . $urlPath . $imgInfo['path'];
                }
                else
                {
                    $img = $urlAddr . '/' . $imgInfo['path'];
                }
            }

            $images[] = $img;
        }

        $firstImg = reset($images);
        $firstImg = $firstImg ? $firstImg : null;

        return array(
            'type' => 'link',
            'description' => $description,
            'title' => $title,
            'thumbnail_url' => $firstImg,
            'allImages' => $images
        );
    }

    public function parse( $url )
    {
        $sType = $this->getType($url);

        if ( $sType == 'image' )
        {
            return array(
                'url' => $url,
                'href' => $url,
                'type' => 'photo'
            );
        }

        return $this->parsePage($url);
    }
}