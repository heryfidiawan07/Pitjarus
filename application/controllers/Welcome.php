<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct() {
        parent::__construct();

		$this->load->model('WelcomeModel');
    }

	public function index()
	{
		$data['area'] = $this->db->get('store_area')->result();
		$this->load->view('welcome', $data);
	}

	public function chart()
	{
		try {
			$area = $this->input->post('area', true);
            $from = date('Y-m-d', strtotime($this->input->post('from', true)));
            $to = date('Y-m-d', strtotime($this->input->post('to', true)));

			$data = $this->WelcomeModel->chart($area, $from, $to);
			if(!$data) {
				throw new Exception("Data not found !");
			}

			$tempData = [];
			foreach($data as $row) {
				$tempData[] = number_format(($row->compliance_total/$row->row_total)*100, 1);
			}

			$result = [
				'labels' => array_column($data, 'area_name'),
				'datasets' => [
					[
						'backgroundColor' => 'blue',
						'borderColor' => 'blue',
						'borderWidth' => 1,
						'data' => $tempData,
						'label' => 'Nilai',
					]
				]
			];

			echo json_encode($result);
		} catch (\Throwable $th) {
			echo json_encode(['status'=>false, 'labels'=>[], 'datasets'=>[], 'message'=>$th->getMessage()]);
		}
	}

	public function datatables()
	{
		try {
            $area = $this->input->post('area', true);
            $from = date('Y-m-d', strtotime($this->input->post('from', true)));
            $to = date('Y-m-d', strtotime($this->input->post('to', true)));

            $data = $this->WelcomeModel->datatables($area, $from, $to);
			if(!$data) {
				throw new Exception("Data not found !");
			}

			$header = array_values(array_unique(array_column($data, 'area_name')));
			$brand  = array_unique(array_column($data, 'brand_name'));
			$result = [
				'header' => $header,
				'data' => []
			];

			foreach($brand as $head) {
				foreach($data as $row) {
					if($row->brand_name == $head) {
						$result['data'][$head][$row->area_name] = number_format(($row->compliance_total/$row->row_total)*100, 1);
					}
				}
			}
            
            echo json_encode($result);
        } catch (\Throwable $th) {
			echo json_encode(['status'=>false, 'header'=>[], 'data'=>[], 'message'=>$th->getMessage()]);
		}
	}

}
