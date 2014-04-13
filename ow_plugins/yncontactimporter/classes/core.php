<?php
// Get the library file
require_once OW::getPluginManager() -> getPlugin('yncontactimporter') ->getClassesDir().'vcardreader.php';
require_once OW::getPluginManager() -> getPlugin('yncontactimporter') ->getClassesDir().'vcard.php';
class YNCONTACTIMPORTER_CLASS_Core
{
	//check social bridge plugin is installed and is actived and set config
	function checkSocialBridgePlugin($provider)
	{
		if (!$plugin = BOL_PluginService::getInstance() -> findPluginByKey('ynsocialbridge'))
		{
			return false;
		}
		else
		{
			if (!$plugin->isActive() || !YNSOCIALBRIDGE_BOL_ApisettingService::getInstance() -> getConfig($provider))
			{
				return false;
			}
		}
		return true;
	}
	
	function parse_vcards(&$lines)
	{
		$cards = array();
		$card = new VCard();
		while ($card -> parse($lines))
		{
			$property = $card -> getProperty('N');
			if (!$property)
			{
				return "";
			}
			$n = $property -> getComponents();
			$tmp = array();
			if ($n[3])
				$tmp[] = $n[3];
			// Mr.
			if ($n[1])
				$tmp[] = $n[1];
			// John
			if ($n[2])
				$tmp[] = $n[2];
			// Quinlan
			if ($n[4])
				$tmp[] = $n[4];
			// Esq.
			$ret = array();
			if ($n[0])
				$ret[] = $n[0];
			$tmp = join(" ", $tmp);
			if ($tmp)
				$ret[] = $tmp;
			$key = join(", ", $ret);
			$cards[$key] = $card;
			// MDH: Create new VCard to prevent overwriting previous one (PHP5)
			$card = new VCard();
		}
		ksort($cards);
		return $cards;
	}

	function get_vcard_categories(&$cards)
	{
		$unfiled = false;
		// set if there is at least one unfiled card
		$result = array();
		foreach ($cards as $card_name => $card)
		{
			$properties = $card -> getProperties('CATEGORIES');
			if ($properties)
			{
				foreach ($properties as $property)
				{
					$categories = $property -> getComponents(',');
					foreach ($categories as $category)
					{
						if (!in_array($category, $result))
						{
							$result[] = $category;
						}
					}
				}
			}
			else
			{
				$unfiled = true;
			}
		}
		if ($unfiled && !in_array('Unfiled', $result))
		{
			$result[] = 'Unfiled';
		}
		return $result;
	}

	/**
	 * Used to upload CSV/VCF file
	 *
	 * @param mixed
	 */
	function uploadContactFile()
	{
		$contacts = array();
		$friends = array();

		$is_error = 0;
		$message = '';
		$ci_contacts = array();

		// list the permitted file type
		$permit_file_types = array(
			'text/csv' => 'csv',
			'text/comma-separated-values' => 'csv',
			'application/csv' => 'csv',
			'application/excel' => 'csv',
			'application/vnd.ms-excel' => 'csv',
			'application/vnd.msexcel' => 'csv',
			'text/anytext' => 'csv',
			'text/x-vcard' => 'vcf',
			'application/vcard' => 'vcf',
			'text/anytext' => 'vcf',
			'text/directory' => 'vcf',
			'text/x-vcalendar' => 'vcf',
			'application/x-versit' => 'vcf',
			'text/x-versit' => 'vcf',
			'application/octet-stream' => 'ldif',
		);

		for (; ; )
		{
			$uploaded_file = $_FILES['csvfile']['tmp_name'];
			$filetype = $_FILES['csvfile']["type"];
			$filename = $_FILES['csvfile']['name'];
			// Check file types
			$v = strpos($filename, '.ldif');

			if (!array_key_exists($filetype, $permit_file_types) && $v < 0)
			{
				$is_error = 1;
				$message = "Invalid file type!";
				break;
			}

			if (is_uploaded_file($uploaded_file))
			{
				$fh = fopen($uploaded_file, "r");
				//die('0');
				if ($this -> EndsWith(mb_strtolower($filename), 'csv'))
				{

					// Process CSV file type
					//die('1');
					$i = 0;
					$row = fgetcsv($fh, 1024, ',');

					$first_name_pos = -1;
					$email_pos = -1;
					$first_name_pos = -1;
					$last_name_pos = -1;
					$count = count($row);

					for ($i = 0; $i < $count; $i = $i + 1)
					{

						if ($row[$i] == "E-mail Display Name" || $row[$i] == "First" || $row[$i] == "First Name")
						{
							$first_name_pos = $i;
						}
						elseif ($row[$i] == "E-mail Address" || $row[$i] == "Email" || $row[$i] == "E-mail Address")
						{
							$email_pos = $i;
						}
						elseif ($row[$i] == "Last Name" || $row[$i] == "Last")//yahoo format oulook
						{
							$last_name_pos = $i;
						}
						else
						{
							// do nothing
						}
					}

					if (($email_pos == -1) || ($first_name_pos == -1 && $last_name_pos == -1))
					{
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}
					while (($row = fgetcsv($fh, 1024, ',')) != false)
					{
						if (isset($row[$email_pos]) && $row[$email_pos] != "")
						{
							$name = empty($row[$first_name_pos]) ? '': @$row[$first_name_pos];
							if($name)
								$name .= ' ';
							$name .= empty($row[$last_name_pos]) ? '': @$row[$last_name_pos];
							
							if(empty($name))
								$name = $row[$email_pos];
							$contacts[] = array(
								'email' => $row[$email_pos],
								'name' => $name
							);
						}
					}

					fclose($fh);

				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'vcf'))
				{
					// Process VCF file type
					//die('2');
					$file_size = filesize($uploaded_file);

					if ($file_size == 0)
					{
						$is_error = 1;
						$message = 'Empty file!';
						break;
					}
					$lines = file($uploaded_file);
					$cards = @$this -> parse_vcards($lines);
					$all_categories = @$this -> get_vcard_categories($cards);
					//$names = array('FN', 'TITLE', 'ORG', 'TEL', 'EMAIL', 'URL', 'ADR', 'BDAY', 'NOTE');
					$names = array('EMAIL');
					foreach ($cards as $card_name => $card)
					{
						$contact['first_name'] = $card_name;
						$contact['name'] = $contact['first_name'];

						$properties = $card -> getProperties('EMAIL');
						if ($properties)
						{
							$contact['email'] = $properties[0] -> value;
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
						}
					}
					if ((!isset($contact['email'])) || (!isset($contact['name'])))
					{
						//die('3');
						$is_error = 1;
						$message = "Invalid file format!";
						break;
					}

					if (isset($contact['email']))
					{
						if ($this -> validateEmail($contact['email']))
						{
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
						}
						else
						{
							$is_error = 0;
							$message = "There's some error in your contact file";
						}
					}
				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'ldif'))//thunderbirth
				{
					//die('1');
					$thunder_data = fread($fh, filesize($uploaded_file));
					$rows = explode(PHP_EOL, $thunder_data);
					$name = "";
					$email = "";
					$contacts = array();

					foreach ($rows as $index => $row)
					{
						try
						{
							@list($key, $data) = @explode(':', $row);
							if ($key == 'cn')
								$name = $data;
							if ($key == 'mail')
								$email = trim($data);

							if ($name != "" && $email != "")
							{

								$contacts[] = array(
									'email' => $email,
									'name' => $name
								);

								$name = "";
								$email = "";
							}

						}
						catch(Exception $ex)
						{

						}

						//echo $key.'--'.$data."<br/>";
					}
				}
				else
				{

					// not support format
					$is_error = 1;
					$message = "Unknown file type!";

				}
			}

			if (empty($contacts))
			{

				$is_error = 1;
				$message = "There is no contact in your address book";
				break;
			}
			foreach ($contacts as $value)
			{

				$ci_contacts["{$value["email"]}"] = $value["name"];
			}
			break;
		}

		$returns['contacts'] = $ci_contacts;
		$returns['is_error'] = $is_error;
		$returns['error_message'] = $message;

		return $returns;
	}

	/**
	 * Validate an email address
	 *
	 * @param mixed $email
	 * @return mixed
	 */
	function validateEmail($email)
	{
		$pattern = "/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/";
		return (bool) preg_match($pattern, $email);
	}

	/**
	 * Check if a string ends with a specified substring
	 *
	 * @param mixed $FullStr
	 * @param mixed $EndStr
	 */
	function endsWith($FullStr, $EndStr)
	{
		// Get the length of the end string
		$StrLen = strlen($EndStr);

		// Look at the end of FullStr for the substring the size of EndStr

		$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}
}
?>