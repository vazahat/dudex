<?php

class EQUESTIONS_CLASS_VideoTools
{
    public static function formatEmbedDimensions( $code, $width, $height )
    {
        if ( !strlen($code) )
            return '';

        //adjust width and height
        $code = preg_replace("/width=(\"|')?[\d]+(px)?(\"|')?/i", 'width=${1}' . $width . '${3}', $code);
        $code = preg_replace("/height=(\"|')?[\d]+(px)?(\"|')?/i", 'height=${1}' . $height . '${3}', $code);

        $code = preg_replace("/width:( )?[\d]+(px)?/i", 'width:' . $width . 'px', $code);
        $code = preg_replace("/height:( )?[\d]+(px)?/i", 'height:' . $height . 'px', $code);

        return $code;
    }

    /**
     * Validate clip code integrity
     *
     * @param string $code
     * @return string
     */
    public static function validateEmbedCode( $code )
    {
        $tags = array('object', 'embed', 'param');

        $objStart = '<object';
        $objEnd = '</object>';
        $objEndS = '/>';

        $posObjStart = stripos($code, $objStart);
        $posObjEnd = stripos($code, $objEnd);

        $posObjEnd = $posObjEnd ? $posObjEnd : stripos($code, $objEndS);

        if ( $posObjStart !== false && $posObjEnd !== false )
        {
            $posObjEnd += strlen($objEnd);
            return substr($code, $posObjStart, $posObjEnd - $posObjStart);
        }
        else
        {
            $embStart = '<embed';
            $embEnd = '</embed>';
            $embEndS = '/>';

            $posEmbStart = stripos($code, $embStart);
            $posEmbEnd = stripos($code, $embEnd) ? stripos($code, $embEnd) : stripos($code, $embEndS);

            if ( $posEmbStart !== false && $posEmbEnd !== false )
            {
                $posEmbEnd += strlen($embEnd);
                return substr($code, $posEmbStart, $posEmbEnd - $posEmbStart);
            }
            else
            {
                $frmStart = '<iframe ';
                $frmEnd = '</iframe>';
                $posFrmStart = stripos($code, $frmStart);
                $posFrmEnd = stripos($code, $frmEnd);
                if ( $posFrmStart !== false && $posFrmEnd !== false )
                {
                    $posFrmEnd += strlen($frmEnd);
                    return substr($code, $posFrmStart, $posFrmEnd - $posFrmStart);
                }
                else
                {
                    return '';
                }
            }
        }
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
        $repl = $code;

        if ( preg_match("/<object/i", $code) )
        {
            $searchPattern = '<param';
            $pos = stripos($code, $searchPattern);
            if ( $pos )
            {
                $addParam = '<param name="' . $name . '" value="' . $value . '"></param><param';
                $repl = substr_replace($code, $addParam, $pos, strlen($searchPattern));
            }
        }

        if ( preg_match("/<embed/i", !empty($repl) ? $repl : $code) )
        {
            $repl = preg_replace("/<embed/i", '<embed ' . $name . '="' . $value . '"', !empty($repl) ? $repl : $code);
        }
        
        $matches = array();
        if ( preg_match("/<iframe[^>]*src=['\"]([^'\"]+)['\"]/i", !empty($repl) ? $repl : $code, $matches) )
        {
            $url = OW::getRequest()->buildUrlQueryString($matches[1], array($name => $value));
            $repl = preg_replace("/(<iframe[^>]*)src=['\"]([^'\"]+)['\"]/i", "$1src=\"".$url."\"", $code);
        }

        return $repl;
    }
    
    public static function addAutoPlay( $code )
    {
        return self::addCodeParam($code, "autoplay", 1);
    }
}
