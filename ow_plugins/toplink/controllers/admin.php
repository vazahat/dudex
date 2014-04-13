<?php
	class TOPLINK_CTRL_Admin extends ADMIN_CTRL_Abstract{
		
		public $iconDir;
		private $iMagicInstalled;
		
		private $myService;
		
		public function __construct(){
			parent::__construct();
			
			$this->setPageTitle($this->text('toplink', 'index_page_title'));
			$this->setPageHeading($this->text('toplink', 'index_page_title'));
			$this->setPageHeadingIconClass('ow_ic_star');
			
			$this->iconDir = BOL_AvatarService::getInstance()->getAvatarsDir();
			
			OW::getNavigation()->activateMenuItem('admin_plugins', 'admin', 'sidebar_menu_plugins_installed');
			
			$this->myService = TOPLINK_BOL_Service::getInstance();
		}
		
		public function index(){}
		
		public function toplinklist( $curr = null ){
			OW::getDocument()->addStyleSheet( OW::getPluginManager()->getPlugin('toplink')->getStaticCssUrl().'style.css' );
			OW::getDocument()->addScript( OW::getPluginManager()->getPlugin('toplink')->getStaticJsUrl().'toplink.js' );
			
			$currId = @$curr['id'];
			$topForm = new Form("topForm");
			$topForm->setEnctype('multipart/form-data');
			$topSubmit = new Submit("topSubmit");
			$topForm->addElement( $topSubmit );
			
			$topName = new TextField("topName");
			$topUrl = new TextField("topUrl");
			$topIcon = new TextField("topIcon");
			$topId = new HiddenField("topId");
			
			$uploadIcon = new FileField('topIconFile');
			$uploadIcon->setLabel( $this->text( 'toplink', 'new_icon' ) );
			$topOrder = new TextField('toporder');
			$topTarget = new CheckboxField('toptarget');
			
			$topPermission = new CheckboxGroup('toppermission');
			$topPermission->setColumnCount( 1 );
			$topPermission->setLabel( $this->text( 'toplink', 'new_permission' ) );
			$availableDesc = TOPLINK_BOL_Service::$visibility;
			$topPermission->addOptions( $availableDesc );
			
			$topOrder->setLabel( $this->text( 'toplink', 'new_order' ) );
			$topOrder->setRequired();
			$topTarget->setLabel( $this->text( 'toplink', 'new_target' ) );
			$topName->setLabel( $this->text( 'toplink', 'new_name' ) );
			//$topName->setRequired();
			$topUrl->setLabel( $this->text( 'toplink', 'new_url' ) );
			$topUrl->setRequired();
			$topIcon->setLabel( $this->text( 'toplink', 'new_icon' ) );
			
			if( !empty( $currId ) && !OW::getRequest()->isPost() ){
				$theTopLink = $this->myService->getTopLinkById( $currId );
				
				$topName->setValue( $theTopLink->itemname );
				$topId->setValue( $currId );
				$topUrl->setValue( $theTopLink->url );
				$topIcon->setValue( $theTopLink->icon );
				$topTarget->setValue( $theTopLink->target );
				$topOrder->setValue( $theTopLink->order );
				
				$theTopLinkChild = $this->myService->getTopLinkChildObjectByParentId( $currId );
				
				$theTopLinkPermission = $this->myService->getTopLinkPermissionById( $currId );
				if( !empty( $theTopLinkPermission ) ){
					$i = 1;
					foreach( $theTopLinkPermission as $topLinkPermission ){
						$permissionOption[$i] = $topLinkPermission->availablefor;
						$i++;
					}
					$topPermission->setValue( $permissionOption );
				}
			}
			
			$topForm->addElement( $topName );
			$topForm->addElement( $topUrl );
			$topForm->addElement( $topIcon );
			$topForm->addElement( $topId );
			$topForm->addElement( $topTarget );
			$topForm->addElement( $topOrder );
			$topForm->addElement( $uploadIcon );
			$topForm->addElement( $topPermission );
			$this->addForm( $topForm );
			
			/* --- form submit --- */
			$childrenNameList = @$_REQUEST['menuchildname'];
			$childrenUrlList = @$_REQUEST['menuchildurl'];
			$childrenIDList = @$_REQUEST['menuchildid'];
			if( OW::getRequest()->isPost() ){
				if( $topForm->isValid( $_POST ) ){
					
					$fdata = $topForm->getValues();
					$newtoplink = new TOPLINK_BOL_Toplink();
					$newtoplink->id = $fdata['topId'];
					$newtoplink->itemname = $fdata['topName'];
					
					$theurl = $fdata['topUrl'];
					if( !empty( $theurl ) ){
						$theurl = preg_match( "/^http/",$theurl ) ? $theurl : "http://".$theurl;
					}else{
						$theurl = "#";
					}
					
					$newtoplink->url = $theurl;
					
					/* check file exist */
					if( !empty( $fdata['topIcon'] ) && preg_match( "/^\//",$fdata['topIcon'] ) ){
						$newtoplink->icon = $fdata['topIcon'];
						$iconFileName = preg_replace( "/^\//" , "" , $newtoplink->icon );
						if( !file_exists( $this->iconDir . $iconFileName ) ){
							$newtoplink->icon = null;
						}
					}
					/* end */
					
					$newtoplink->target = $fdata['toptarget'];
					$newtoplink->order = empty( $fdata['toporder'] ) ? 5 : $fdata['toporder'];
					
					$loadedExts = get_loaded_extensions();
					if( in_array( 'imagick', $loadedExts ) ){
						$this->iMagicInstalled = true;
					}
					
					if( $_FILES['topIconFile']['error'] == 0 ){
						$ext = explode( '.', $_FILES['topIconFile']['name'] );
						$ext = end( $ext );
						
						if( $this->iMagicInstalled ){
							$image = new Imagick( $_FILES['topIconFile']['tmp_name'] );
							$image->thumbnailImage( 16, 0);
							
							file_put_contents( $this->iconDir . $_FILES['topIconFile']['name'].'.png', $image );
							
							$uploadresult = $_FILES['topIconFile']['name'].'.png';
						}else{
							try{
								$image = new UTIL_Image($_FILES['topIconFile']['tmp_name'], 'PNG');
								$image->resizeImage(16, 16, false)
									->saveImage( $this->iconDir . $_FILES['topIconFile']['name'].'.png' );
								
								$uploadresult = $_FILES['topIconFile']['name'].'.png';
							}catch(Exception $e){
								$uploadresult = null;
							}
						}
						
						if( $uploadresult ){
							$newtoplink->icon = "/".$uploadresult;
						}
						
						/* check file exist AGAIN AFTER UPLOAD */
						if( $newtoplink->icon && preg_match( "/^\//",$newtoplink->icon ) ){
							$iconFileName = preg_replace( "/^\//" , "" , $newtoplink->icon );
							if( !file_exists( $this->iconDir . $iconFileName ) ){
								$newtoplink->icon = null;
							}
						}
						/* end */
					}
					$permission = $fdata['toppermission'];
					//save link
					$newid = $this->myService->saveToplink( $newtoplink, $permission );
					$toplinkid = !empty( $newtoplink->id ) ? $newtoplink->id : $newid;
					$childIds = $this->myService->getTopLinkChildIdByParentId( $toplinkid );
					
					if( !empty( $childIds ) ){
						if( !empty( $childrenIDList ) ){
							foreach( $childIds as $cid ){
								if( !in_array( $cid, $childrenIDList ) ){
									$this->myService->removeToplinkChild( $cid );
								}
							}
						}else{
							foreach( $childIds as $cid ){
								$this->myService->removeToplinkChild( $cid );
							}
						}
					}
						
					//process children if any
					if( !empty( $childrenNameList ) && !empty( $childrenUrlList ) ){
						foreach( $childrenNameList as $childIndex => $childName ){
							if( !empty( $childName ) && !empty( $childrenUrlList[$childIndex] ) ){
								$childDoa = new TOPLINK_BOL_ToplinkChildren();
								$childDoa->childof = $toplinkid;
								$childDoa->name = $childName;
								
								if( !empty( $childrenUrlList[$childIndex] ) ){
									$thecurl = preg_match( "/^http/",$childrenUrlList[$childIndex] ) ? $childrenUrlList[$childIndex] : "http://".$childrenUrlList[$childIndex];
								}else{
									$thecurl = "#";
								}
								$childDoa->url = $thecurl;
								
								if( !empty( $childrenIDList[$childIndex] ) ){
									$childDoa->id = $childrenIDList[$childIndex];
								}
								$this->myService->saveTopLinkChild( $childDoa );
							}
						}
					}
					
					OW::getFeedback()->info($this->text('toplink', 'save_success_message'));
					$this->redirect( OW::getRouter()->urlForRoute( 'toplink.admin' ) );
				}
			}
			
			$alltoplink = $this->myService->getTopLink( true );
			$updatelink = array();
			if( !empty( $alltoplink ) ){
				foreach( $alltoplink as $toplinkId => $toplink ){
					$toplink->itemname = empty( $toplink->itemname ) ? $this->text( 'toplink','top_link_no_name' ) : $toplink->itemname;
					$permissionx = array();
					$theTopLinkPermission = $this->myService->getTopLinkPermissionById( $toplink->id );
					foreach( $theTopLinkPermission as $topLinkPermission ){
						$permissionx[] = ucwords( $availableDesc[$topLinkPermission->availablefor] );
					}
					$toplink->permission = !empty( $permissionx ) ? implode( ',', $permissionx ) : '';
					$toplink->updateurl = OW::getRouter()->urlForRoute( 'toplink.admin2', array( 'id' => $toplink->id ) );
					$toplink->removeurl = OW::getRouter()->urlForRoute( 'toplink.remove', array( 'id' => $toplink->id ) );
					$alltoplink[$toplinkId] = $toplink;
				}
			}
			
			if( !empty( $theTopLinkChild ) ){ $this->assign( 'children', $theTopLinkChild ); }
			$this->assign( 'alltoplink', $alltoplink );
		}
		
		public function savelink( $param ){
			OW::getResponse()->setDocument(new OW_AjaxDocument());
			var_dump( $param );
		}
		
		public function removelink( $param ){
			$id = $param['id'];
			$this->myService->removeToplink( $id );
			$childIds = $this->myService->getTopLinkChildIdByParentId( $id );
			if( !empty( $childIds ) ){
				foreach( $childIds as $cid ){
					$this->myService->removeToplinkChild( $cid );
				}
			}
			OW::getFeedback()->info($this->text('toplink', 'toplink_removed'));
			$this->redirect( OW::getRouter()->urlFor( 'TOPLINK_CTRL_Admin', 'toplinklist' ) );
		}
		
		private function text( $prefix, $key, array $vars = null ){
			return OW::getLanguage()->text( $prefix, $key, $vars );
		}
	}
?>