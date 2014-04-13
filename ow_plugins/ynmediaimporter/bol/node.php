<?php
class YNMEDIAIMPORTER_BOL_Node extends OW_Entity 
{
	/**
     * @var int
     */	
	public $id;
	
	/**
	 * @var string
	 */
	public $nid;
	
	/**
	 * @var int
	 */
	public $user_id;
	
	/**
	 * @var int
	 */
	public $user_aid;
	
	/**
	 * @var int
	 */
	public $scheduler_id;
	
	/**
	 * @var int
	 */
	public $owner_id;
	
	/**
	 * @var string
	 */
	public $owner_type;
	
	/**
	 * @var string
	 */
	public $key;
	
	/**
	 * @var string
	 */
	public $uid;
	
	/**
	 * @var string
	 */
	public $aid;
	
	/**
	 * @var string
	 */
	public $media;
	
	/**
	 * @var string
	 */
	public $provider;
	
	/**
	 * @var string
	 */
	public $photo_count;
	
	/**
	 * @var int
	 */
	public $status;
	
	/**
	 * @var string
	 */
	public $title;
	
	/**
	 * @var string
	 */
	public $src_thumb;
	
	/**
	 * @var string
	 */
	public $src_small;
	
	/**
	 * @var string
	 */
	public $src_medium;
	
	/**
	 * @var string
	 */
	public $src_big;
	
	/**
	 * @var string
	 */
	public $description;
	
	public function getUUID(){
		return (microtime(1)*10000) . '_'.$this->id .'.jpg';
	}
	
	public function getDownloadFilename()
	{
		return $this -> src_big;
	}
}