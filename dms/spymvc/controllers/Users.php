<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	
	function __construct() {
		parent::__construct();
		$this->load->library(array('session','email'));
		$this->load->model('user_model');
		$this->load->model('admin_model');
	}
	
	public function index()
	{
		if(($this->session->userdata('user_name')!=""))
		{
			$this->dashboard();
		}
		else
		{
			$this->login();
		}
	}
	
	public function login($errmsg = '0')
	{
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("login - $sitename");
		$data['errmsg'] = $errmsg;
		$data['wpcopy'] = $copyrights;
		
		$this->load->view('backend/login');
	}
	
	public function ulogin()
	{
		error_log('=== Login Attempt Started ===');
		error_log('Username: ' . $this->input->post('username'));
		
		$this->form_validation->set_rules('username','Username','trim|required');
		$this->form_validation->set_rules('password','Password','required|trim|min_length[4]|max_length[32]');
		
		$email = $this->input->post('username');
		$password = $this->input->post('password');
		
		error_log('Form validation passed');
		
		if ($this->form_validation->run() == FALSE)
		{
			error_log('Form validation failed');
			$this->login();
		}
		else
		{
			error_log('Trying admin login...');
			// First try admin login
			$admin_result = $this->admin_model->login($email, $password);
			if($admin_result->num_rows() > 0) {
				error_log('Admin login successful');
				$user = $admin_result->row_array();
				$sess_array = array(
					'user_id' => $user['user_id'],
					'user_name' => $user['user_name'],
					'user_email' => $user['user_email'],
					'user_type' => $user['user_type'],
					'logged_in' => TRUE
				);
				$this->session->set_userdata($sess_array);
				redirect('welcome/loaddashboard');
			} 
			// Then try tenant login
			else {
				error_log('Admin login failed, trying tenant login...');
				error_log('Email: ' . $email);
				error_log('Password (MD5): ' . md5($password));
				
				// Get the actual password from the database for debugging
				$this->db->where('ten_email', $email);
				$db_user = $this->db->get('tbl_tenants')->row_array();
				if($db_user) {
					error_log('Found tenant in DB. Stored hash: ' . $db_user['ten_pass']);
				} else {
					error_log('No tenant found with email: ' . $email);
				}
				
				$tenant_result = $this->admin_model->loginMember($email, md5($password));
				error_log('Tenant login query: ' . $this->db->last_query());
				error_log('Tenant login result count: ' . $tenant_result->num_rows());
				
				if($tenant_result->num_rows() > 0) {
					error_log('Tenant login successful');
					$user = $tenant_result->row_array();
					$sess_array = array(
						'user_id' => $user['ten_uid'],
						'user_name' => $user['ten_fname'].' '.$user['ten_lname'],
						'user_email' => $user['ten_email'],
						'user_type' => 'member',
						'logged_in' => TRUE
					);
					$this->session->set_userdata($sess_array);
					redirect('welcome/loaddashboard');
				} else {
					error_log('Tenant login failed');
					$this->login('err1');
				}
			}
		}
	}
	
	public function logout()
	{
		$newdata = array(
		'user_id'   =>'',
		'user_name'  =>'',
		'user_email'     => '',
		'user_type'     => '',
		'logged_in' => FALSE,
		);
		$this->session->unset_userdata($newdata);
		$this->session->sess_destroy();
		redirect('/');
	}
	
	public function register($errmsg = '0')
	{
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("register - $sitename");
		$data['errmsg'] = $errmsg;
		$data['wpcopy'] = $copyrights;
		$data['projlist'] = $this->admin_model->getprojects();
		$data['msg'] = $errmsg;
		
		$this->load->view('user/regheader', $data);
		$this->load->view('user/register');
		$this->load->view('user/regfooter', $data);
	}
	
	public function registeruser()
	{
		$this->load->library('form_validation');
		
		// Basic validation rules
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|matches[rpassword]');
		$this->form_validation->set_rules('rpassword', 'Password Confirmation', 'trim|required');
		$this->form_validation->set_rules('fullname', 'Full Name', 'trim|required');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
		
		if ($this->form_validation->run() == FALSE) {
			// Validation failed
			$this->session->set_flashdata('error', validation_errors());
			redirect('users/register');
		} else {
			// Check if email already exists
			$email = $this->input->post('email');
			if (!$this->admin_model->is_email_unique($email)) {
				$this->session->set_flashdata('error', 'This email is already registered.');
				redirect('users/register');
			}
			
			// Prepare tenant data
			$name_parts = explode(' ', $this->input->post('fullname'), 2);
			$fname = $name_parts[0];
			$lname = isset($name_parts[1]) ? $name_parts[1] : '';
			
			$tenant_data = array(
				'fname' => $fname,
				'lname' => $lname,
				'email' => $email,
				'phone' => $this->input->post('phone'),
				'password' => $this->input->post('password')
			);
			
			try {
				// Register the tenant
				$tenant_id = $this->admin_model->register_tenant($tenant_data);
				
				if ($tenant_id) {
					// Registration successful
					$this->session->set_flashdata('success', 'Registration successful! You can now log in.');
					redirect('users/login');
				} else {
					throw new Exception('Failed to register. Please try again.');
				}
			} catch (Exception $e) {
				$this->session->set_flashdata('error', 'Registration failed: ' . $e->getMessage());
				redirect('users/register');
			}
		}
	}
	
	public function makepayment($msg,$username)
	{
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("Complete Registration Payment - $sitename");
		$data['wpcopy'] = $copyrights;
		$data['msg'] = $msg;
		$data['username'] = $username;
		
		$this->load->view('user/regheader', $data);
		$this->load->view('user/makepayment');
		$this->load->view('user/regfooter', $data);
	}
	
	public function makeppayment($msg = '0',$username,$project)
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("Complete Property Payment - $sitename");
		$data['name'] = $name;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		$data['msg'] = $msg;
		$data['username'] = $username;
		$data['project'] = $project;
		
		$this->load->view('user/header', $data);
		$this->load->view('user/makeppayment');
		$this->load->view('user/footer', $data);
	}
	
	public function confirm_payment($username)
	{
		$this->user_model->c_payment($username);
		$this->user_model->active_user($username);
		$msg = 'Payment Successful. Please Login!';
		$this->login($msg);
	}
	
	public function confirm_payments($username,$project)
	{
		$this->user_model->c_payments($username,$project);
		$this->ntfyusers($username,$project);
		$this->dashboard();
	}
	
	public function ntfyusers($username,$project)
	{
		$notifyulist = $this->user_model->loadntfyusers($username,$project);
		$udtl = $this->user_model->getudetails($username);
		$pdtl = $this->user_model->getpdetails($project);
		foreach($notifyulist as $row)
		{
			$msg = "New User ".$udtl['uname']." Joined Group: ".$pdtl['projectname'];
			$this->user_model->sendnotify('user',$row['username'],$msg);
		}
		$this->user_model->sendnotify('admin','admin',$msg);
	}
	
	public function dashboard()
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		// add in aech function for profile photo working
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		// till here
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("user dashbaord - $sitename");
		$data['name'] = $name;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		$data['projlist'] = $this->user_model->getmyprojects($username);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		
		$this->load->view('user/header', $data);
		$this->load->view('user/homepage', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function addproject($msg = '0')
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("add project - $sitename");
		$data['name'] = $name;
		$data['email'] = $email;
		$data['menu'] = "projects";
		$data['wpcopy'] = $copyrights;
		$data['msg'] = $msg;
		$data['projlist'] = $this->admin_model->getprojects();
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		
		$this->load->view('user/header', $data);
		$this->load->view('user/addproject', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function newproject()
	{
		$username = $this->session->userdata('username');
		$project = $this->input->post('project');
		
		if($this->user_model->validateproj($username,$project) == FALSE)
		{
			$msg = "You have already added this property. Please try different property!";
			$this->addproject($msg);
		}
		else
		{
			$this->user_model->buy_project($username,$project);
			$msg = "Property Added Successfully! Please Complete your payment!";
			$this->makeppayment($msg,$username,$project);
		}
	}
	
	public function chat($ctype,$proj)
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['pdetails'] = $this->user_model->getpdetails($proj);
		$data['totalmembersinchat'] = $this->user_model->tmchat($proj);
		$data['memlist'] = $this->user_model->getprojmembers($proj);
		$projd = $this->user_model->getpdetails($proj);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		$data['title'] = ucfirst($projd['projectname']." Chatroom - $sitename");
		$data['name'] = $name;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/chatroom', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function uchat($ctype,$user)
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$data['mydetails'] = $this->user_model->getudetails($username);
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['udetails'] = $this->user_model->getudetails($user);
		$userd = $this->user_model->getudetails($user);
		$data['totalmprojects'] = $this->user_model->countmprojects($username,$user);
		$data['mplist'] = $this->user_model->getmprojects($username,$user);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		$data['title'] = ucfirst($userd['uname']." Chatroom - $sitename");
		$data['name'] = $name;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/uchatroom', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function chatsubmit($utype,$fromuser,$touser)
	{
		$msg = $this->input->post('cmsg');
		$this->user_model->uchatsubmit($utype,$fromuser,$touser,$msg,'text');
		//$this->dashboard();
	}
	
	public function getchat($utype,$fromuser,$touser,$ltid)
	{
		$getclist = $this->user_model->getuchat($utype,$fromuser,$touser,$ltid);
		$jsonData = '{"results":[';
		$line = new stdClass;
		$arr = array();
		foreach($getclist AS $crow)
		{
			$fromudtl = $this->user_model->getudetails($fromuser);
			$toudtl = $this->user_model->getudetails($touser);
			$line->id = $crow['id'];
			$line->usertype = $crow['usertype'];
			$line->mcontent = $crow['mcontent'];
			$line->fromuser = $crow['fromuser'];
			$line->touser = $crow['touser'];
			$line->fromuname = $fromudtl['uname'];
			$line->touname = $toudtl['uname'];
			$line->datentime = $crow['datentime'];
			$line->msgtype = $crow['msgtype'];
			$arr[] = json_encode($line);
		}
		$jsonData .= implode(",", $arr);
		$jsonData .= ']}';
		echo $jsonData;
	}
	
	
	public function getgchat($utype,$fromuser,$togroup,$ltid)
	{
		$getclist = $this->user_model->getgchat($utype,$fromuser,$togroup,$ltid);
		$jsonData = '{"results":[';
		$line = new stdClass;
		$arr = array();
		foreach($getclist AS $crow)
		{
			$fromudtl = $this->user_model->getudetails($crow['fromuser']);
			$line->id = $crow['id'];
			$line->usertype = $crow['usertype'];
			$line->mcontent = $crow['mcontent'];
			$line->fromuser = $crow['fromuser'];
			$line->touser = $crow['touser'];
			$line->fromuname = $fromudtl['uname'];
			$line->datentime = $crow['datentime'];
			$line->msgtype = $crow['msgtype'];
			$arr[] = json_encode($line);
		}
		$jsonData .= implode(",", $arr);
		$jsonData .= ']}';
		echo $jsonData;
	}
	
	public function upload()
	{
		$this->load->library('upload');
		$attachment_file=$_FILES["upload"];
		$fromuser = $this->input->post('fromuser');
		$touser = $this->input->post('touser');
		$usertype = $this->input->post('usertype');
		$msgtype = $this->input->post('msgtype');
		
        $output_dir = "uploads/";
        $fileName = $_FILES["upload"]["name"];
		move_uploaded_file($_FILES["upload"]["tmp_name"],$output_dir.$fileName);
		$this->user_model->uchatsubmit($usertype,$fromuser,$touser,$fileName,$msgtype);
		echo "File uploaded successfully";
	}
	
	public function docs($ctype,$proj)
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['gdocs'] = $this->user_model->getgdocs($ctype,$proj);
		$projd = $this->user_model->getpdetails($proj);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		$data['title'] = ucfirst($projd['projectname']." Documents - $sitename");
		$data['name'] = $name;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/gdocs', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function udocs($fromuser,$touser)
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['udocs'] = $this->user_model->getudocs($fromuser,$touser);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		$data['title'] = ucfirst("Documents - $sitename");
		$data['name'] = $name;
		$data['username'] = $username;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/udocs', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function getuserdetails($usert)
	{
		$udtl = $this->user_model->getudetails($usert);
		return $udtl;
	} 
	
	public function changepassword($msg = '0')
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['title'] = ucfirst("Change Password - $sitename");
		$data['name'] = $name;
		$data['email'] = $email;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		$data['msg'] = $msg;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/cpassword', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function cpassword()
	{
		$this->load->library('form_validation');
		$username = $this->session->userdata('username');
		
		$this->form_validation->set_rules('oldpass', 'Old Password', 'trim|required|min_length[5]|callback_validate_pass');
		$this->form_validation->set_rules('newpass', 'New Password', 'trim|required|min_length[5]|matches[rnewpass]');
		$this->form_validation->set_rules('rnewpass', 'Repeat New Password', 'trim|required|min_length[5]');
		$this->form_validation->set_message('validate_pass','Entered Old Password is not valid. Please Try Again!');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->changepassword();
		}
		else
		{
			$this->admin_model->changepass($username);
			$msg = "Password Successfully Changed!";
			$this->changepassword($msg);
		}
	}
	
	function validate_pass($str)
	{
		$field_value = $str;
		$username = $this->session->userdata('username');
		if($this->admin_model->validate_pass($field_value,$username))
		{
		return TRUE;
		}
		else
		{
		return FALSE;
		}	
	}
	
	public function profilest($msg = '0')
	{
		$name = $this->session->userdata('user_name');
		$email = $this->session->userdata('user_email');
		$username = $this->session->userdata('username');
		$profphoto = $this->session->userdata('profphoto');
		$data['profphoto'] = $profphoto;
		$sitename = WEBAPP_NAME;
		$copyrights = WEBAPP_COPYRIGHTS;
		$data['udtl'] = $this->user_model->getudetails($username);
		$data['totalntfy'] = $this->user_model->totalnotify($username);
		$data['ntfy'] = $this->user_model->notify($username);
		$data['title'] = ucfirst("Change Profile - $sitename");
		$data['name'] = $name;
		$data['email'] = $email;
		$data['username'] = $username;
		$data['menu'] = "dashboard";
		$data['wpcopy'] = $copyrights;
		$data['msg'] = $msg;
		
		
		$this->load->view('user/header', $data);
		$this->load->view('user/cprofile', $data);
		$this->load->view('user/footer', $data);
	}
	
	public function updateprof()
	{
		$this->load->library('form_validation');
		$username = $this->session->userdata('username');
		
		$this->form_validation->set_rules('uemail', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('uname', 'Name', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->profilest();
		}
		else
		{
			$this->user_model->profilest($username);
			$msg = "Profile Successfully Updated!";
			$this->profilest($msg);
		}
	}
}
