<?php
	class TOPLINK_CLASS_EventHandler{
		private static $classInstance;
		
		public static function getInstance(){
			if ( !isset(self::$classInstance) ){
				self::$classInstance = new self();
			}
			return self::$classInstance;
		}
		
		public function collectItems( BASE_CLASS_ConsoleItemCollector $event ){
			$toplinks = TOPLINK_BOL_Service::getInstance()->getToplink( false );
			
			$prefericon = 1;
			
			if( !empty( $toplinks ) ){
				foreach( $toplinks as $toplink ){
					$eventContent = array();
					
					if( empty( $toplink->itemname ) ){
						$eventContent['notitle'] = 1;
					}
					
					if( !empty( $toplink->icon ) ){
						$toplink->icon = preg_match( '/^\//',$toplink->icon ) ? OW::getPluginManager()->getPlugin('base')->getUserFilesUrl() . 'avatars' . $toplink->icon : $toplink->icon;
						if( !empty( $toplink->itemname ) ){
							$label = "<span style=\"display:inline;padding-left:21px;background:url('".$toplink->icon."') no-repeat scroll left center transparent;\" >".$toplink->itemname."</span>";
						}else{
							$label = "<span style=\"display:inline;padding-left:21px;width:21px;background:url('".$toplink->icon."') no-repeat scroll center center transparent;\" ></span>";
						}
					}else{
						$toplink->icon = OW::getPluginManager()->getPlugin('toplink')->getStaticUrl() . 'images/no-title-16.png';
						$label = $toplink->itemname;
					}
					
					/*-- check for children --*/
					$toplinkchild = TOPLINK_BOL_Service::getInstance()->getTopLinkChildObjectByParentId( $toplink->id );
					if( !empty( $toplinkchild ) ){
						$item = new BASE_CMP_ConsoleDropdownMenu( $label,'toplink' );
						$item->addClass('ow_toplink_'.mt_rand(100,10000).'_list');
						
						foreach( $toplinkchild as $children ){
							$itemarr = array();
							$itemarr['url'] = $children->url;
							$itemarr['label'] = $children->name;
							$item->addItem( 'xxxx',$itemarr );
						}
					}else{
						$item = new BASE_CMP_ConsoleItem();
						$template = OW::getPluginManager()->getPlugin('toplink')->getCmpViewDir() . 'top_link_item.html';
						$item->setTemplate($template);
						$eventContent['name'] = $toplink->itemname;
						$eventContent['icon'] = $toplink->icon;
						$eventContent['url'] = $toplink->url;
						$eventContent['target'] = $toplink->target;
						$item->setContent( $eventContent );
					}
					$event->addItem($item, $toplink->order);
					/*-- check for children --*/
				}
			}
		}
		
		public function init(){
			OW::getEventManager()->bind('console.collect_items', array($this, 'collectItems'));
		}
	}
?>