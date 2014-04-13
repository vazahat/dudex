<?php
class YNCONTACTIMPORTER_CMP_Contacts extends OW_Component
{
	public function __construct($params)
	{
		$contacts = $params['contacts'];
		$contactPerPage = (int)OW::getConfig() -> getValue('yncontactimporter', 'contact_per_page');
		$this -> assign('contactPerPage', $contactPerPage);
		$maxInvitePerTimes = (int)OW::getConfig() -> getValue('yncontactimporter', 'max_invite_per_times');
		$noPhoto = OW::getPluginManager() -> getPlugin('yncontactimporter') -> getStaticUrl() . "img/default_user.jpg";

		if (!$maxInvitePerTimes)
		{
			$maxInvitePerTimes = 10;
		}
		$totalContacts = count($contacts);
		if ($params['provider'] == 'facebook')
		{
			$totalContacts = $params['totalFriends'];
		}
		$totalPages = ceil($totalContacts / $contactPerPage);
		$infoisArray = in_array($params['provider'], array(
			'facebook',
			'twitter',
			'linkedin',
			'myspace'
		));
		$isEmail = in_array($params['provider'], array(
			'yahoo_google_hotmail_csv',
			'sapo',
			'mail2world'
		));
		$this -> assign('contacts', "");
		if ($params['provider'] == 'yahoo_google_hotmail_csv' && isset($params['gmailContacts']) && $params['gmailContacts'])
		{
			$this -> assign('contacts', $params['gmailContacts']);

		}

		if (!empty($_POST['skip_add']))
		{
			$this -> assign('skip_add', $_POST['skip_add']);
		}
		else
		{
			$this -> assign('skip_add', '');
		}
		if(!isset($_SESSION['ynfriends_checked']))
		{
			$_SESSION['ynfriends_checked']['page_friendIds'] ='';
			$_SESSION['ynfriends_checked']['page_friendNames'] ='';
		}
		// check total checked
		$checked = 0;
		$arr_Friends = explode(',', $_SESSION['ynfriends_checked']['page_friendIds']);
		if(isset($_REQUEST['page_friendIds']) && $_REQUEST['page_friendIds'])
		{
			$arr_FriendNames = explode(',', $_REQUEST['page_friendNames']);
			foreach (explode(',', $_REQUEST['page_friendIds']) as $key => $value) 
			{
				if($value && !in_array($value, $arr_Friends))
				{
					$_SESSION['ynfriends_checked']['page_friendIds'] .= $value.",";
					$_SESSION['ynfriends_checked']['page_friendNames'] .= $arr_FriendNames[$key].',';
				}
			}
		}
		$checked = count(explode(',', $_SESSION['ynfriends_checked']['page_friendIds']));
		if($checked)
		{
			$checked = $checked -1;
		}
		$this -> assign('isEmail', $isEmail);
		$this -> assign('maxInvitePerTimes', $maxInvitePerTimes);
		$this -> assign('invitation_selected', $checked);
		$this -> assign('totalContacts', $totalContacts);
		$this -> assign('totalSearchContacts', $totalContacts);
		$this -> assign('warningMaxInvite', OW::getLanguage() -> text('yncontactimporter', 'warning_max_invite', array('max' => $maxInvitePerTimes)));
		$this -> assign('warningNoContactSelected', OW::getLanguage() -> text('yncontactimporter', 'no_contacts_selected'));
		$this -> assign('actionUrl', $params['actionUrl']);
		$this -> assign('add_friend', $params['add_friend']);
		$this -> assign('importUrl', OW::getRouter() -> urlForRoute('yncontactimporter-import'));
		$this -> assign('friendIds', $_SESSION['ynfriends_checked']['page_friendIds']);
		$this -> assign('friendNames', $_SESSION['ynfriends_checked']['page_friendNames']);
		$defaultMessage = OW::getConfig() -> getValue('yncontactimporter', 'default_invite_message');
		$this -> assign('defaultMessage', $defaultMessage);
		if ($totalContacts > 0)
		{
			//search contacts
			$search_contacts = array();
			if (isset($_REQUEST['search']) && trim($_REQUEST['search']) != "")
			{
				$totalContacts = 0;
				if ($params['provider'] == 'facebook')
				{
					$search_contacts = $contacts;
					$totalContacts = $params['totalFriendSearch'];
				}
				else 
				{
					foreach ($contacts as $key => $info)
					{
						
						if (is_array($info))
						{
							$name = trim($info['name']);
						}
						else
						{
							$name = trim($info);
							if ($name == "")
								$name = $key;
						}
						if (strpos(strtoupper("." . $name), strtoupper(trim($_REQUEST['search']))))
						{
							$search_contacts[$key] = $info;
						}
					}
					$contacts = $search_contacts;
					$totalContacts = count($contacts);
				}
				$totalPages = ceil($totalContacts / $contactPerPage);
				$this -> assign('totalSearchContacts', $totalContacts);
			}
			if ($infoisArray)
			{
				uasort($contacts, 'compareOrder');
			}
			else
			{
				uasort($contacts, 'compare');
			}
			$contents = "";
			$counter = 0;
			$page = 1;
			if (isset($_REQUEST['search_page_id']))
			{
				$page = $_REQUEST['search_page_id'];
			}
			$check_first_cha = "";
			foreach ($contacts as $key => $info)
			{
				if (strpos($key, "no-cache") > 0)
				{
					continue;
				}
				$counter++;
				if (is_array($info))
				{
					if ($info['pic'])
					{
						$pic = "<img height='30px' src='{$info['pic']}'>";
					}
					else
					{
						$pic = "<img height='30px' src='{$noPhoto}'>";
					}
					$name = trim($info['name']);
				}
				else
				{
					$name = trim($info);
					if ($name == "")
						$name = $key;
					$pic = '';
				}
				//check and add new page
				if ($counter > $page * $contactPerPage)
				{
					$contents .= "</table></div><span class='contactimporter_total_page'>" . OW::getLanguage() -> text('yncontactimporter', 'contact_page', array(
						'start' => ($page - 1) * $contactPerPage + 1,
						'end' => $page * $contactPerPage > $totalContacts ? $totalContacts : $page * $contactPerPage,
						'total' => $totalContacts
					)) . "</span></div>";
					$page++;
					$contents .= "<div class = 'contact_page' id = 'page_" . $page . "' style = 'display: none'>";
					$contents .= "<table class='ow_table_2' style='margin-bottom: 0px'>
									<tbody>
										<tr class='ow_tr_first'>
										<th style='width: 9%'>
									<input id='checkallBox' type='checkbox' onclick='toggleAll(this)' name='toggle_all' title='" . OW::getLanguage() -> text('yncontactimporter', 'select_all') . "'>
									</th>";
					if (!$isEmail)
						$contents .= "<th style='width: 86%'>" . OW::getLanguage() -> text('yncontactimporter', 'name') . "</th>
									<th> </th>";
					else
						$contents .= "<th style='width: 50%'>" . OW::getLanguage() -> text('yncontactimporter', 'name') . "</th>
									<th>" . OW::getLanguage() -> text('yncontactimporter', 'email') . "</th>";

					$contents .= "</tr></tbody></table>";
					$contents .= "<div style='max-height: 560px; overflow-x: hidden; overflow-y: auto; float: left; width: 100%;margin-bottom: 10px'>";
					$contents .= "<table class='ow_table_2'>";
				}

				if (ucfirst(mb_substr($name, 0, 1, 'UTF-8')) != $check_first_cha)
				{
					$contents .= '<tr class="ow_alt1">
                    			<td colspan="4" align="left" style = "border-top-right-radius:0px; border-top-left-radius:0px; text-align:left; font-weight: bold; padding-left: 4%">
                    			' . ucfirst(mb_substr($name, 0, 1, 'UTF-8')) . '</td>
                			</tr>';
					$check_first_cha = ucfirst(mb_substr($name, 0, 1, 'UTF-8'));
				}
				if ($counter % 2)
					$class = ' ow_alt1';
				else
					$class = 'ow_alt2';
				if ($counter % $contactPerPage == 0)
				{
					$class .= ' ow_tr_last';
				}

				$contents .= "<tr class='{$class}'  id='row_{$counter}'>
							<td style = 'width: 9%'>
							<input id='check_{$counter}' name='check_{$counter}' onclick='check_toggle({$counter},false);' value='{$counter}' type='checkbox' class='thCheckbox'";
				if (in_array($key, $arr_Friends))
						$contents .= " checked ";
				$contents .= "><input type='hidden' id = 'email_{$counter}' name='email_{$counter}' value='{$key}'>
								<input type='hidden' name='name_{$counter}' id='name_{$counter}' value='{$name}'>
							</td>
							<td style = '" . ($isEmail ? "width: 50%;" : "width: 86%;") . " text-align:left' onclick='check_toggle({$counter}, true);'>{$name}
							</td>" . ($isEmail ? "<td onclick='check_toggle({$counter},true);'>&lt;{$key}&gt;</td>" : "<td class = 'contactimporter_contact_image'>{$pic}</td>") . "</tr>";
			}
			if ($counter == 0)
			{
				$contents = "<tr class='thTableOddRow'><td align='center' style='padding:20px;' colspan='" . ($isEmail) ? "2" : "3" . "'>" . OW::getLanguage() -> text('yncontactimporter', 'not_contacts') . "</td></tr>";
			}
			else
			{
				$contents .= "<script type='text/javascript'>counter={$counter}</script>";
			}
			$this -> assign('contents', $contents);
			$pages = "<span class='contactimporter_total_page'>" . OW::getLanguage() -> text('yncontactimporter', 'contact_page', array(
				'start' => ($page - 1) * $contactPerPage + 1,
				'end' => $page * $contactPerPage > $totalContacts ? $totalContacts : $page * $contactPerPage,
				'total' => $totalContacts
			)) . "</span>";
			$this -> assign('pages', $pages);

			$pagination = '<center>
		<div class="ow_paging clearfix ow_smallmargin" id="contactimporter_page_list">';
			if ($totalPages > 1)
			{
				$pagination .= '<span>' . OW::getLanguage() -> text('yncontactimporter', 'pages') . '</span>
	                    <a ';
				if ($page == 1)
				{
	      	$pagination .= 'style="display:none"';
	      }
	      $pagination .= ' href="javascript:;" id="0" rel="page_0" >
	                          &#171;
	                    </a>
	                    <a ';
				if ($page == 1)
				{
					$pagination .= 'class="active"';
				}
				$pagination .= 'href="javascript:;" id="1" rel="page_1">
	                          1
	                    </a>';
				for ($i = 2; $i <= $totalPages; $i++)
				{
					$pagination .= '<a ';
					if ($page == $i)
					{
						$pagination .= 'class="active"';
					}
					if ($i > 10)
						$pagination .= 'style = "display: none"';
					$pagination .= ' href="javascript:;" id="' . $i . '" rel="page_' . $i . '" >
	                         ' . $i . '
	                    </a>';
				}
				$pagination .= '<a ';
				if($page == $totalPages)
				{
					$pagination .= 'style = "display: none"';
				}
				
				$pagination .= ' href="javascript:;" id="' . ($totalPages + 1) . '" rel="page_' . ($totalPages + 1) . '">
	                          &#187;
	                    </a>';
			}
			$pagination .= '</div>
			</center>';
			$this -> assign('pagination', $pagination);
		}
		$this -> assign('currentSearch', !empty($_REQUEST['search']) ? htmlspecialchars($_REQUEST['search']) : '');
		$this -> assign('service', $params['service']);
	}

}

function compareOrder($a, $b)
{
	if ($a['name'])
	{
		return ucfirst(trim($b['name'])) < ucfirst(trim($a['name']));
	}
}

function compare($a, $b)
{
	return ucfirst(trim($a)) > ucfirst(trim($b));
}
?>