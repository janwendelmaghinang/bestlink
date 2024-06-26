<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Training_types extends Admin_Controller 
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Training_types';

		$this->load->model('model_types');
		$this->load->model('model_training');
		$this->load->model('model_designations');
	}


	public function index()
	{
		if(!in_array('viewTrainingTypes', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->render_template('trainingtypes/index', $this->data);
	}

           
	public function fetchTrainingTypesData()
	{
		$result = array('data' => array());

		$data = $this->model_types->getTrainingTypesData();
		foreach ($data as $key => $value) {

			// button
			$buttons = '';

			if(in_array('updateTrainingTypes', $this->permission)) {
				$buttons .= '<button type="button" class="btn btn-default" onclick="editTrainingTypes('.$value['id'].')" data-toggle="modal" data-target="#editTrainingTypesModal"><i class="fa fa-pencil"></i></button>';	
			}
			
			if(in_array('deleteTrainingTypes', $this->permission)) {
				$buttons .= ' <button type="button" class="btn btn-default" onclick="removeTrainingTypes('.$value['id'].')" data-toggle="modal" data-target="#removeTrainingTypesModal"><i class="fa fa-trash"></i></button>
				';
			}				

			$status = ($value['active'] == 1) ? '<span class="label label-success">Active</span>' : '<span class="label label-warning">Inactive</span>';

			$result['data'][$key] = array(
				$value['name'],
				$status,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}
            
	public function fetchTrainingTypesDataById($id)
	{
		if($id) {
			$data = $this->model_types->getTrainingTypesData($id);
			echo json_encode($data);
		}

		return false;
	}


	public function create()
	{

		if(!in_array('createTrainingTypes', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		$this->form_validation->set_rules('training_types_name', 'Training types name', 'trim|required');
		$this->form_validation->set_rules('active', 'Active', 'trim|required');

		$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

        if ($this->form_validation->run() == TRUE) {
        	$data = array(
        		'name' => $this->input->post('training_types_name'),
        		'active' => $this->input->post('active'),	
        	);

        	$create = $this->model_types->create($data);
        	if($create == true) {
        		$response['success'] = true;
        		$response['messages'] = 'Succesfully created';
        	}
        	else {
        		$response['success'] = false;
        		$response['messages'] = 'Error in the database while creating the Training type information';			
        	}
        }
        else {
        	$response['success'] = false;
        	foreach ($_POST as $key => $value) {
        		$response['messages'][$key] = form_error($key);
        	}
        }

        echo json_encode($response);

	}


	public function update($id)
	{
		if(!in_array('updateTrainingTypes', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$response = array();

		if($id) {
			$this->form_validation->set_rules('edit_training_types_name', 'Training type name', 'trim|required');
			$this->form_validation->set_rules('edit_active', 'Active', 'trim|required');

			$this->form_validation->set_error_delimiters('<p class="text-danger">','</p>');

	        if ($this->form_validation->run() == TRUE) {
	        	$data = array(
	        		'name' => $this->input->post('edit_training_types_name'),
	        		'active' => $this->input->post('edit_active'),	
	        	);

	        	$update = $this->model_types->update($data, $id);
	        	if($update == true) {
	        		$response['success'] = true;
	        		$response['messages'] = 'Succesfully updated';
	        	}
	        	else {
	        		$response['success'] = false;
	        		$response['messages'] = 'Error in the database while updated the Training type information';			
	        	}
	        }
	        else {
	        	$response['success'] = false;
	        	foreach ($_POST as $key => $value) {
	        		$response['messages'][$key] = form_error($key);
	        	}
	        }
		}
		else {
			$response['success'] = false;
    		$response['messages'] = 'Error please refresh the page again!!';
		}

		echo json_encode($response);
	}


	public function remove()
	{
		if(!in_array('deleteTrainingTypes', $this->permission)) {
			redirect('dashboard', 'refresh');
		}
		$response = array();
		$d_type = 'ttype';
		
		$type_id = $this->input->post('type_id');
        $check = $this->model_training->existInTraining($type_id,$d_type);

		if($check == true){
			$response['success'] = false;
			$response['messages'] = "This Training Type Exists in Training!";
		}else{
			$delete = $this->model_types->remove($type_id);
			if($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";	
			}
			else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the Training type information";
			}
		}
		echo json_encode($response);
	}

}