<?php 
	class Users extends CI_Controller{
		public function register(){
			$data['title'] = 'Sign up';

			$this->form_validation->set_rules('name', 'Name','required');
			$this->form_validation->set_rules('username', 'UserName','required|callback_check_username_exists');
			$this->form_validation->set_rules('email', 'Email','required|callback_check_email_exists');
			$this->form_validation->set_rules('password', 'Password','required');
			$this->form_validation->set_rules('password2', 'Confirm Password','matches[password]');

			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header_login_register');
				$this->load->view('users/register', $data);
				$this->load->view('templates/footer_login_register');

			} else {
				// Encrypt passoword
				$enc_password = md5($this->input->post('password')); //php function

				$this->user_model->register($enc_password);

				//set message
				$this->session->set_flashdata('user_registered', 'Sunteti inregistrat');

				redirect('home');

			}

		}

		//login 
		public function login(){
			$data['title'] = 'Sign in';

			$this->form_validation->set_rules('username', 'Username','required');
			$this->form_validation->set_rules('password', 'Password','required');

			if($this->form_validation->run() === FALSE){
				$this->load->view('templates/header_login_register');
				$this->load->view('users/login', $data);
				$this->load->view('templates/footer_login_register');

			} else {
				//get username
				$username = $this->input->post('username');
				//encrypt pass
				$password = md5($this->input->post('password'));

				//user
				$user_id = $this->user_model->login($username, $password);

				if($user_id){
					//create session
					$user_data = array(
					               'user_id' => $user_id,
					               'username' => $username,
					               'logged_in' =>true  
					                   );

					$this->session->set_userdata($user_data);


					//set message
				$this->session->set_flashdata('user_loggedin', 'Sunteti logat');

				redirect('home');

				}
				else {
					//set message
				$this->session->set_flashdata('login_failed', 'Login invalid');

				redirect('users/login');
				}

			}

		}

		public function logout(){
			//unset user data
			$this->session->unset_userdata('logged_in');
			$this->session->unset_userdata('user_id');
			$this->session->unset_userdata('username');

			$this->session->set_flashdata('user_loggedout', 'Te-ai delogat');

			redirect('users/login');

		}

		//check if username exists
		public function check_username_exists($username){
			$this->form_validation->set_message('check_username_exists', 'Username folosit');

			if($this->user_model->check_username_exists($username)){
				return true;
			}
			else {
				return false;
			}
		}

		public function check_email_exists($email){
			$this->form_validation->set_message('check_email_exists', 'Email folosit');

			if($this->user_model->check_email_exists($email)){
				return true;
			}
			else {
				return false;
			}
		}
	}