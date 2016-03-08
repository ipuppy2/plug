<?php
// namespace vendor\phpExcel;
require __DIR__.'/PHPExcel/IOFactory.php';
class Excel {

	private static $_obj=null;

	public static function getInstance(){
		if(null!==static::$_obj) return static::$_obj;
		return (static::$_obj=new static());	}

	/**
	 * 读取 excel 的内容
	 * @param  [type] $filename [description]
	 * @param  string $encode   [description]
	 * @return [type]           [description]
	 */
	public function read($filename,$encode='utf-8'){
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');

		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($filename);



		$sheetCount=$objPHPExcel->getSheetCount();

		$excelDatas=[];
		for($i=0;$i<$sheetCount;$i++){
			// $objPHPExcel->setActiveSheet($i);
			$objWorksheet = $objPHPExcel->getSheet($i);
			$highestRow = $objWorksheet->getHighestRow(); 
			$highestColumn = $objWorksheet->getHighestColumn(); 

			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

			$sheetDatas = []; 
			for ($row = 1; $row <= $highestRow; $row++) {

				for ($col = 0; $col < $highestColumnIndex; $col++) { 

					print_r($objWorksheet->getCellByColumnAndRow($col, $row));

					$sheetDatas[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				} 
			}

			$excelDatas[]=$sheetDatas;
		}



		
		// return $excelDatas;
	}

	/**
	 * 读取 excel 的图片
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	public function readImage($file){
		require_once __DIR__.'/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');  

		$objPHPExcel = $objReader->load($file);  //载入文件

		is_dir($dir='./images/') or mkdir($dir);

		$sheetCount=$objPHPExcel->getSheetCount();

		for($i=0;$i<$sheetCount;$i++){

			foreach ($objPHPExcel->getSheet($i)->getDrawingCollection() as $k => $drawing) {
			        

				if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
			        ob_start();
			        call_user_func(
			            $drawing->getRenderingFunction(),
			            $drawing->getImageResource()
			        );
			        $imageContents = ob_get_contents();
			        ob_end_clean();
			        
			        switch ($drawing->getMimeType()) {
			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG :
			                $extension = 'png';
			            break;

			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF:
			                $extension = 'gif';
			            break;

			            case PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG :
			                $extension = 'jpg';
			            break;

			        }
			    } else {
			        $zipReader = fopen($drawing->getPath(),'r');
			        $imageContents = '';
			        while (!feof($zipReader)) {
			            $imageContents .= fread($zipReader,1024);
			        }
			        fclose($zipReader);
			        $extension = $drawing->getExtension();
			    }


			    file_put_contents($dir.$i.'-'.$k.'.'.$extension,$imageContents);
			}
		}
	}

}