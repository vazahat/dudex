<?php

class ATTACHMENTS_CLASS_VideoTools
{
    public static function formatEmbedDimensions( $code, $width, $height )
    {
        if ( !ATTACHMENTS_CLASS_VideoBridge::getInstance()->isActive() ) return;
        
        return VIDEO_BOL_ClipService::getInstance()->formatClipDimensions($code, $width, $height);
    }

    /**
     * Validate clip code integrity
     *
     * @param string $code
     * @return string
     */
    public static function validateEmbedCode( $code, $provider = null )
    {
        if ( !ATTACHMENTS_CLASS_VideoBridge::getInstance()->isActive() ) return $code;
        
        return VIDEO_BOL_ClipService::getInstance()->validateClipCode($code, $provider);
    }

    /**
     * Adds parameter to embed code
     *
     * @param string $code
     * @param string $name
     * @param string $value
     * @return string
     */
    public static function addCodeParam( $code, $name = 'wmode', $value = 'transparent' )
    {
        if ( !ATTACHMENTS_CLASS_VideoBridge::getInstance()->isActive() ) return $code;
        
        $out = VIDEO_BOL_ClipService::getInstance()->addCodeParam($code, $name, $value);
        
        $matches = array();
        if ( preg_match("/<iframe[^>]*src=['\"]([^'\"]+)['\"]/i", !empty($out) ? $out : $code, $matches) )
        {
            $src = null;
            if ( strpos($matches[1], "//") === 0 )
            {
                $src = "http:" . $matches[1];
                $src = OW::getRequest()->buildUrlQueryString($src, array($name => $value));
                $src = substr($src, 5);
            }
            else
            {
                $src = OW::getRequest()->buildUrlQueryString($matches[1], array($name => $value));
            }
            
            $repl = preg_replace("/(<iframe[^>]*)src=['\"]([^'\"]+)['\"]/i", "$1src=\"".$src."\"", $code);
        }

        return $repl;
    }
    
    public static function addAutoPlay( $code )
    {
        return self::addCodeParam($code, "autoplay", 1);
    }
    
    public static function detectThumbnail( $code )
    {
        if ( !ATTACHMENTS_CLASS_VideoBridge::getInstance()->isActive() ) return null;
        
        $prov = new VideoProviders($code);
        $provider = $prov->detectProvider();
        $thumbUrl = $prov->getProviderThumbUrl($provider);
        
        if ( $thumbUrl == VideoProviders::PROVIDER_UNDEFINED )
        {
            return null;
        }
        
        return $thumbUrl;
    }
}
