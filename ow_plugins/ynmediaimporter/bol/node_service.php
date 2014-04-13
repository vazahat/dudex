<?php
class YNMEDIAIMPORTER_BOL_NodeService
{
	private static $classInstance;
	private function __construct()
	{
		
	}
	
	public static function getInstance()
	{
		if (!isset(self::$classInstance))
		{
			self::$classInstance = new self();
		}
		return self::$classInstance;
	}
	
	public function getNodeList()
    {
        return YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->findAll();
    }
    
    public function addNode($params)
    {
    	$node = new YNMEDIAIMPORTER_BOL_Node();
    	
    	if ( isset($params['nid']) && $params['nid'] != '' )
    		$node->nid = $params['nid'];
    	
    	if ( isset($params['user_id']) && $params['user_id'] != '' )
    		$node->user_id = $params['user_id'];
    	
    	if ( isset($params['user_aid']) && $params['user_aid'] != '' )
    		$node->user_aid = $params['user_aid'];
    	
    	if ( isset($params['scheduler_id']) && $params['scheduler_id'] != '' )
    		$node->scheduler_id = $params['scheduler_id'];
    	
    	if ( isset($params['owner_id']) && $params['owner_id'] != '' )
    		$node->owner_id = $params['owner_id'];

    	if ( isset($params['owner_type']) && $params['owner_type'] != '' )
    		$node->owner_type = $params['owner_type'];
    	
    	if ( isset($params['key']) && $params['key'] != '' )
    		$node->key = $params['key'];

    	if ( isset($params['uid']) && $params['uid'] != '' )
    		$node->uid = $params['uid'];

    	if ( isset($params['aid']) && $params['aid'] != '' )
    		$node->aid = $params['aid'];

    	if ( isset($params['media']) && $params['media'] != '' )
    		$node->media = $params['media'];

    	if ( isset($params['provider']) && $params['provider'] != '' )
    		$node->provider = $params['provider'];

    	if ( isset($params['photo_count']) && $params['photo_count'] != '' )
    		$node->photo_count = $params['photo_count'];

    	if ( isset($params['status']) && $params['status'] != '' )
    		$node->status = $params['status'];

    	if ( isset($params['title']) && $params['title'] != '' )
    		$node->title = $params['title'];

    	if ( isset($params['src_thumb']) && $params['src_thumb'] != '' )
    		$node->src_thumb = $params['src_thumb'];

    	if ( isset($params['src_small']) && $params['src_small'] != '' )
    		$node->src_small = $params['src_small'];

    	if ( isset($params['src_medium']) && $params['src_medium'] != '' )
    		$node->src_medium = $params['src_medium'];

    	if ( isset($params['src_big']) && $params['src_big'] != '' )
    		$node->src_big = $params['src_big'];

    	if ( isset($params['description']) && $params['description'] != '' )
    		$node->description = $params['description'];
    	
    	YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->save($node);
    }
    
    public function deleteNode($id)
    {
    	if ($id > 0)
    	{
    		YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->deleteById($id);
    	}
    }
    
    public function deleteNodes($idList)
    {
    	if (count($idList) > 0)
    	{
    		YNMEDIAIMPORTER_BOL_NodeDao::getInstance()->deleteByIdList($idList);
    	}
    }
    
}