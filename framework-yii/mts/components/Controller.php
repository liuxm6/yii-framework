<?php

Yii::import('mts.core.MtsController');
class Controller extends MtsController
{
    public $layout='//layouts/main-nav';
    public function checkAccess($item_id, $operation, $params=array(), $ret=false)
    {
        $ok = Yii::app()->authManager->checkAccess($item_id, $operation, $params);
        if (!$ok && !$ret) {
            throw new CHttpException(403, "没有访问权限");
        }
        return $ok;
    }
    public function checkLogin()
    {
        if (Yii::app()->user->isGuest) {
            Yii::app()->user->loginRequired();
        }
    }
    public function replaceUrl($params, $url=null)
    {
        if (empty($url)) $url = Yii::app()->request->requestUri;
        return url_replace_param($url, $params);
    }
    protected function beforeAction($action)
    {
        $c_id = $action->controller->id;
        $m_id = $action->controller->module->id;
        $a_id = $action->id;
        $list = array('login', 'logout', 'error');
        if (!($m_id == null && in_array($c_id, $list))) {
            $this->checkLogin();
            $ok = Yii::app()->authManager->getAccessAction($action);
            if (Yii::app()->user->name == 'admin') return true;
            if (!$ok) {
                if(isset($_POST['ajax'])) {
                    Yii::app()->end();
                }
                throw new CHttpException(403, "没有访问权限");
            }
        }
        return true;
    }
    protected function exportRowsFile($rows, $columns=array(),$export='data.xls')
    {
        try {
            Yii::import('mts.extensions.excel.PHPExcel');
            $phpExcel = new PHPExcel();
            $sheet = $phpExcel->setActiveSheetIndex(0);
            $sheet->setTitle('data');
            $col = 0;
            if (!empty($columns)) {
                foreach ($columns as $k=>$v) {
                    $name = $v;
                    $sheet->setCellValueExplicitByColumnAndRow($col++, 1, $name);
                    $sheet->getColumnDimension(chr(64+$col))->setWidth(strlen($name)+4);
                    $style = $sheet->getStyle(chr(64+$col)."1");
                    $objBorder = $style->getBorders();
                    $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objFill = $style->getFill();
                    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objFill->getStartColor()->setARGB('FFEEEEEE');
                }
                foreach ($rows as $i=>$row) {
                    $col = 0;
                    foreach ($columns as $k=>$v) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $i+2, $row->exportValue($k));
                        $col++;
                        $style = $sheet->getStyle(chr(64+$col).($i+2));
                        $objBorder = $style->getBorders();
                        $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                }
            }
            else {
                foreach ($rows[0] as $k=>$v) {
                    $name = $rows[0]->getAttributeLabel($k);
                    $sheet->setCellValueExplicitByColumnAndRow($col++, 1, $name);
                    $sheet->getColumnDimension(chr(64+$col))->setWidth(strlen($name)+4);
                    $style = $sheet->getStyle(chr(64+$col)."1");
                    $objBorder = $style->getBorders();
                    $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objFill = $style->getFill();
                    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objFill->getStartColor()->setARGB('FFEEEEEE');
                }
                foreach ($rows as $i=>$row) {
                    $col = 0;
                    foreach ($row as $k=>$v) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $i+2, $row->exportValue($k));
                        $col++;
                        $style = $sheet->getStyle(chr(64+$col).($i+2));
                        $objBorder = $style->getBorders();
                        $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                }
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$export.'"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
            $objWriter->save('php://output');
        }catch(Exception $e) {

        }
    }
    protected function exportFile($table, $columns=array(), $condition='', $export='data.xls')
    {
        try {
            $model=new $table;
            $rows = $model->findAll($condition);
            Yii::import('mts.extensions.excel.PHPExcel');
            $phpExcel = new PHPExcel();
            $sheet = $phpExcel->setActiveSheetIndex(0);
            $sheet->setTitle('data');
            $col = 0;
            if (!empty($columns)) {
                foreach ($columns as $k=>$v) {
                    $name = $v;
                    if (empty($name)) $name = $model->getAttributeLabel($k);
                    $sheet->setCellValueExplicitByColumnAndRow($col++, 1, $name);
                    $sheet->getColumnDimension(chr(64+$col))->setWidth(strlen($name)+4);
                    $style = $sheet->getStyle(chr(64+$col)."1");
                    $objBorder = $style->getBorders();
                    $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objFill = $style->getFill();
                    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objFill->getStartColor()->setARGB('FFEEEEEE');
                }
                foreach ($rows as $i=>$row) {
                    $col = 0;
                    foreach ($columns as $k=>$v) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $i+2, $row->exportValue($k));
                        $col++;
                        $style = $sheet->getStyle(chr(64+$col).($i+2));
                        $objBorder = $style->getBorders();
                        $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                }
            }
            else {
                foreach ($rows[0] as $k=>$v) {
                    $name = $model->getAttributeLabel($k);
                    $sheet->setCellValueExplicitByColumnAndRow($col++, 1, $name);
                    $sheet->getColumnDimension(chr(64+$col))->setWidth(strlen($name)+4);
                    $style = $sheet->getStyle(chr(64+$col)."1");
                    $objBorder = $style->getBorders();
                    $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $objFill = $style->getFill();
                    $objFill->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $objFill->getStartColor()->setARGB('FFEEEEEE');
                }
                foreach ($rows as $i=>$row) {
                    $col = 0;
                    foreach ($row as $k=>$v) {
                        $sheet->setCellValueExplicitByColumnAndRow($col, $i+2, $row->exportValue($k));
                        $col++;
                        $style = $sheet->getStyle(chr(64+$col).($i+2));
                        $objBorder = $style->getBorders();
                        $objBorder->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    }
                }
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$export.'"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($phpExcel, 'Excel5');
            $objWriter->save('php://output');
        }catch(Exception $e) {

        }
    }
    protected function getImportExcelFileData($model, $filecolumn='filename')
    {
        $file=CUploadedFile::getInstance($model,$filecolumn);
        $excelData = array();
        if ($file) {
            $filename=$file->getName();
            $filesize=$file->getSize();
            $filetype=$file->getType();
            if (!in_array($filetype, array(
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ))) throw new Exception("只支持excel文件格式导入");
            $model->filename=$filename;
            $filename1=iconv("utf-8", "gb2312", $filename);
            if (!is_dir("./assets/upload")) @mkdir("./assets/upload", 0777, true);
            $uploadfile="./assets/upload/".$filename1;
            $file->saveAs($uploadfile,true);
            Yii::import('mts.extensions.excel.PHPExcel');

            $phpExcel = new PHPExcel();
            $objPHPExcel = PHPExcel_IOFactory::load($uploadfile);
            //echo $objPHPExcel->getSheetCount(),' worksheet',(($objPHPExcel->getSheetCount() == 1) ? '' : 's'),' loaded<br /><br />';
            $loadedSheetNames = $objPHPExcel->getSheetNames();
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            for ($row = 1; $row <= $highestRow; ++$row) {
                $one = array();
                $error = false;
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $objWorksheet->getCellByColumnAndRow($col, $row);
                    $value=$cell->getValue();
                    if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC) {
                        $cellstyleformat=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
                        $formatcode=$cellstyleformat->getFormatCode();
                        if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode)) {
                            $value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
                        }
                        else {
                            $value=PHPExcel_Style_NumberFormat::toFormattedString($value,$formatcode);
                        }

                    }
                    else {
                        try {
                            $value = (string)$cell->getCalculatedValue();
                        }catch(Exception $e) {
                            $error = true;
                            $value = '';
                        }
                    }
                    $one[] = $value;
                }
                $excelData[] = $one;
            }
        }
        return $excelData;
    }
}