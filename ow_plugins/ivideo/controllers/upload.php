<?php

/**
 * PHP Real Ajax Uploader
 * Copyright @Alban Xhaferllari
 * albanx@gmail.com
 * www.albanx.com
 */
class IVIDEO_CTRL_Upload extends OW_ActionController {

    public function action() {
        error_reporting(E_ALL ^ E_NOTICE); //remove notice for json invalidation

        /*         * ***************************************************************************
         * EMAIL CONFIGURATION HERE
         * *************************************************************************** */
        $send_email = false;      //Enable email notification
        $main_receiver = 'test@gmail.com';    //Who receive the email when files get uploaded
        $cc = '';       //Other email that receive the email in CC
        $from = 'from@ajaxupload.com';  //What should appear in the from bar, usually something like fromupload@mysite.com
        /*         * ************************************************************************** */

        $uploadPath = isset($_REQUEST['ax-file-path']) ? $_REQUEST['ax-file-path'] : '';
        $fileName = isset($_REQUEST['ax-file-name']) ? $_REQUEST['ax-file-name'] : '';
        $currByte = isset($_REQUEST['ax-start-byte']) ? $_REQUEST['ax-start-byte'] : 0;
        $maxFileSize = isset($_REQUEST['ax-maxFileSize']) ? $_REQUEST['ax-maxFileSize'] : '4M';
        $html5fsize = isset($_REQUEST['ax-fileSize']) ? $_REQUEST['ax-fileSize'] : '';
        $isLast = isset($_REQUEST['isLast']) ? $_REQUEST['isLast'] : false;

        //if set generates thumbs only on images type files
        $thumbHeight = isset($_REQUEST['ax-thumbHeight']) ? $_REQUEST['ax-thumbHeight'] : 0;
        $thumbWidth = isset($_REQUEST['ax-thumbWidth']) ? $_REQUEST['ax-thumbWidth'] : '';
        $thumbPostfix = isset($_REQUEST['ax-thumbPostfix']) ? $_REQUEST['ax-thumbPostfix'] : '_thumb';
        $thumbPath = isset($_REQUEST['ax-thumbPath']) ? $_REQUEST['ax-thumbPath'] : '';
        $thumbFormat = isset($_REQUEST['ax-thumbFormat']) ? $_REQUEST['ax-thumbFormat'] : 'png';

        //$allowExt	= (empty($_REQUEST['ax-allow-ext']))?array():explode('|', $_REQUEST['ax-allow-ext']);
        $uploadPath = OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir();

        if (!file_exists($uploadPath) && !empty($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!file_exists($thumbPath) && !empty($thumbPath)) {
            mkdir($thumbPath, 0777, true);
        }

        ini_set('memory_limit', -1);

        if (isset($_FILES['ax-files'])) {
            //for eahc theorically runs only 1 time, since i upload i file per time
            foreach ($_FILES['ax-files']['error'] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $newName = !empty($fileName) ? $fileName : $_FILES['ax-files']['name'][$key];
                    $fullPath = $this->checkFilename($newName, $_FILES['ax-files']['size'][$key]);

                    if ($fullPath) {
                        move_uploaded_file($_FILES['ax-files']['tmp_name'][$key], $fullPath);
                        if (!empty($thumbWidth) || !empty($thumbHeight))
                            $this->createThumbGD($fullPath, $thumbPath, $thumbPostfix, $thumbWidth, $thumbHeight, $thumbFormat);

                        if ($send_email)
                            $this->send_notification($main_receiver, $cc, $fullPath, $from);

                        OW::getSession()->set('ivideo.filename', basename($fullPath));
                        echo json_encode(array('name' => basename($fullPath), 'size' => filesize($fullPath), 'status' => 1, 'info' => 'File uploaded'));
                    }
                }
                else {
                    echo json_encode(array('name' => basename($_FILES['ax-files']['name'][$key]), 'size' => $_FILES['ax-files']['size'][$key], 'status' => -1, 'info' => $error));
                }
            }
        } elseif (isset($_FILES['Filedata'])) { //flash upload
            if ($_FILES['Filedata']['error'] == UPLOAD_ERR_OK) {
                $newName = !empty($fileName) ? $fileName : $_FILES['Filedata']['name'];

                $fullPath = $this->checkFilename($newName, $_FILES['Filedata']['size']);

                $result = move_uploaded_file($_FILES['Filedata']['tmp_name'], $fullPath);
                if ($result) {
                    OW::getSession()->set('ivideo.filename', basename($fullPath));
                    echo json_encode(array('name' => basename($fullPath), 'size' => filesize($fullPath), 'status' => 1, 'info' => 'File uploaded'));
                } else {
                    echo json_encode(array('name' => basename($_FILES['Filedata']['name']), 'size' => $_FILES['Filedata']['size'], 'status' => -1, 'info' => 'Cannot move file'));
                }
            } else {
                echo json_encode(array('name' => basename($_FILES['Filedata']['name']), 'size' => $_FILES['Filedata']['size'], 'status' => -1, 'info' => $_FILES['Filedata']['error']));
            }
        } elseif (isset($_REQUEST['ax-file-name'])) {
            //check only the first peice
            $fullPath = ($currByte != 0) ? $uploadPath . $fileName : $this->checkFilename($fileName, $html5fsize);

            if ($fullPath) {

                $flag = ($currByte == 0) ? 0 : FILE_APPEND;
                $receivedBytes = file_get_contents('php://input');
                //strange bug on very fast connections like localhost, some times cant write on file
                //TODO future version save parts on different files and then make join of parts
                while (@file_put_contents($fullPath, $receivedBytes, $flag) === false) {
                    usleep(50);
                }

                if ($isLast == 'true') {
                    $this->createThumbGD($fullPath, $thumbPath, $thumbPostfix, $thumbWidth, $thumbHeight, $thumbFormat);
                    if ($send_email)
                        $this->send_notification($main_receiver, $cc, $fullPath, $from);
                }
                OW::getSession()->set('ivideo.filename', basename($fullPath));
                echo json_encode(array('name' => basename($fullPath), 'size' => $currByte, 'status' => 1, 'info' => 'File/chunk uploaded'));
            }
        }
        die();
    }

    //with gd library

    function createThumbGD($filepath, $thumbPath, $postfix, $maxwidth, $maxheight, $format = 'jpg', $quality = 75) {
        if ($maxwidth <= 0 && $maxheight <= 0) {
            return 'No valid width and height given';
        }

        $gd_formats = array('jpg', 'jpeg', 'png', 'gif'); //web formats
        $file_name = pathinfo($filepath);
        if (empty($format))
            $format = $file_name['extension'];

        if (!in_array(strtolower($file_name['extension']), $gd_formats)) {
            return false;
        }

        $thumb_name = $file_name['filename'] . $postfix . '.' . $format;

        if (empty($thumbPath)) {
            $thumbPath = $file_name['dirname'];
        }
        $thumbPath.= (!in_array(substr($thumbPath, -1), array('\\', '/')) ) ? DIRECTORY_SEPARATOR : ''; //normalize path
        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($filepath);
        if ($width_orig > 0 && $height_orig > 0) {
            $ratioX = $maxwidth / $width_orig;
            $ratioY = $maxheight / $height_orig;
            $ratio = min($ratioX, $ratioY);
            $ratio = ($ratio == 0) ? max($ratioX, $ratioY) : $ratio;
            $newW = $width_orig * $ratio;
            $newH = $height_orig * $ratio;

            // Resample
            $thumb = imagecreatetruecolor($newW, $newH);
            $image = imagecreatefromstring(file_get_contents($filepath));

            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newW, $newH, $width_orig, $height_orig);

            // Output
            switch (strtolower($format)) {
                case 'png':
                    imagepng($thumb, $thumbPath . $thumb_name, 9);
                    break;

                case 'gif':
                    imagegif($thumb, $thumbPath . $thumb_name);
                    break;

                default:
                    imagejpeg($thumb, $thumbPath . $thumb_name, $quality);
                    ;
                    break;
            }
            imagedestroy($image);
            imagedestroy($thumb);
        } else {
            return false;
        }
    }

    //for image magick
    function createThumbIM($filepath, $thumbPath, $postfix, $maxwidth, $maxheight, $format) {
        $file_name = pathinfo($filepath);
        $thumb_name = $file_name['filename'] . $postfix . '.' . $format;

        if (empty($thumbPath)) {
            $thumbPath = $file_name['dirname'];
        }
        $thumbPath.= (!in_array(substr($thumbPath, -1), array('\\', '/')) ) ? DIRECTORY_SEPARATOR : ''; //normalize path

        $image = new Imagick($filepath);
        $image->thumbnailImage($maxwidth, $maxheight);
        $images->writeImages($thumbPath . $thumb_name);
    }

    function checkFilename($fileName, $size) {
        //global $allowExt, $uploadPath, $maxFileSize;
        $maxFileSize = isset($_REQUEST['ax-maxFileSize']) ? $_REQUEST['ax-maxFileSize'] : '4M';
        //$allowExt	= (empty($_REQUEST['ax-allow-ext']) || !isset($_REQUEST['ax-allow-ext']) )?array():explode('|', $_REQUEST['ax-allow-ext']);
        $uploadPath = OW::getPluginManager()->getPlugin('ivideo')->getUserFilesDir();
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        //------------------max file size check from js
        $maxsize_regex = preg_match("/^(?'size'[\\d]+)(?'rang'[a-z]{0,1})$/i", $maxFileSize, $match);
        $maxSize = 4 * 1024 * 1024; //default 4 M
        if ($maxsize_regex && is_numeric($match['size'])) {
            switch (strtoupper($match['rang'])) {//1024 or 1000??
                case 'K': $maxSize = $match[1] * 1024;
                    break;
                case 'M': $maxSize = $match[1] * 1024 * 1024;
                    break;
                case 'G': $maxSize = $match[1] * 1024 * 1024 * 1024;
                    break;
                case 'T': $maxSize = $match[1] * 1024 * 1024 * 1024 * 1024;
                    break;
                default: $maxSize = $match[1]; //default 4 M
            }
        }

        if (!empty($maxFileSize) && $size > $maxSize) {
            echo json_encode(array('name' => $fileName, 'size' => $size, 'status' => -1, 'info' => 'File size not allowed.'));
            return false;
        }
        //-----------------End max file size check
        //comment if not using windows web server
        $windowsReserved = array('CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
            'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9');
        $badWinChars = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));

        $fileName = str_replace($badWinChars, '', $fileName);
        $fileInfo = pathinfo($fileName);
        $fileExt = strtolower($fileInfo['extension']);
        $fileBase = $fileInfo['filename'];

        //check if legal windows file name
        if (in_array($fileName, $windowsReserved)) {
            echo json_encode(array('name' => $fileName, 'size' => 0, 'status' => -1, 'info' => 'File name not allowed. Windows reserverd.'));
            return false;
        }

        //check if is allowed extension

        if (!in_array($fileExt, explode(",", OW::getConfig()->getValue('ivideo', 'allowedExtensions'))) && count(explode(",", OW::getConfig()->getValue('ivideo', 'allowedExtensions')))) {
            echo json_encode(array('name' => $fileName, 'size' => 0, 'status' => -1, 'info' => "Extension [$fileExt] not allowed."));
            return false;
        }

        $fullPath = $uploadPath . $fileName;

        $c = 0;
        while (file_exists($fullPath)) {
            $c++;
            $fileName = $fileBase . "($c)." . $fileExt;
            $fullPath = $uploadPath . $fileName;
        }
        return $fullPath;
    }

    function send_notification($main_receiver, $cc = '', $file_path, $from = 'ajax@uploader') {
        $msg = '<p> New file uploaded to your site at ' . date('Y-m-i H:i') . ' from IP ' . $_SERVER['REMOTE_ADDR'] . ':</p>';
        $msg.= '<div style="overflow:auto;padding:10px;border:1px solid black;border-radius:5px;">';
        $msg.= $file_path;
        $msg.= '</div>';


        $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n";
        $headers .= 'Cc: ' . $cc . "\r\n";
        $headers .= "Content-type: text/html\r\n";

        @mail($main_receiver, 'New file uploaded', $msg, $headers);
    }

}