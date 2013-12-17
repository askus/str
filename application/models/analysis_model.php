<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Analysis_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("PHPExcel");
        $this->load->model("template_model");
        $this->load->helper('excel_helper');
        $this->load->model("department_model");
        $this->load->model("questionnaire_model");
    }

    public function cal_score( $section ){
        $ret_scores = new stdClass();
        $ret_scores->pos = 0;
        $ret_scores->neg = 0;
        if( !$section->is_analyzed ){return null;}
        foreach( $section->questions as $question ){
            if( !$question->has_score){ continue;}
            if( $question->score->is_null ){ continue; }
            if( $question->score->score > 0 ){
                $ret_scores->pos +=1;
            }else{
                $ret_scores->neg += 1;
            }
        }

        return $ret_scores;
    }

    public function get_report_by_template_id( $template_id ){
        // get template
        $template = $this->template_model->get( $template_id );

        // setting excel 
        $fontStyle = array(
            'font' => array('name' => '標楷體', 'size' => 12),
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $titleFontStyle = array( 
            'font' => array('name'=>'標楷體', 'bold'=>true, 'size'=>12) 
        );

        // setting the header 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getDefaultStyle()->applyFromArray( $fontStyle);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setWrapText(true );

        $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0 );
        $activeSheet = $objPHPExcel->getActiveSheet();
        $title = sprintf("%s年%s月%s", $template->year, $template->month, $template->title );
        $activeSheet->setTitle( $title );

        // setting col width 
        $activeSheet->getColumnDimension('A')->setWidth(28.75 );
        $activeSheet->getColumnDimension('B')->setWidth(14.25);
        $activeSheet->getColumnDimension('C')->setWidth(8.38);
        $activeSheet->getColumnDimension('D')->setWidth(26.63);

        // setting row heigth
        $activeSheet->getRowDimension('3')->setRowHeight(63);

            // setting title 
        $activeSheet->setCellValue("A1", $title );
        $activeSheet->mergeCells('A1:T1');
        $activeSheet->getStyle( 'A1:T1')->applyFromArray( $titleFontStyle );

        $activeSheet->setCellValue("A2","受測\n科室");
        $activeSheet->mergeCells('A2:A5');

        $activeSheet->setCellValue('B2', "受測\n時間");
        $activeSheet->mergeCells('B2:B5');

        $activeSheet->setCellValue('C2','負責人');
        $activeSheet->mergeCells('C2:C5');

        $activeSheet->setCellValue('D2', '負責人單位');
        $activeSheet->mergeCells('D2:D5');

        // setting title border 
        $allbordersStyleArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
        );
        $outlineStyleArray = array( 
            'borders' =>array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK
                )
            )    
        );
        $activeSheet->getStyle('A2:D5')->applyFromArray( $allbordersStyleArray ) ;
        $activeSheet->getStyle('A2:D5') -> applyFromArray( $outlineStyleArray );


        $dynamic_start_col = ord('E')-ord('A')+1;
        $start_col = $dynamic_start_col;

        $title_index = 1;
        for( $i = 0 ; $i < count( $template->sections ); $i++ ){
            if( !$template->sections[$i]->is_analyzed ){
                continue;
            }
            $end_col = $start_col +2 ;

            //setting col width 
            $activeSheet->getColumnDimension(  num2col( $start_col ) )->setWidth( 4.88 );
            $activeSheet->getColumnDimension(  num2col( $start_col+1) )->setWidth( 4.88 );
            $activeSheet->getColumnDimension(  num2col( $start_col+2) )->setWidth( 7.88 );

            $activeSheet->setCellValue( num2col( $start_col). '3' , sprintf("%s、 %s", num2chiNum( $title_index ), $template->sections[$i]->section_title));
            $activeSheet->mergeCells( sprintf('%s3:%s4', num2col($start_col), num2col($end_col) ) );

            $activeSheet->setCellValue( num2col( $start_col ).'5', "優\n良");
            $activeSheet->setCellValue( num2col( $start_col +1).'5', "調\n整");
            $activeSheet->setCellValue( num2col( $start_col +2).'5', "總\n分");

            $title_index++;
            $start_col = $start_col + 3 ;
        } 
        $dynamic_end_col = $start_col -1 ;
        // setting section title border 
        $activeSheet->getStyle( sprintf("%s2:%s5", num2col( $dynamic_start_col), num2col( $dynamic_end_col ) ) )->applyFromArray( $allbordersStyleArray);
        $activeSheet->getStyle( sprintf("%s2:%s5", num2col( $dynamic_start_col), num2col( $dynamic_end_col ) ) )->applyFromArray( $outlineStyleArray);

        $total_col = $dynamic_end_col+1 ;

        $activeSheet->setCellValue( num2col( $dynamic_start_col) .'2', '測試內容小計');
        $activeSheet->mergeCells( sprintf("%s2:%s2", num2col( $dynamic_start_col) , num2col($dynamic_end_col) ));

        $activeSheet->getColumnDimension(  num2col( $total_col ) )->setWidth( 8.88 );
        $activeSheet->setCellValue( num2col( $total_col).'2','總計');
        $activeSheet->mergeCells( sprintf('%s2:%s5', num2col($total_col), num2col($total_col)) );
        $activeSheet->getStyle(  sprintf('%s2:%s5', num2col($total_col), num2col($total_col)) )->applyFromArray( $outlineStyleArray );



        // cal for each questionnaires 
        $row = 6;

        foreach( $template->questionnaires as $questionnaire ){
            $total_scores = new stdClass();
            $total_scores->pos = 0;
            $total_scores->neg = 0;
            $from_datetime_str = date( "m/dH:i", strtotime( $questionnaire->from_date ) );
            $to_datetime_str = date( "H:i", strtotime( $questionnaire->to_date));
            $during_datetime = sprintf("%s\n%s～%s",
                                date("m/d",strtotime($questionnaire->from_date)),
                                date("H:i",strtotime($questionnaire->from_date)),
                                date("H:i",strtotime($questionnaire->to_date)) 
                               );


            $assigned_user_department = $this->department_model->get( $questionnaire->assigned_user->department_id );
            
            $activeSheet->setCellValue( 'A'.$row,  $questionnaire->target_department->department_name) ;
            $activeSheet->setCellValue( 'B'.$row,  $during_datetime );
            $activeSheet->setCellValue( 'C'.$row, $questionnaire->assigned_user->name );
            $activeSheet->setCellValue( 'D'.$row, $assigned_user_department->department_name );

            $start_col = ord('E') -ord( 'A')+1;

            $questionnaire_with_score = $this->questionnaire_model->get_with_question_score( $questionnaire->questionnaire_id );

            foreach( $questionnaire_with_score->sections as $section ){
                if( !$section->is_analyzed ) continue; 
                
                $scores = $this->cal_score( $section );
                //$pos= sprintf("%d", $scores->pos );
                //$neg = sprintf("%d", $scores->neg);
                $pos = $scores->pos;
                $neg = $scores->neg ;

                if( $scores->pos + $scores->neg ==0 ){
                    $pos = '-';
                    $neg = '-';
                    $scr = '-';
                }else{
                    //$scr= sprintf("%.1f%%", 100 * ( $scores->pos / ($scores->pos + $scores->neg ) ) );
                    $scr = $scores->pos / ($scores->pos + $scores->neg );
                }
                $activeSheet->setCellValue( num2col($start_col).$row , $pos) ;
                $activeSheet->setCellValue( num2col($start_col+1).$row, $neg);
                
                $activeSheet->setCellValue( num2col($start_col+2).$row, $scr );
                $activeSheet->getStyle( num2col($start_col+2).$row )->getNumberFormat()->setFormatCode('0.00%');

                $start_col+=3 ;
                $total_scores->pos += $scores->pos ;
                $total_scores->neg += $scores->neg ;
            }
            
            $total_col = $start_col ;
            if( ($total_scores->pos + $total_scores->neg) == 0 ){
                $total_scr = '-';
            }else{
                $total_scr = $total_scores->pos / ($total_scores->pos+$total_scores->neg );
                //$total_scr = sprintf( "%.1f%%", 100* ($total_scores->pos / ($total_scores->pos+$total_scores->neg) ));
            }
                $activeSheet->setCellValue( num2col( $total_col).$row, $total_scr);
                $activeSheet->getStyle( num2col( $total_col).$row )->getNumberFormat()->setFormatCode('0.00%');

            $row++;
        }   
        $last_row = $row; 
        // average information
        $activeSheet->setCellValue( "A".$last_row , "本局各服務面項平均值");
        $activeSheet->mergeCells( sprintf("A%d:D%d", $last_row, $last_row) );

        $activeSheet->setCellValue("G".$last_row, sprintf("=AVERAGE(G6:G%d)", $last_row-1) );
        $activeSheet->getStyle( "G".$last_row )->getNumberFormat()->setFormatCode('0.00%');

        $activeSheet->setCellValue("J".$last_row, sprintf("=AVERAGE(J6:J%d)", $last_row-1) );
        $activeSheet->getStyle( "J".$last_row )->getNumberFormat()->setFormatCode('0.00%');

        $activeSheet->setCellValue("M".$last_row, sprintf("=AVERAGE(M6:M%d)", $last_row-1) );
        $activeSheet->getStyle( "M".$last_row )->getNumberFormat()->setFormatCode('0.00%');

        $activeSheet->setCellValue("P".$last_row, sprintf("=AVERAGE(P6:P%d)", $last_row-1) );
        $activeSheet->getStyle( "P".$last_row )->getNumberFormat()->setFormatCode('0.00%');

        $activeSheet->setCellValue("T".$last_row, sprintf("=AVERAGE(T6:T%d)", $last_row-1) );
        $activeSheet->getStyle( "T".$last_row )->getNumberFormat()->setFormatCode('0.00%');


        $activeSheet->getStyle(sprintf('A6:T%d',$last_row) )->applyFromArray( $allbordersStyleArray ) ;
        $activeSheet->getStyle(sprintf('A6:T%d',$last_row) )-> applyFromArray( $outlineStyleArray );

        return $objPHPExcel;
    }

}