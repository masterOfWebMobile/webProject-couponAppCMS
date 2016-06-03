<?php
require_once('GCM.php');
require_once('resize_class.php');
class webservice extends GCM 
{
/********************* GLOBAL VARIABLES *********************/
public $push_array = array();
public $json_array = array();
public $posts = array();
public $fetchresult = array();
public $temp_array = array();
public $temp_array1 = array();
public $format = 'json';
/********************* GLOBAL VARIABLES *********************/
	private $RCI_OBJ;

	function __construct(){
		$this->RCI_OBJ = new RCI;
	}
	
	function test(){
		
		
	}
	

	public function getCoupanCount()
	{

		$query = "SELECT COUNT(id) FROM `add_coupon` WHERE 1";
		$result = $GLOBALS['DB']->Query($query);
		$fetch = $GLOBALS['DB']->GetRow($query);
		array_push($this->json_array,array("count"=>"".$fetch['COUNT(id)'].""));
            				$this->output();

	}
	
	
	public function getCoupan(){
		if(isset($_REQUEST['coupan']) && isset($_REQUEST['device_id'])){
			$coupan = $_REQUEST['coupan'];
			$start = 0;
			$end = 0;
			$page_item = 5; 
			$page = 0;

			if($coupan == "Yes"){
			
				$sortby = (isset($_REQUEST['sortby'])) ? $_REQUEST['sortby'] : '';  
				$key = (isset($_REQUEST['key'])) ? $_REQUEST['key'] : ''; 
				$page_item = (isset($_REQUEST['items'])) ? $_REQUEST['items'] : 5;
				$page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 0;
				
				$start = $page;
				$end = $page_item;
				
				$query_con = "";
				$query = "SELECT * FROM add_coupon $query_con LIMIT $start, $end";
				
				$result = $GLOBALS['DB']->Query($query);
				$count = $GLOBALS['DB']->num_rows($result);

				if($count > 0){
					$fetch = $GLOBALS['DB']->GetRows($query);
					
					foreach($fetch as $f){

					    $coupan_id = $f['id'];
					    
					    $c_prefix = $f['c_prefix'];
					    $c_type = $f['c_type'];
					    $c_nums = $f['c_nums'];

					    $coupon_path = dirname($f['c_image']);

						if(!empty($f['c_image']) && isset($f['c_image'])){
							$f['c_image'] = IMGDIRURL.$f['c_image'];
						}
						
						if(isset($f['c_share_image']) && !empty($f['c_share_image'])){
							$f['c_share_image'] = IMGDIRURL.$f['c_share_image'];
						}
						
						$query_like = "SELECT * FROM coupan_like WHERE coupan_id = '$coupan_id'";
						$result_like = $GLOBALS['DB']->Query($query_like);
						$count_like = $GLOBALS['DB']->num_rows($result_like);
						$count_cl = 0;
						if($count_like > 0){
							$fetch_like = $GLOBALS['DB']->GetRow($query_like);
							$count_cl = $fetch_like['total_like'];
						}
						$f['total_like'] = $count_like;
						
						$coupan = $_REQUEST['coupan'];
						$device_id = $_REQUEST['device_id'];
						
						$coupan_number = '';
						
						
						// check coupon number type is fixed or running number
					    if($c_nums == 1)
					    {
					    	$coupan_number = $c_prefix . $c_type;
					    }
					    else
					    {
							if(!empty($device_id)){
								if ($device_id == 'ae32877r840kg08967') {
									$count_cn = 0;
								}
								else
								{
									$query_check_cn = "SELECT coupan_number, coupan_id FROM  cupan_number WHERE coupan_id = '$coupan_id' && device_id = '$device_id' LIMIT 1 ";
									$result_check_cn = $GLOBALS['DB']->Query($query_check_cn);
									$count_cn = $GLOBALS['DB']->num_rows($result_check_cn);
								}
							
								if($count_cn == 0)
								{
									// new mechanism
									$running_number = 0;

									$retryNum = 0;
									while (true) 
									{
										$txtfile = "../" . $coupon_path . "/" . $coupan_id . ".txt";
										$handle = fopen($txtfile,"r+");
										//Lock File, error if unable to lock
										if(flock($handle, LOCK_EX)) {
										    $count = fread($handle, filesize($txtfile));    //Get Current Hit Count
										    $count = $count + 1;    //Increment Hit Count by 1

										    if ($count >= $c_nums) {
										    	$coupan_number = "Sold out";
										    }
										    else
										    {
											    $running_number = $count;
											    ftruncate($handle, 0);    //Truncate the file to 0
											    rewind($handle);           //Set write pointer to beginning of file
											    fwrite($handle, $count);    //Write the new Hit Count
											}
										    flock($handle, LOCK_UN);    //Unlock File
										    fclose($handle);
										    break;
										} else {
										    //loopback mechanism
										    fclose($handle);
										    usleep(10);
										}

										if($retryNum > 5)
											break;
										$retryNum++;
									}

									if (is_numeric($running_number) && $running_number != 0) {
										$running_number_with_zero = sprintf('%0'. strlen((string)$c_nums) .'d', $running_number);
										$coupan_number = $c_prefix . $c_type . $running_number_with_zero;

										$insert_coupan_q = "INSERT INTO cupan_number(cn_id,coupan_number,coupan_id,device_id) VALUES('','".$coupan_number."','".$coupan_id."','".$device_id."')";
										$insert_coupan_r = $GLOBALS['DB']->Query($insert_coupan_q);

									}
									
								}else{
									
									$fetch_coupan = $GLOBALS['DB']->GetRow($query_check_cn);
									$coupan_number = $fetch_coupan['coupan_number'];
								
								}	
								
							}
						}
						//echo $coupan_number;exit;
						$coupan_size = strlen($coupan_number);
						
						if($coupan_size < 1){
							$f['coupan_number'] = 17002262;
						}else{
							$f['coupan_number'] = $coupan_number;
						}
						array_push($this->json_array,$f);
					}
					//print_r($this->json_array);
					$this->output();
				}else{
					array_push($this->json_array,array("status"=>"failure1"));
            				$this->output();
				}	
			}else{
				array_push($this->json_array,array("status"=>"failure2"));
            			$this->output();
			}
		}else{
			array_push($this->json_array,array("status"=>"failure3"));
            		$this->output();
		}
	}

	public function getStore(){
		if(isset($_REQUEST['store'])){
			$store = $_REQUEST['store'];
			if($store == "Yes"){
				$query = "SELECT * FROM add_store";
				$result = $GLOBALS['DB']->Query($query);
				$count = $GLOBALS['DB']->num_rows($result);
				if($count > 0){
					$fetch = $GLOBALS['DB']->GetRows($query);
					$city_array = array();
					$store_array = array();
					
					foreach($fetch as $f){
						if(!empty($f['s_image']) && isset($f['s_image'])){
							$f['s_image'] = IMGDIRURL.$f['s_image'];
						}
											
						array_push($store_array,$f);
					}
					
					$query_city = "SELECT * FROM `add_city`";
					$fetch_city = $GLOBALS['DB']->GetRows($query_city);
						
					foreach($fetch_city as $fc){
						array_push($city_array, $fc); 
					}
					
					array_push($this->json_array, array("store"=> $store_array));
					array_push($this->json_array, array("city"=> $city_array));
					$this->output();
				}else{
					array_push($this->json_array,array("status"=>"failure"));
            				$this->output();
				}
			}else{
				array_push($this->json_array,array("status"=>"failure"));
            			$this->output();
			}
		}else{
			array_push($this->json_array,array("status"=>"failure"));
            		$this->output();
		}
	}
	
	public function getCouponsStatus()
	{
		if(isset($_REQUEST['coupon_ids'])){
		
			$param = $_REQUEST['coupon_ids'];
			$coupon_ids = explode(",",$param);
			$response = "";
			
			
			foreach($coupon_ids as $coupon_id)
			{
				if( $coupon_id == null || $coupon_id == '')
					continue;
				$query = "SELECT id FROM `add_coupon` WHERE id='$coupon_id' LIMIT 1";
				$result = $GLOBALS['DB']->Query($query);
				$count = $GLOBALS['DB']->num_rows($result);
				
				if($count > 0)
					$response .="1,";
				else
					$response .="0,";
			}
			$response .="-1";
			
			array_push($this->json_array,array("status"=>$response));
			$this->output();
			
		}else{
			array_push($this->json_array,array("status"=>"failure"));
            		$this->output();
		}
	}
	
	public function doGetStoreBycity(){
		if(isset($_REQUEST['city'])){
			$city =(int) $_REQUEST['city'];
			$query = "SELECT * FROM add_store WHERE s_city = '$city'";
			$result = $GLOBALS['DB']->Query($query);
			$count = $GLOBALS['DB']->num_rows($result);
			if($count > 0){
				$fetch = $GLOBALS['DB']->GetRows($query);
				$img_path = '';
				foreach($fetch as $f){
					$img_path = $f['s_image'];
					$f['s_image'] = IMGDIRURL."".$img_path;
					$f['s_name'] = html_entity_decode($f['s_name']);
					$s_address = htmlentities($f['s_address']);
					$f['s_address'] = html_entity_decode($s_address);
					
					array_push($this->json_array,$f);
				}
            			$this->output();
			}else{
				array_push($this->json_array,array("status"=>"failure"));
            			$this->output();
			}	 
		}else{
			array_push($this->json_array,array("status"=>"failure"));
            		$this->output();
		}
	}
	
	public function dolike(){
		if(isset($_REQUEST['like']) && isset($_REQUEST['coupan_id']) && isset($_REQUEST['device_id'])){
			$coupan_id = $_REQUEST['coupan_id'];
			$device_id = $_REQUEST['device_id'];
			
			$query_check_like = "SELECT * FROM coupan_like WHERE coupan_id = '$coupan_id' && device_id = '$device_id'";
			$result_check_like = $GLOBALS['DB']->Query($query_check_like);
			$count_check_like = $GLOBALS['DB']->num_rows($result_check_like);
			if($count_check_like == 0){
				$insert_like = "INSERT INTO coupan_like (`coupan_id`, `total_like`, `device_id`) VALUES('$coupan_id', '1', '$device_id')";
				$result_like = $GLOBALS['DB']->Query($insert_like);
								
				if($result_like){
					$query_total_like = "SELECT * FROM coupan_like WHERE coupan_id = '$coupan_id'";
					$result_total_like = $GLOBALS['DB']->Query($query_total_like);
					$count_total_like = $GLOBALS['DB']->num_rows($result_total_like);
					array_push($this->json_array,array("status"=>"success", "coupan_id"=>$coupan_id, "total_likes"=>$count_total_like));
            				$this->output();
				}else{
					$query_total_like = "SELECT * FROM coupan_like WHERE coupan_id = '$coupan_id'";
					$result_total_like = $GLOBALS['DB']->Query($query_total_like);
					$count_total_like = $GLOBALS['DB']->num_rows($result_total_like);
					array_push($this->json_array,array("status"=>"failure", "coupan_id"=>$coupan_id, "total_likes"=>$count_total_like));
            				$this->output();
				}
			}else{
				array_push($this->json_array,array("status"=>"failure"));
            			$this->output();
			}
			
		}else{
		
		}
	}
	
	public function dolike1(){
		if(isset($_REQUEST['like']) && isset($_REQUEST['coupan_id'])){
			$coupan_id = $_REQUEST['coupan_id'];
			$coupan_query = "SELECT * FROM coupan_like WHERE coupan_id = '$coupan_id'";
			$coupan_result = $GLOBALS['DB']->Query($coupan_query);
			$count_like = $GLOBALS['DB']->num_rows($coupan_result);
			$new_like = 0;
			
			if($count_like > 0){
				$fetch_coupan = $GLOBALS['DB']->GetRow($coupan_query);
				$like_count = $fetch_coupan['total_like'];
				$new_like = (int) $like_count + 1;
				
				$update_like = "UPDATE coupan_like SET `total_like` = '$new_like' WHERE coupan_id = '$coupan_id'";
				$result_like = $GLOBALS['DB']->Query($update_like);
				if($result_like){
					array_push($this->json_array,array("status"=>"success", "coupan_id"=>$coupan_id, "total_likes"=>$new_like));
            				$this->output();
				}else{
					array_push($this->json_array,array("status"=>"failure", "coupan_id"=>$coupan_id, "total_likes"=>$new_like));
            				$this->output();
				}
				
			}else{
				$insert_like = "INSERT INTO coupan_like (`coupan_id`, `total_like`) VALUES('$coupan_id', '1')";
				$result_like = $GLOBALS['DB']->Query($insert_like);
				if($result_like){
					$new_like = $new_like +1;
					array_push($this->json_array,array("status"=>"success", "coupan_id"=>$coupan_id, "total_likes"=>$new_like));
            				$this->output();
				}else{
					array_push($this->json_array,array("status"=>"failure", "coupan_id"=>$coupan_id, "total_likes"=>$new_like));
            				$this->output();
				}
			}
			
		}else{
			array_push($this->json_array,array("status"=>"failure", "total_likes"=>0));
            		$this->output();
		}
	}
	
	public function assign_coupan(){
		if(isset($_REQUEST['coupan']) && isset($_REQUEST['device_id'])){
			$coupan = $_REQUEST['coupan'];
			$device_id = $_REQUEST['device_id'];
			
			if((!empty($device_id)) && ($coupan == "Yes")){
				$query_coupan = "SELECT coupan_number, coupan_id FROM  `cupan_number` AS cn INNER JOIN `add_coupon` AS ac ON cn.coupan_id = ac.id  WHERE  cn.`device_id` IS NULL ORDER BY  cn.`cn_id` ASC LIMIT 0 , 1";
				$result_coupan = $GLOBALS['DB']->Query($query_coupan);
				$count_coupan = $GLOBALS['DB']->num_rows($result_coupan);
			
				if($count_coupan > 0){
					$fetch_coupan = $GLOBALS['DB']->GetRow($query_coupan);
					$coupan_number = $fetch_coupan['coupan_number'];
					$coupan_id = $fetch_coupan['coupan_id'];
				}	
			}
		}
	}
	
	public function getPolicy(){
		if(isset($_REQUEST['policy']) && $_REQUEST['policy']=='Yes'){
			$query = "SELECT * FROM add_policy LIMIT 0,1";
			$result = $GLOBALS['DB']->Query($query);
			$count = $GLOBALS['DB']->num_rows($result);
			
			if($count > 0){
				$fetch = $GLOBALS['DB']->GetRow($query);
				$fetch['p_name'] = htmlspecialchars_decode($fetch['p_name']);
				array_push($this->json_array,array("status"=>"success", "policy"=>$fetch));
            			$this->output();
			}else{
				array_push($this->json_array,array("status"=>"failure"));
            			$this->output();
			}
		}else{
			array_push($this->json_array,array("status"=>"failure"));
            		$this->output();
		}
	}
	
	
	public function putGCM(){
        if(isset($_REQUEST['device_id']) && isset($_REQUEST['device_name']) && isset($_REQUEST['device_gcm']) && isset($_REQUEST['device_type'])){
            $device_id = $_REQUEST['device_id'];
            $device_name = htmlspecialchars($_REQUEST['device_name'],ENT_QUOTES);
            $device_gcm = $_REQUEST['device_gcm'];
            $device_type = $_REQUEST['device_type'];


            $check_query = "SELECT * FROM coupan_gcm WHERE device_id = '$device_id'";
            $check_result = $GLOBALS['DB']->Query($check_query);
            $check_count = $GLOBALS['DB']->num_rows($check_result);

            if($check_count == 0){

                $check_query = "SELECT * FROM coupan_gcm WHERE device_gcm = '$device_gcm'";
                $check_result = $GLOBALS['DB']->Query($check_query);
                $check_count = $GLOBALS['DB']->num_rows($check_result);

                if ($check_count == 0) {
                    $query = "INSERT INTO coupan_gcm (`device_id`, `device_name`, `device_gcm`, `device_type`) VALUES('$device_id', '$device_name', '$device_gcm', '$device_type')";

                    $result = $GLOBALS['DB']->Query($query);
                    if($result){
                        array_push($this->json_array,array("status"=>"success"));
                        $this->output();
                    }else{
                        array_push($this->json_array,array("status"=>"failure"));
                        $this->output();
                    }
                }else{
                    array_push($this->json_array,array("status"=>"success"));
                    $this->output();
                }
            }else{
                $query = "UPDATE coupan_gcm SET `device_name` = '$device_name', `device_GCM` = '$device_gcm', `device_type` = '$device_type' WHERE `device_id` = '$device_id'";
                $result = $GLOBALS['DB']->Query($query);
                if($result){
                    array_push($this->json_array,array("status"=>"success"));
                    $this->output();
                }else{
                    array_push($this->json_array,array("status"=>"failure"));
                    $this->output();
                }
            }

        }else{
            array_push($this->json_array,array("status"=>"failure"));
            $this->output();
        }
	}
	
	
	function notify(){
		$this->GCMpushNotifyMsg("APA91bGxIdaVt61Gn8aqZg3WP4_1w6kVdONkYSCPp1yPMCM8W_OgCANFAar0yeSz718WoEuk65rTfjutvcV_fm5f_cgW6XO8C06wmuBUHNoKYFDyZqZVzkL8d1lRAdnP5vRp2zaogGu5", "or devendra sir kya hal chal hai?");
		
	}
	
	function GCMpushNotifyMsg($regId,$message)
	{
		$registatoin_ids = array($regId);
		$message = array("price" => "or devendra sir kya hal chal hai?");
		
		$result = $this->send_notification($registatoin_ids, $message);
		echo $result;
	}
	

	function wrongurl()
	{
		$this->push_array = 'Wrong URL';
		array_push($this->json_array, $this->push_array);
		$this->output();
	}

	function output()
	{
		
		if($this->format == 'json') 
		{
		header('Content-type: application/json');
		echo str_replace('\/','/',json_encode($this->json_array));
		@mysql_close($link);
		exit;
		}
	}	

	function output1($result_array)
	{		
		header('Content-type: application/json');
		echo str_replace('\/','/',json_encode($result_array));
		@mysql_close($link);
		exit;
		
	}	
	
}

?>
