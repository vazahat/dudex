<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Affiliate service
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
final class OCSAFFILIATES_BOL_Service
{
    /**
     * @var OCSAFFILIATES_BOL_AffiliateDao
     */
    private $affiliateDao;
    
    /**
     * @var OCSAFFILIATES_BOL_AffiliateUserDao
     */
    private $affiliateUserDao;
    
    /**
     * @var OCSAFFILIATES_BOL_BannerDao
     */
    private $bannerDao;
    
    /**
     * @var OCSAFFILIATES_BOL_ClickDao
     */
    private $clickDao;
    
    /**
     * @var OCSAFFILIATES_BOL_PayoutDao
     */
    private $payoutDao;

    /**
     * @var OCSAFFILIATES_BOL_ResetPasswordDao
     */
    private $resetPasswordDao;

    /**
     * @var OCSAFFILIATES_BOL_SaleDao
     */
    private $saleDao;
    
    /**
     * @var OCSAFFILIATES_BOL_SignupDao
     */
    private $signupDao;
    
    /**
     * @var OCSAFFILIATES_BOL_VerificationDao
     */
    private $verificationDao;
    /**
     * @var OCSAFFILIATES_BOL_VisitDao
     */
    private $visitDao;

    const AFFILIATE_ID_SESSION_KEY = 'oxafflogin';
    const AFFILIATE_ID_COOKIE_KEY = 'oxaffid';
    const AFFILIATE_GET_PARAM = 'aid';
    const BANNER_IMG_PREFIX = 'aff_banner_';
    
    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->affiliateDao = OCSAFFILIATES_BOL_AffiliateDao::getInstance();
        $this->affiliateUserDao = OCSAFFILIATES_BOL_AffiliateUserDao::getInstance();
        $this->bannerDao = OCSAFFILIATES_BOL_BannerDao::getInstance();
        $this->clickDao = OCSAFFILIATES_BOL_ClickDao::getInstance();
        $this->payoutDao = OCSAFFILIATES_BOL_PayoutDao::getInstance();
        $this->resetPasswordDao = OCSAFFILIATES_BOL_ResetPasswordDao::getInstance();
        $this->saleDao = OCSAFFILIATES_BOL_SaleDao::getInstance();
        $this->signupDao = OCSAFFILIATES_BOL_SignupDao::getInstance();
        $this->verificationDao = OCSAFFILIATES_BOL_VerificationDao::getInstance();
        $this->visitDao = OCSAFFILIATES_BOL_VisitDao::getInstance();
    }
    
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_Service
     */
    private static $classInstance;

    /**
     * Returns an instance of class
     *
     * @return OCSAFFILIATES_BOL_Service
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    /**
     * Return affiliate by email
     *
     * @param string $email
     * @return OCSAFFILIATES_BOL_Affiliate
     */
    public function findAffiliateByEmail( $email )
    {
        if ( !mb_strlen($email) )
        {
            return false;
        }
        
        return $this->affiliateDao->findByEmail($email);
    }
    
    /**
     * Returns affiliate by id
     *
     * @param int $affiliateId
     * @return OCSAFFILIATES_BOL_Affiliate
     */
    public function findAffiliateById( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return $this->affiliateDao->findById($affiliateId);
    }
    
    /**
     * Registers new affiliate
     *
     * @param OCSAFFILIATES_BOL_Affiliate $affiliate
     * @return int
     */
    public function registerAffiliate( OCSAFFILIATES_BOL_Affiliate $affiliate )
    {
        $this->affiliateDao->save($affiliate);
        
        return $affiliate->id;
    }

    /**
     * Updates affiliate info
     *
     * @param OCSAFFILIATES_BOL_Affiliate $affiliate
     * @return int
     */
    public function updateAffiliate( OCSAFFILIATES_BOL_Affiliate $affiliate )
    {
        $this->affiliateDao->save($affiliate);

        return true;
    }

    /**
     * Updates affiliate activity timestamp
     *
     * @param $affiliateId
     * @return bool
     */
    public function updateAffiliateActivity( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }

        /** @var OCSAFFILIATES_BOL_Affiliate $affiliate */
        $affiliate = $this->affiliateDao->findById($affiliateId);

        if ( !$affiliate )
        {
            return false;
        }

        $affiliate->activityStamp = time();
        $this->affiliateDao->save($affiliate);

        return true;
    }

    /**
     * @param int $affiliateId
     * @return bool
     */
    public function deleteAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        $affiliate = $this->affiliateDao->findById($affiliateId);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        $this->affiliateUserDao->deleteByAffiliateId($affiliateId);
        $this->deleteAffiliateBanners($affiliateId);
        $this->clickDao->deleteByAffiliateId($affiliateId);
        $this->payoutDao->deleteByAffiliateId($affiliateId);
        $this->saleDao->deleteByAffiliateId($affiliateId);
        $this->signupDao->deleteByAffiliateId($affiliateId);
        $this->verificationDao->deleteByAffiliateId($affiliateId);
        $this->resetPasswordDao->deleteByAffiliateId($affiliateId);
        $this->affiliateDao->deleteById($affiliateId);
        
        return true;
    }


    /**
     * Returns affiliate list
     *
     * @param int $offset
     * @param int $limit
     * @param $sortBy
     * @param $sortOrder
     * @return array
     */
    public function getAffiliateList( $offset, $limit, $sortBy, $sortOrder )
    {
        $list = $this->affiliateDao->getList($offset, $limit, $sortBy, $sortOrder);
        
        if ( $list )
        {
            foreach ( $list as &$aff )
            {
                $aff['clickCount'] = (int) $aff['clickCount'];
                $aff['signupCount'] = (int) $aff['signupCount'];
                $aff['saleCount'] = (int) $aff['saleCount'];
                $aff['earnings'] = floatval($aff['earnings']);
                $aff['payouts'] = floatval($aff['payouts']);
                $aff['balance'] = floatval($aff['balance']);
                $aff['url'] = OW::getRouter()->urlForRoute('ocsaffiliates.admin_affiliate', array('affId' => $aff['id']));
            }
        }
        return $list;
    }

    /**
     * @param $affiliateId
     * @return float
     */
    public function getPayoutSum( $affiliateId )
    {
        return floatval($this->payoutDao->getPayoutSum($affiliateId));
    }

    /**
     * @return array
     */
    public function countAffiliates()
    {
        return $this->affiliateDao->countAll();
    }

    /**
     * @return int
     */
    public function countUnverifiedAffiliates()
    {
        return $this->affiliateDao->countUnverified();
    }

    /**
     * @return string
     */
    public function getRemoteAddr()
    {
        return OW_Request::getInstance()->getRemoteAddress();
    }
    
    /**
     * Adds email verification request
     * Sends message with verification link to affiliate
     *
     * @param string $email
     * @return bool
     */
    public function addVerificationRequest( $email )
    {
        if ( !mb_strlen($email) )
        {
            return false;
        }
        
        $affiliate = $this->affiliateDao->findByEmail($email);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        $time = time();
        $code = sha1($email . $time);
        
        $verification = $this->verificationDao->findByAffiliateId($affiliate->id);
        
        if ( !$verification )
        {
            $verification = new OCSAFFILIATES_BOL_Verification();
        }
        
        $verification->affiliateId = $affiliate->id;
        $verification->code = $code;
        $verification->startStamp = $time;
        $verification->expireStamp = $time + 7 * 24 * 60 * 60;
        
        $this->verificationDao->save($verification);
        
        // send email
        $language = OW::getLanguage();
        $url = $this->getVerificationLink($affiliate->id, $code);
        $vars = array('name' => $affiliate->name, 'url' => $url);
        $mail = OW::getMailer()->createMail();
        $mail->addRecipientEmail($email);
        $mail->setSubject($language->text('ocsaffiliates', 'verification_mail_template_subject'));
        $mail->setTextContent($language->text('ocsaffiliates', 'verification_mail_template_content_txt', $vars));
        $mail->setHtmlContent($language->text('ocsaffiliates', 'verification_mail_template_content_html', $vars));
        OW::getMailer()->send($mail);
        
        return true;
    }

    /**
     * @param int $affiliateId
     * @param string $code
     * @return string
     */
    public function getVerificationLink( $affiliateId, $code )
    {
        if ( !$affiliateId || !mb_strlen($code) )
        {
            return null;
        }
        
        return OW::getRouter()->urlForRoute('ocsaffiliates.verify', array('affId' => $affiliateId, 'code' => $code));
    }

    /**
     * @param string $code
     * @return bool
     */
    public function processVerificationCode( $code )
    {
        if ( !mb_strlen($code) )
        {
            return false;
        }
        
        $verification = $this->verificationDao->findByCode($code);
        
        if ( !$verification )
        {
            return false;
        }
        
        if ( $verification->expireStamp < time() )
        {
            return false;
        }

        /** @var OCSAFFILIATES_BOL_Affiliate $affiliate */
        $affiliate = $this->affiliateDao->findById($verification->affiliateId);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        $this->verificationDao->deleteById($verification->id);
        $affiliate->emailVerified = 1;
        $this->affiliateDao->save($affiliate);
        
        return true;
    }

    /**
     * @param $affiliateId
     * @return array
     */
    public function getBannerListForAffiliate( $affiliateId )
    {
        $list = $this->bannerDao->findListByAffiliateId($affiliateId);

        if ( !$list )
        {
            return null;
        }

        $result = array();
        foreach ( $list as $banner )
        {
            $result[$banner->id]['dto'] = $banner;
            $result[$banner->id]['url'] = $this->getBannerUrl($banner->id, $banner->uploadDate, $banner->ext);
        }

        return $result;
    }

    public function getDefaultBannerList()
    {
        return $this->getBannerListForAffiliate(0);
    }

    /**
     * @param $id
     * @return OCSAFFILIATES_BOL_Banner
     */
    public function findBannerById( $id )
    {
        if ( !$id )
        {
            return null;
        }

        return $this->bannerDao->findById($id);
    }

    /**
     * @param $affiliateId
     * @param $file
     * @return bool
     */
    public function addAffiliateBanner( $affiliateId, $file )
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        $banner = new OCSAFFILIATES_BOL_Banner();
        $banner->affiliateId = $affiliateId;
        $banner->ext = $ext;
        $banner->uploadDate = time();

        $this->bannerDao->save($banner);

        $destPath = $this->getBannerDir($banner->id, $banner->uploadDate, $banner->ext);
        $tmpPath = $this->getBannerTmpDir($banner->id, $banner->uploadDate, $banner->ext);

        if ( move_uploaded_file($file['tmp_name'], $tmpPath) )
        {
            $storage = OW::getStorage();
            if ( $storage->copyFile($tmpPath, $destPath) )
            {
                @unlink($tmpPath);

                return true;
            }
        }

        $this->bannerDao->deleteById($banner->id);

        return false;
    }

    /**
     * @param $file
     * @return bool
     */
    public function validateBannerFileType( $file )
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        return in_array($ext, array('jpg', 'jpeg', 'png', 'gif'));
    }

    /**
     * @param $id
     * @param $hash
     * @param $ext
     * @return string
     */
    public function getBannerDir( $id, $hash, $ext )
    {
        $dir = OW::getPluginManager()->getPlugin('ocsaffiliates')->getUserFilesDir();

        return $dir . self::BANNER_IMG_PREFIX . $id . '_' . $hash . '.' . $ext;
    }

    /**
     * @param $id
     * @param $hash
     * @param $ext
     * @return string
     */
    public function getBannerTmpDir( $id, $hash, $ext )
    {
        $dir = OW::getPluginManager()->getPlugin('ocsaffiliates')->getPluginFilesDir();

        return $dir . self::BANNER_IMG_PREFIX . $id . '_' . $hash . '.' . $ext;
    }

    /**
     * @param $id
     * @param $hash
     * @param $ext
     * @return string
     */
    public function getBannerUrl( $id, $hash, $ext )
    {
        $dir = OW::getPluginManager()->getPlugin('ocsaffiliates')->getUserFilesDir();

        $storage = OW::getStorage();

        return $storage->getFileUrl($dir . self::BANNER_IMG_PREFIX . $id . '_' . $hash . '.' . $ext);
    }


    /**
     * @param int $affiliateId
     * @return bool
     */
    public function deleteAffiliateBanners( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        $banners = $this->bannerDao->findListByAffiliateId($affiliateId);
        
        if ( !$banners )
        {
            return true;
        }
        
        foreach ( $banners as $banner )
        {
            $this->deleteBannerById($banner->id);
        }
        
        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteBannerById( $id )
    {
        if ( !$id )
        {
            return false;
        }

        /** @var OCSAFFILIATES_BOL_Banner $banner */
        $banner = $this->bannerDao->findById($id);

        $storage = OW::getStorage();

        $path = $this->getBannerDir($id, $banner->uploadDate, $banner->ext);
        $storage->removeFile($path);
        
        $this->bannerDao->deleteById($id);
        
        return true;
    }

    public function deleteAffiliateUserByUserId( $userId )
    {
        if ( !$userId )
        {
            return false;
        }

        $this->affiliateUserDao->deleteByUserId($userId);

        return true;
    }
    
    /**
     * Checks if affiliate is currently active
     *
     * @param int $affiliateId
     * @return bool
     */
    public function isAffiliateActive( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }

        /** @var OCSAFFILIATES_BOL_Affiliate $affiliate */
        $affiliate = $this->affiliateDao->findById($affiliateId);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        return $affiliate->status == 'active';
    }
    
    /**
     * Checks if affiliate email is verified
     *
     * @param int $affiliateId
     * @return bool
     */
    public function isAffiliateVerified( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        /** @var OCSAFFILIATES_BOL_Affiliate $affiliate */
        $affiliate = $this->affiliateDao->findById($affiliateId);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        return (bool) $affiliate->emailVerified;
    }

    /**
     * @param int $saleId
     * @param float $amount
     * @param int $userId
     * @return bool
     */
    public function registerAffiliateSale( $saleId, $amount, $userId )
    {
        if ( !$saleId || !$amount )
        {
            return false;
        }
        
        $affiliateUser = $this->affiliateUserDao->findByUserId($userId);
        
        if ( !$affiliateUser )
        {
            return false;
        }
        
        $affiliateId = $affiliateUser->affiliateId;
        
        if ( !$this->isAffiliateActive($affiliateId) )
        {
            return false;
        }
        
        $config = OW::getConfig();
        $commissionType = $config->getValue('ocsaffiliates', 'sale_commission');
        
        switch ( $commissionType )
        {
            case 'percent':
                $commission = $amount * floatval($config->getValue('ocsaffiliates', 'sale_percent')) / 100;
                break;
                
            case 'amount':
                $commission = floatval($config->getValue('ocsaffiliates', 'sale_amount'));
                break;

            default:
                $commission = 0;
        }
        
        $sale = new OCSAFFILIATES_BOL_Sale();
        $sale->affiliateId = $affiliateId;
        $sale->saleAmount = $amount;
        $sale->bonusAmount = $commission;
        $sale->saleId = $saleId;
        $sale->saleDate = time();
        
        $this->saleDao->save($sale);
        
        return true;
    }

    /**
     * @param int $affiliateId
     * @return array
     */
    public function getPayoutListForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        $list = $this->payoutDao->findListByAffiliateId($affiliateId);
        foreach ( $list as &$item )
        {
            $item->amount = floatval($item->amount);
        }

        return $list;
    }

    /**
     * @param OCSAFFILIATES_BOL_Payout $payout
     */
    public function registerPayout( OCSAFFILIATES_BOL_Payout $payout )
    {
        $this->payoutDao->save($payout);
    }

    /**
     * @param $payoutId
     * @return bool
     */
    public function deletePayoutById( $payoutId )
    {
        if ( !$payoutId )
        {
            return false;
        }

        $this->payoutDao->deleteById($payoutId);

        return true;
    }
    
    /**
     * Logins affiliate by Id
     *
     * @param int $id
     * @return bool
     */
    public function loginAffiliateById( $id )
    {
        if ( !$id )
        {
            return false;
        }
        
        OW::getSession()->set(self::AFFILIATE_ID_SESSION_KEY, $id);

        return true;
    }
    
    /**
     * Logouts affiliate
     */
    public function logoutAffiliate()
    {
        OW::getSession()->delete(self::AFFILIATE_ID_SESSION_KEY);
    }

    /**
     * Checks if current affiliate is authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return ( OW::getSession()->isKeySet(self::AFFILIATE_ID_SESSION_KEY) && $this->getAffiliateId() > 0 );
    }

    /**
     * Returns current affiliate id.
     *
     * @return integer
     */
    public function getAffiliateId()
    {
        return (int) OW::getSession()->get(self::AFFILIATE_ID_SESSION_KEY);
    }

    /**
     * @return bool
     */
    public function checkAccess( )
    {
        if ( !$this->isAuthenticated() )
        {
            return false;
        }
        
        $affiliateId = $this->getAffiliateId();
        $affiliate = $this->findAffiliateById($affiliateId);
        
        if ( !$affiliate )
        {
            return false;
        }
        
        if ( !$affiliate->emailVerified )
        {
            return false;
        }
        
        return true;
    }

    /**
     * @param integer $affiliateId
     * @return OCSAFFILIATES_BOL_ResetPassword
     */
    public function findResetPasswordByAffiliateId( $affiliateId )
    {
        return $this->resetPasswordDao->findByAffiliateId($affiliateId);
    }

    /**
     * @param integer $affiliateId
     * @return OCSAFFILIATES_BOL_ResetPassword
     */
    public function getNewResetPassword( $affiliateId )
    {
        $resetPassword = $this->findResetPasswordByAffiliateId($affiliateId);

        if ( !$resetPassword )
        {
            $resetPassword = new OCSAFFILIATES_BOL_ResetPassword();
            $resetPassword->affiliateId = $affiliateId;
        }

        $resetPassword->expirationTimeStamp = time() + 24 * 3600;
        $resetPassword->code = md5(UTIL_String::generatePassword(8, 5));

        $this->resetPasswordDao->save($resetPassword);

        return $resetPassword;
    }

    /**
     * @param string $code
     * @return OCSAFFILIATES_BOL_ResetPassword
     */
    public function findResetPasswordByCode( $code )
    {
        return $this->resetPasswordDao->findByCode($code);
    }

    public function deleteExpiredResetPasswordCodes()
    {
        $this->resetPasswordDao->deleteExpiredEntities();
    }

    /**
     * @param $resetCodeId
     */
    public function deleteResetCode( $resetCodeId )
    {
        $this->resetPasswordDao->deleteById($resetCodeId);
    }

    /**
     * @param int $affiliateId
     * @return int
     */
    public function countClicksForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return (int) $this->clickDao->countByAffiliateId($affiliateId);
    }

    /**
     * @param int $affiliateId
     * @return float
     */
    public function getClicksSumForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return floatval($this->clickDao->getSumByAffiliateId($affiliateId));
    }

    /**
     * @param int $affiliateId
     * @return int
     */
    public function countRegistrationsForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return (int) $this->signupDao->countByAffiliateId($affiliateId);
    }

    public function findAffiliateSignupById( $id )
    {
        return $this->signupDao->findById($id);
    }

    /**
     * @param int $affiliateId
     * @return float
     */
    public function getRegistrationsSumForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return floatval($this->signupDao->getSumByAffiliateId($affiliateId));
    }

    /**
     * @param int $affiliateId
     * @return int
     */
    public function countSalesForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return (int) $this->saleDao->countByAffiliateId($affiliateId);
    }

    public function findAffiliateSaleById( $id )
    {
        return $this->saleDao->findById($id);
    }

    /**
     * @param int $affiliateId
     * @return float
     */
    public function getSalesSumForAffiliate( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }
        
        return floatval($this->saleDao->getSumByAffiliateId($affiliateId));
    }

    public function getPeriodConfig()
    {
        return OW::getConfig()->getValue('ocsaffiliates', 'period') * 60 * 60;
    }

    /**
     * @param null $index
     * @return array
     */
    public function explodeCookieVar( $index = null )
    {
        if ( !isset($_COOKIE[self::AFFILIATE_ID_COOKIE_KEY]) )
        {
            return null;
        }

        $arr = explode('|' , $_COOKIE[self::AFFILIATE_ID_COOKIE_KEY]);

        if ( isset($arr[0]) )
        {
            $arr[0] = (int) $arr[0];
        }

        if ( isset($arr[1]) )
        {
            $arr[1] = (int) $arr[1];
        }

        return $index !== null ? $arr[$index] : $arr;
    }

    /**
     * @param $affiliateId
     * @param $type
     */
    public function setCookieData( $affiliateId, $type )
    {
        setcookie(self::AFFILIATE_ID_COOKIE_KEY, (int) $affiliateId . '|' . (int) $type, time() + $this->getPeriodConfig(), '/');
    }

    public function catchAffiliateVisit()
    {
        $affiliateId = !empty($_GET[self::AFFILIATE_GET_PARAM]) ? (int) $_GET[self::AFFILIATE_GET_PARAM] : null;

        // check if data is already in cookies
        if ( !empty($_COOKIE[self::AFFILIATE_ID_COOKIE_KEY]) )
        {
            $type = $this->explodeCookieVar(1);
            if ( $type & 1 )
            {
                return;
            }

            $visit = $this->visitDao->findLastVisitFromIp($this->getRemoteAddr());

            // check visit period and type
            if ( $visit && ($visit->type & 1) && time() - $visit->timestamp < $this->getPeriodConfig() )
            {
                return;
            }

            if ( !$visit ) // register click
            {
                $affiliateId = $this->explodeCookieVar(0);
                $this->trackClick($affiliateId);
                $this->setCookieData($affiliateId, 1);

                $visit = new OCSAFFILIATES_BOL_Visit();
                $visit->ipAddress = ip2long($this->getRemoteAddr());
                $visit->timestamp = time();
                $visit->type = 1;
                $this->visitDao->save($visit);

                return;
            }
            else
            {
                if ( time() - $visit->timestamp > $this->getPeriodConfig() )
                {
                    $affiliateId = $this->explodeCookieVar(0);
                    $this->trackClick($affiliateId);
                    $this->setCookieData($affiliateId, 1);

                    $visit->type = 1;
                    $visit->timestamp = time();
                    $this->visitDao->save($visit);

                }
                elseif ( !($visit->type & 1) )
                {
                    $affiliateId = $this->explodeCookieVar(0);
                    $this->trackClick($affiliateId);
                    $this->setCookieData($affiliateId, $visit->type + 1);

                    $visit->type = $visit->type + 1;
                    $visit->timestamp = time();
                    $this->visitDao->save($visit);
                }
            }
        }
        elseif ( $affiliateId ) // set affiliate data to cookies
        {
            $url = OW::getRequest()->buildUrlQueryString(null, array(self::AFFILIATE_GET_PARAM => null));
            $this->setCookieData($affiliateId, 0);

            OW::getApplication()->redirect($url);
        }
    }

    /**
     * @param $userId
     * @return bool
     */
    public function catchAffiliateSignup( $userId )
    {
        $affiliateId = (int) $this->explodeCookieVar(0);

        if ( !$affiliateId || !$this->isAffiliateActive($affiliateId) )
        {
            return false;
        }

        $type = (int) $this->explodeCookieVar(1);

        if ( !($type & 2) )
        {
            $visit = $this->visitDao->findLastVisitFromIp($this->getRemoteAddr());
            if ( $visit && $visit->type & 2 )
            {
                return false;
            }

            if ( true /* time() - $visit->timestamp > $this->getPeriodConfig()*/ )
            {
                $amount = floatval(OW::getConfig()->getValue('ocsaffiliates', 'reg_amount'));
                $signup = new OCSAFFILIATES_BOL_Signup();
                $signup->affiliateId = $affiliateId;
                $signup->bonusAmount = $amount;
                $signup->userId = $userId;
                $signup->signupDate = time();

                $this->signupDao->save($signup);

                $affUser = new OCSAFFILIATES_BOL_AffiliateUser();
                $affUser->userId = $userId;
                $affUser->affiliateId = $affiliateId;
                $affUser->timestamp = time();

                $this->affiliateUserDao->save($affUser);

                $this->setCookieData($affiliateId, $type + 2);

                $visit->type = $visit->type + 2;
                $visit->timestamp = time();
                $this->visitDao->save($visit);

                return true;
            }
        }

        return false;
    }

    /**
     * @param $affiliateId
     * @return bool
     */
    public function trackClick( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return false;
        }

        $affiliate = $this->findAffiliateById($affiliateId);

        if ( !$affiliate || !$this->isAffiliateActive($affiliateId) )
        {
            return false;
        }

        $amount = floatval(OW::getConfig()->getValue('ocsaffiliates', 'click_amount'));

        $click = new OCSAFFILIATES_BOL_Click();
        $click->affiliateId = $affiliateId;
        $click->clickDate = time();
        $click->bonusAmount = $amount;
        $this->clickDao->save($click);

        $visit = $this->visitDao->findLastVisitFromIp($this->getRemoteAddr());
        if ( $visit )
        {
            $visit->timestamp = time();
            $this->visitDao->save($visit);
        }

        return true;
    }

    /**
     * @param $userId
     * @param $saleId
     * @param $amount
     * @return bool
     */
    public function trackSale( $userId, $saleId, $amount )
    {
        if ( !$userId || !$saleId || !$amount )
        {
            return false;
        }

        // get user affiliate
        $affUser = $this->affiliateUserDao->findByUserId($userId);

        if ( !$affUser )
        {
            return false;
        }

        $affiliate = $this->affiliateDao->findById($affUser->affiliateId);

        if ( !$affiliate || !$this->isAffiliateActive($affiliate->id) )
        {
            return false;
        }

        $commission = $this->getSaleCommission($amount);

        $sale = new OCSAFFILIATES_BOL_Sale();
        $sale->affiliateId = $affiliate->id;
        $sale->saleId = $saleId;
        $sale->saleAmount = $amount;
        $sale->bonusAmount = $commission;
        $sale->saleDate = time();

        $this->saleDao->save($sale);

        return true;
    }

    /**
     * @param $amount
     * @return float
     */
    public function getSaleCommission( $amount )
    {
        if ( !$amount )
        {
            return 0;
        }

        $config = OW::getConfig();
        $type = $config->getValue('ocsaffiliates', 'sale_commission');

        $bonusAmount = 0;
        switch ( $type )
        {
            case 'percent':
                $bonusAmount = floatval($config->getValue('ocsaffiliates', 'sale_percent')) * $amount / 100;
                break;

            case 'amount':
                $bonusAmount = floatval($config->getValue('ocsaffiliates', 'sale_amount'));
                break;
        }

        if ( $bonusAmount > $amount )
        {
            return 0;
        }

        return $bonusAmount;
    }

    /**
     * @param $limit
     * @return array
     */
    public function getUntrackedSales( $limit )
    {
        return $this->saleDao->getUntrackedSales($limit);
    }

    public function processUntrackedSales()
    {
        $sales = $this->getUntrackedSales(10);

        if ( $sales )
        {
            foreach ( $sales as $sale )
            {
                $this->trackSale($sale->userId, $sale->id, $sale->totalAmount);
            }
        }
    }

    /**
     * @param $affiliateId
     * @param $start
     * @param $end
     * @return array
     */
    public function getAffiliateEarningForPeriod( $affiliateId, $start, $end )
    {
        if ( !$affiliateId )
        {
            return null;
        }

        $stat = $this->clickDao->getAffiliateEarningStat($affiliateId, $start, $end);

        $result = array();
        $period = 24 * 60 * 60;
        $timestamp = $start;

        while ( $timestamp < $end )
        {
            $pStart = $timestamp;
            $pEnd = $timestamp + $period;
            $result[$timestamp] = $this->getStatForTimestamps($pStart, $pEnd, $stat);
            $timestamp += $period;
        }

        return $result;
    }

    private function getStatForTimestamps( $start, $end, $stat )
    {
        if ( !$stat )
        {
            return array('count' => 0, 'sum' => 0);
        }

        $count = 0;
        $sum = 0.0;
        foreach ( $stat as $data )
        {
            if ( $data['timestamp'] > $start && $data['timestamp'] < $end )
            {
                $count += 1;
                $sum += $data['bonusAmount'];
            }
        }

        return array('count' => $count, 'sum' => $sum);
    }

    /**
     * @param $stat
     * @return float
     */
    public function getAffiliateEarningMax( $stat )
    {
        if ( !count($stat) )
        {
            return 10;
        }

        $first = array_shift($stat);
        $max = $first['sum'];

        foreach ( $stat as $point )
        {
            if ( $point['sum'] > $max )
            {
                $max = $point['sum'];
            }
        }

        return ceil($max * 1.1);
    }

    /**
     * @param $affiliateId
     */
    public function generateTestData( $affiliateId )
    {
        $start = strtotime('-3 month');
        $end = strtotime('+1 month');

        $ts = $start;
        $period = 24 * 60 * 60;

        while ( $ts < $end )
        {
            $number = rand(10, 50);

            for ( $i = 0; $i < $number; $i++ )
            {
                $click = new OCSAFFILIATES_BOL_Click();
                $click->affiliateId = $affiliateId;
                $click->bonusAmount = 0.05;
                $click->clickDate = $ts;
                $this->clickDao->save($click);

                $signup = new OCSAFFILIATES_BOL_Signup();
                $signup->affiliateId = $affiliateId;
                $signup->bonusAmount = 0.1;
                $signup->signupDate = $ts;
                $signup->userId = 1;
                $this->signupDao->save($signup);

                $sale = new OCSAFFILIATES_BOL_Sale();
                $sale->affiliateId = $affiliateId;
                $sale->saleAmount = rand(5, 30);
                $sale->bonusAmount = $sale->saleAmount * 0.1;
                $sale->saleDate = $ts;
                $sale->saleId = 1;
                $this->saleDao->save($sale);
            }

            $ts += $period;
        }

    }

    public function getSortFields()
    {
        return array(
            'name', 'registerStamp', 'status',
            'clickCount', 'signupCount', 'saleCount',
            'earnings', 'payouts', 'balance'
        );
    }

    /**
     * @param $affiliateId
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getAffiliateEventsLog( $affiliateId, $offset, $limit )
    {
        if ( !$affiliateId )
        {
            return array();
        }

        $log = $this->clickDao->getEventsLog($affiliateId, $offset, $limit);

        if ( !$log )
        {
            return array();
        }

        $res = array();
        $billingService = BOL_BillingService::getInstance();
        $userService = BOL_UserService::getInstance();
        foreach ( $log as $key => $event )
        {
            if ( $event['type'] == 'sale' )
            {
                $affSale = $this->findAffiliateSaleById($event['id']);
                $sale = $billingService->getSaleById($affSale->saleId);
                if ( $sale )
                {
                    $event['details'] = $sale->entityDescription;
                    $displayName = $userService->getDisplayName($sale->userId);
                    $url = $userService->getUserUrl($sale->userId);
                    $event['user'] = '<a href="'.$url.'">' . $displayName . '</a>';
                    $event['amount'] = floatval($sale->totalAmount);
                }
            }
            elseif ( $event['type'] == 'signup' )
            {
                $affSignup = $this->findAffiliateSignupById($event['id']);
                $displayName = $userService->getDisplayName($affSignup->userId);
                $url = $userService->getUserUrl($affSignup->userId);
                $event['details'] = '<a href="'.$url.'">' . $displayName . '</a>';
            }
            $res[$key] = $event;
        }

        return $res;
    }

    /**
     * @param $affiliateId
     * @return int
     */
    public function countAffiliateEventsLog( $affiliateId )
    {
        $clickEvents = $this->clickDao->countByAffiliateId($affiliateId);
        $signupEvents = $this->signupDao->countByAffiliateId($affiliateId);
        $saleEvents = $this->saleDao->countByAffiliateId($affiliateId);

        return $clickEvents + $signupEvents + $saleEvents;
    }

    public function getAffiliateAssocUser( $affiliateId )
    {
        if ( !$affiliateId )
        {
            return null;
        }

        /** @var OCSAFFILIATES_BOL_Affiliate $aff */
        $aff = $this->affiliateDao->findById($affiliateId);

        if ( !$aff || !$aff->userId )
        {
            return null;
        }

        $userService = BOL_UserService::getInstance();
        $user = $userService->findUserById($aff->userId);

        if ( !$user )
        {
            return null;
        }

        return array(
            'id' => $user->id,
            'name' => $userService->getDisplayName($user->id),
            'url' => $userService->getUserUrl($user->id)
        );
    }

    public function findAffiliateByAssocUser( $userId )
    {
        if ( !$userId )
        {
            return null;
        }

        return $this->affiliateDao->findByUserId($userId);
    }
}