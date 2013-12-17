<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Analysis extends CI_Controller
{

	private $controller;

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->controller = strtolower(__CLASS__);
    	$this->load->model('analysis_model');
        $this->load->model('template_model');
    }

    public function test( $template_id ){
        $template = $this->template_model->get( $template_id);
        echo count( $template->sections );
        echo "<br>";
        print_r( $template->sections  );
    }
    public function gen( $template_id ){
    	check_permission( $this->controller, 'view');

    	$objPHPExcel = $this->analysis_model->get_report_by_template_id( $template_id);
        // Redirect output to a client’s web browser (Excel2007)
        
        header('Set-Cookie: fileDownload=true');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. date("Ymdhis") .'.xlsx"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

}
