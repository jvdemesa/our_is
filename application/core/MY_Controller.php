<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	protected $data = array();
	protected $login_user_data = array();
	public function __construct()
	{
		parent::__construct();

		// $this->data['bread_crumbs'] = array();
	}
	
	public function admin_loader()
	{
		// $this->data["login_admin_data"] = $this->session->userdata('login_admin_data');
	}
	
	protected function redirect_to_default_page() 
	{				
 		//redirect(site_url("admin/home"));
	}
	
	protected function isLogin()
	{
		$this->load->model('General_model', '', TRUE);
		if(!$this->session->userdata(SESSION_DATA))
		{
			redirect(site_url("Authentication/logout"));
		}else{
			$this->load->model('User_login_model', '', TRUE);
			//get user data (is_restricted and window_id)
			$userdata = $this->User_login_model->get_user_data();
			$is_restricted = $userdata->is_restricted;
			//if is_restricted is = 1, redirect to restricted page
			if($is_restricted){
				redirect(site_url("Authentication/restricted"));
			}
		}
	}

	protected function linkGenerator()
	{
		$this->load->model('General_model', '', TRUE);
		//check if method name is index, if index return blank if not return method name
		$method_name = ($this->router->fetch_method() == 'index')?'':$this->router->fetch_method();
		//set active links url (class/method)
		$active_link = $this->router->fetch_class().'/'.$method_name;
		//get active link_id and parent_id in link tbl using link field
		$active_link_id = $this->General_model->get_active_link($active_link);
		$links_label = $this->session->userdata(SESSION_DATA)->links_label;
		$this->load->model('General_model', '', TRUE);
		
		$links = $this->General_model->link_tree();
		$data = new stdClass();
		$tree_data = $this->General_model->link_tree();
		function buildTree($data, $parentId){
		    $branch = array();

		    foreach ($data as $element) 
		    {
		        if ($element->parent_id == $parentId) 
		        {
		            $children = buildTree($data, $element->link_id);
		            if ($children) 
		            {
		                $element->children = $children;
		            }
		            $branch[] = $element;
		        }
		    }
		    return $branch;
		}
		$active_link_id = ($active_link_id==null)?[0,0]:$active_link_id;
		$link_generated = buildTree($tree_data,0);
		function printTree($array,$links_label,$active_link_id) {
			 $output='';
		    foreach ($array as $a) {
		    	if(!empty(array_intersect(array($a->label), $links_label))){
			    	//if parent and multiple is false add direct link
			    	if($a->multiple == 0){
						$active = (in_array($a->link_id, $active_link_id))?"active":"";
				        $output .= "<li class='nav-item '>";
				        $output .= "<a class='nav-link ".$active."' href='".base_url().$a->link."'>";
				        $output .= "<i class='".$a->icon."'></i> ";
				        $output .= "<p>".$a->label."</p></a>";
				        $output .= "</li>";
			        //if multiple is true  start tree view
			    	}else{
						$active = (in_array($a->link_id, $active_link_id))?"active":"";
						$open = (in_array($a->link_id, $active_link_id))?"menu-is-opening menu-open":"";
				        $output .= "<li class='nav-item $open'>";
				        $output .= "<a class='nav-link ".$active."' href='#'>";
				        $output .= "<i class='".$a->icon."'></i> ";
				        $output .= "<p>".$a->label;
				        $output .= "<i class='right fa fa-angle-left pull-right'></i>";
				        $output .= "</p>";
				        $output .= "</a>";
				        $output .= "<ul class='treeviewnav nav-treeview'>";
			    	}
				        if (isset($a->children)) {
				            $output .= printTree($a->children,$links_label,$active_link_id);
				        }
			        //if multiple is true  end tree view
			        if($a->multiple == 1){
				        $output .= "</ul>";
			        }
			    }
				        $output .= "</li>";
		    }
		    return $output;
		}
		return printTree($link_generated,$links_label,$active_link_id);
	}

	protected function isAllowed()
	{
		$allowed_links = $this->session->userdata(SESSION_DATA)->links;
		$link_loc = [];
		if($this->router->fetch_method() == 'index'){
			$link_loc[] = $this->router->fetch_class().'/'.$this->router->fetch_method();
			$link_loc[] = $this->router->fetch_class().'/';
			$link_loc[] = $this->router->fetch_class();
		}else{
			$link_loc[] = $this->router->fetch_class().'/'.$this->router->fetch_method();
		}
		// echo '<pre>' . var_export($allowed_links, true) . '</pre>';
		// echo '<pre>' . var_export($link_loc, true) . '</pre>';
    	if(empty(array_intersect($link_loc, $allowed_links)))
		{
			redirect(site_url("Admin/forbidden"));
		}
	}

	protected function array_dump($array, $name = 'var') {
	    highlight_string("<?php\n\$$name =\n" . var_export($array, true) . ";\n?>");
	    exit();
	}

	protected function resizeImage($filename,$path){
      $source_path = $path . $filename;
      $target_path = $path;
      $config_manip = array(
          'image_library' => 'gd2',
          'source_image' => $source_path,
          'new_image' => $target_path,
          'maintain_ratio' => TRUE,
          'width' => 600,
      );
   
      $this->load->library('image_lib', $config_manip);
      if (!$this->image_lib->resize()) {
          echo $this->image_lib->display_errors();
      }
   
      $this->image_lib->clear();
   }

	protected function get_img($path){
	    $url = $path;
 
		// Initialize a CURL session.
		$ch = curl_init();

		// Return Page contents.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Grab URL and pass it to the variable
		curl_setopt($ch, CURLOPT_URL, $url);

		$result = curl_exec($ch);

		return $result;
  // 		$arrContextOptions=array(
		//     "ssl"=>array(
		//         "verify_peer"=>false,
		//         "verify_peer_name"=>false,
		//     ),
		// );  

		// $response = file_get_contents($path, false, stream_context_create($arrContextOptions));

		// return $response;
  	}

	  protected function outputJSON($data)
	  {
		  header("Content-Type: application/json");
		  
		  echo json_encode($data);
		  
		  exit();
	  }

    protected function send_email($data){
	    $this->load->library('email');
	    $this->load->config('email');
	    $config = $this->config->item('email_config1');

	    $this->email->initialize($config);

	    $from = $config['smtp_user'];
        $to = $data->to;
        $subject = $data->subject;
        $message = $data->message;

        $this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            return 1;
        } else {
            return 0;
        }
    }


    protected function id_send_email($data){
	    $this->load->library('email');
	    $this->load->config('email');
	    $config = $this->config->item('noreply_our');

	    $this->email->initialize($config);

	    $from = $config['smtp_user'];
        $to = $data->to;
        $subject = $data->subject;
        $message = $data->message;

        $this->email->set_newline("\r\n");
        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

	    try {

	        // Suppress PHP warnings from fsockopen
	        $send = @$this->email->send();

	        if ($send) {
	            return 1;
	        }

	        log_message('error', $this->email->print_debugger());

	        return false;

	    } catch (Exception $e) {

	        log_message('error', $e->getMessage());

	        return 0;
	    }
    }

	protected function handleCors() {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0);
        }
    }

    protected function dump_array($data){
		return '<pre>' . var_export($data, true) . '</pre>';
    }

	protected function test() {
		echo "end";
		exit();
	}
}
