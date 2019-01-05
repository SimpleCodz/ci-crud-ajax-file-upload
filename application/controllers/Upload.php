<?php
class Upload extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('m_upload');	
		$this->load->library('form_validation');
	}

	function index(){
		$this->load->view('v_upload');
	}

	function get_data(){
		$data = $this->m_upload->getData();
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function get_single($id){
		$data = $this->m_upload->getById($id);
		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	function hapus($id){
		$data = $this->m_upload->getById($id);
		$img_src = FCPATH.'assets/images/'.$data->gambar;
		
		if(unlink($img_src)){
			$this->m_upload->hapus($id);
		}
	}

	function _validate(){
		$this->form_validation->set_error_delimiters('', '');
		$this->form_validation->set_rules('judul', 'Judul', 'trim|required|min_length[2]|max_length[50]');
		if (empty($_FILES['file']['name']))
		{
			$this->form_validation->set_rules('file', 'Gambar', 'required');
		}
	}
	
	function _config(){
		$config['upload_path']		= "./assets/images";
        $config['allowed_types']	= 'gif|jpg|jpeg|png';
		$config['encrypt_name'] 	= TRUE;
		$config['max_size']     	= '100';

        $this->load->library('upload', $config);
	}

	function do_upload(){
		$this->_validate();
		$this->_config();
		
		$judul= $this->input->post('judul');
		
		if($this->form_validation->run() == FALSE || $this->upload->do_upload("file") == FALSE){
			$errors = array(
				'file'			=> form_error('file'),
				'judul' 		=> form_error('judul'),
				'fail_upload' 	=> $this->upload->display_errors('','')
			);
			$data = array(
				'errors' => $errors,
				'status' => false
			);
			$this->output->set_content_type('application/json')->set_output(json_encode($data));
		}else{
			$data 	= array('upload_data' => $this->upload->data());
			$image	= $data['upload_data']['file_name']; 
			$result	= $this->m_upload->simpan_upload($judul,$image);	
			$this->output->set_content_type('application/json')->set_output(json_encode(array('status'=>true)));
		}	
	}

	function edit(){
		$id = $this->input->post('id');
		$img_data = $this->m_upload->getById($id);	
		$img_src = FCPATH.'assets/images/'.$img_data->gambar;

		$judul = $this->input->post('judul');
		
		if($_FILES['file']['name'] == ''){
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_rules('judul', 'Judul', 'trim|required|min_length[2]|max_length[50]');
			
			if($this->form_validation->run()){
				$this->m_upload->update($id, $judul, null);
				$this->output->set_content_type('application/json')->set_output(json_encode(array('status'=>true)));
			}else{
				$errors['judul'] = form_error('judul');
				$data = array(
					'errors' => $errors,
					'status' => false
				);
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			}
		}else{
			$this->_validate();
			$this->_config();
			if($this->form_validation->run() == FALSE || $this->upload->do_upload("file") == FALSE){
				$errors = array(
					'file'			=> form_error('file'),
					'judul' 		=> form_error('judul'),
					'fail_upload' 	=> $this->upload->display_errors('','')
				);
				$data = array(
					'errors' => $errors,
					'status' => false
				);
				$this->output->set_content_type('application/json')->set_output(json_encode($data));
			}else{
				$data = array('upload_data' => $this->upload->data());
				$image= $data['upload_data']['file_name'];
				
				if(unlink($img_src)){
					$this->m_upload->update($id, $judul, $image);
					$this->output->set_content_type('application/json')->set_output(json_encode(array('status'=>true)));
				}
			}
		}
	}
}