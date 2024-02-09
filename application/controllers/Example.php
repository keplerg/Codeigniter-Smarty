<?php
class Example extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');
	}

	function index()
	{
        $data = array();
        $data['error'] = '';
        $data['message'] = '';

		// This example is taken from the Smarty demo and modified slightly
		$this->smarty->assign("Name","Fred Irving Johnathan Bradley Peppergill");
		$this->smarty->assign("FirstName",array("John","Mary","James","Henry"));
		$this->smarty->assign("LastName",array("Doe","Smith","Johnson","Case"));
		$this->smarty->assign("Class",array(array("A","B","C","D"), array("E", "F", "G", "H"), array("I", "J", "K", "L"), array("M", "N", "O", "P")));

		$this->smarty->assign("contacts", array(array("phone" => "555-1234", "fax" => "555-2345", "cell" => "999-9999"), array("phone" => "555-4444", "fax" => "555-3333", "cell" => "888-8888")));

		$this->smarty->assign("state_options", array( 'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming' ));

		// english is the default if you don't set lang
		$this->smarty->assign("lang", "english");

		// Set the validation rules if this is a submit
		if ( $this->input->post('action') == 'submit' )
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[12]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[passconf]|md5');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'trim|required');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('state', 'State', '');

			if ( ! $this->form_validation->run() )
			{
				$data['error'] = 'Check and fix the form errors below';
			}
			else
			{
				$data['message'] = 'Thanks for posting!';
			}
		}

		// These assignments are passed by the associative array
		$data['title'] = 'Welcome to the Smarty Website';
		$data['bold'] = true;
		$data['ip_address'] = $this->input->server('REMOTE_ADDR');

		// Calling the convenience function view() that allows passing data
		$this->smarty->view( 'example.tpl', $data );
	}
}
