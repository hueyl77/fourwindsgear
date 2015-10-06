<?php
/**
 * Class ITwebexperts_Payperrentals_Helper_Labels
 */
class ITwebexperts_Payperrentals_Helper_Labels extends Mage_Core_Helper_Abstract
{
	/**
	 * @var TCPDF
	 */
	private $pdf;

	/**
	 * @var array
	 */
	private $startLocation = array(
		'row' => 1,
		'col' => 1
	);

    /**
     * @var
     */
    private  $labels;

	/**
	 * @var array
	 */
	private $layoutInfo = array();

    /**
     *
     */
    function PDF_Labels() {
		$this->setStartDate(date('Y-m-d'));
		$this->setEndDate(date('Y-m-d'));
		$this->setFilter('All');
		$this->labels = array();
	}

    /**
     * @param $val
     */
    function setStartDate($val) { $this->startDate = $val; }

    /**
     * @param $val
     */
    function setEndDate($val) { $this->endDate = $val; }

    /**
     * @param $val
     */
    function setFilter($val) { $this->filter = $val; }

    /**
     * @param $val
     */
    function setLabelsType($val) { $this->labelsType = $val; }

    /**
     * @param $val
     */
    function setLabelLocation($val) { $this->labelLocation = $val; }


    /**
     * @param $row
     * @param $col
     */
    public function setStartLocation($row, $col) {
		$this->startLocation = array(
			'row' => $row,
			'col' => $col
		);
	}

    /**
     * @param $col
     */
    public function setStartLocationByColmn($col){
		$this->setStartLocation(floor($col / 2) + 1, ($col % 2) + 1);
	}

    /**
     * @param $data
     */
    public function setData($data) {
		$this->labels = $data;
	}

    /**
     *
     */
    function buildHTML() {
		return $this->buildOutput('pdf');
	}

    /**
     *
     */
    function buildPDF() {
		return $this->buildOutput('pdf');
	}

    /**
     *
     */
    function buildOutput() {
		require_once(Mage::getBaseDir().'/lib/tcpdf/config/tcpdf_config.php');
		require_once(Mage::getBaseDir().'/lib/tcpdf/config/lang/eng.php');
		require_once(Mage::getBaseDir().'/lib/tcpdf/tcpdf.php');
		$this->tmpType = 'pdf';

		$printerMargin = 0;

		$this->layoutInfo = array(
			'leftMargin'   => .15625,
			'topMargin'    => .5,
			'rightMargin'  => .15625,
			'labelPadding' => .125
		);

		if ($printerMargin > $this->layoutInfo['leftMargin']){
			$this->layoutInfo['labelPadding'] = ($printerMargin - $this->layoutInfo['leftMargin']);
			$this->layoutInfo['leftMargin'] = 0;
		}
		else {
			$this->layoutInfo['leftMargin'] -= $printerMargin;
		}

		if ($printerMargin > $this->layoutInfo['topMargin']){
			$this->layoutInfo['topMargin'] = 0;
		}
		else {
			$this->layoutInfo['topMargin'] -= $printerMargin;
		}

		if ($printerMargin > $this->layoutInfo['rightMargin']){
			$this->layoutInfo['rightMargin'] = 0;
		}
		else {
			$this->layoutInfo['rightMargin'] -= $printerMargin;
		}

		$this->layoutInfo['labelPage'] = false;
		if ($this->labelsType == '5160' || $this->labelsType == '8160-s' || $this->labelsType == '8160-b' || $this->labelsType == 'barcodes'){
			$this->layoutInfo['labelPage'] = '8160';
			$this->layoutInfo['RowsPerPage'] = 10;
			$this->layoutInfo['ColsPerRow'] = 3;
			$this->layoutInfo['labelHeight'] = 1;
			$this->layoutInfo['labelWidth'] = 2.625;
			$this->layoutInfo['labelSpacerWidth'] = .15625;

			$this->layoutInfo['barcodeMaxWidth'] = $this->layoutInfo['labelWidth'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] = $this->layoutInfo['labelHeight'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] -= .125; //Allow For Text

			if ($this->labelsType == '5160' || $this->labelsType == '8160-s'){
				$buildfunction = 'buildLabel_Address';
			}elseif ($this->labelsType == 'barcodes' || $this->labelsType == '8160-b'){
				$buildfunction = 'buildLabel_Barcodes';
			}
		}
		elseif ($this->labelsType == '5164' || $this->labelsType == '8164') {
			$this->layoutInfo['labelPage'] = '8164';
			$this->layoutInfo['RowsPerPage'] = 3;
			$this->layoutInfo['ColsPerRow'] = 2;
			$this->layoutInfo['labelHeight'] = 3.3125;
			$this->layoutInfo['labelWidth'] = 4;
			$this->layoutInfo['labelSpacerWidth'] = .1875;

			$this->layoutInfo['barcodeMaxWidth'] = $this->layoutInfo['labelWidth'] - ($this->layoutInfo['labelPadding'] * 2);
			$this->layoutInfo['barcodeMaxHeight'] = 1;
			$this->layoutInfo['barcodeMaxHeight'] -= .125; //Allow For Text

			$buildfunction = 'buildLabel_ProductInfo';
		}

		if ($this->layoutInfo['labelPage'] !== false){
			$this->pdf = new TCPDF('P', 'in', array('8.5', '11.2'), true);
			$this->pdf->SetCreator('Rental Extension');
			$this->pdf->SetAuthor('Cristian Arcu');
			$this->pdf->SetTitle('Rental Product Labels');
			$this->pdf->SetSubject('Rental Product Labels');
			$this->pdf->setViewerPreferences(array(
				'PrintScaling' => 'None'
			));

			$this->pdf->SetMargins(
				$this->layoutInfo['leftMargin'],
				$this->layoutInfo['topMargin'],
				$this->layoutInfo['rightMargin'],
				true
			);
			$this->pdf->SetCellPadding($this->layoutInfo['labelPadding']);
			$this->pdf->setPrintHeader(false);
			$this->pdf->setPrintFooter(false);
			$this->pdf->SetAutoPageBreak(TRUE, .51);
			$this->pdf->setImageScale(1);
			//$this->pdf->setLanguageArray($l);
			//$this->pdf->AliasNbPages();
			$this->pdf->AddPage();
			$this->pdf->SetFont("helvetica", "", 11);

			$CurPage = 1;
			$CurrentRow = 1;
			$CurrentCol = 1;
			$labelCnt = 0;
			$lastLabel = sizeof($this->labels);
			while($CurrentRow <= $this->layoutInfo['RowsPerPage']){
				if (!isset($this->labels[$labelCnt])){
					break;
				}

				if ($CurPage == 1){
					if ($CurrentRow < $this->startLocation['row']){
						$blankRow = true;
					}
					else {
						$blankRow = false;
					}
				}
				else {
					$blankRow = false;
				}

				while($CurrentCol <= $this->layoutInfo['ColsPerRow']){
					//if ($CurPage == 1){
						if (!isset($this->labels[$labelCnt])){
							break;
						}
						if ($blankRow === true || $CurrentCol < $this->startLocation['col']){
							$blankCol = true;
						}
						else {
							//Reset to 1 so that after first output it will continue without skipping columns
							$this->startLocation['col'] = 1;
							$blankCol = false;
						}
					/*}
					else {
						$blankCol = false;
					}*/

					$newLine = ($CurrentCol == $this->layoutInfo['ColsPerRow'] ? 1 : 0);

					if ($blankCol === true){
						$lInfo = array();
					}
					else {
						$lInfo = $this->labels[$labelCnt];
						$labelCnt++;
					}
					$this->$buildfunction($lInfo, $newLine);

					$CurrentCol++;
				}

				$CurrentCol = 1;
				$CurrentRow++;
				if ($CurrentRow == $this->layoutInfo['RowsPerPage']){
					if ($lastLabel > $labelCnt){
						$CurrentRow = 1;
						$CurPage++;
					}
				}
			}

			$this->pdf->lastPage();
			$this->pdf->Output("labelSheet.pdf", "I");
			die();
		}else{
			die('PDF Error: Unknown Label Sheet Type (' . $this->labelsType . ')');
		}
	}

    /**
     * @param $labelInfo
     * @param $newLine
     */
    private function buildLabel_ProductInfo($labelInfo, $newLine) {
		$labelContent = array();
		if (($labelInfo['products_name']) != ''){
			$labelContent[] = '<b>' . $labelInfo['products_name'] . '</b>';
		}

		if (($labelInfo['products_description']) != ''){
			$labelContent[] = '<b>Description:</b> ' . (strlen($labelInfo['products_description']) > 350 ? substr($labelInfo['products_description'], 0, 350) . '...' : $labelInfo['products_description']);
		}

		if (($labelInfo['barcode']) != ''){
			$labelContent[] = '<b>Barcode:</b> ' . $labelInfo['barcode'];
			if (($labelInfo['barcode']) != ''){
				$labelContent[] = $this->getTcpdfBarcode($labelInfo);
			}
			else {
				$labelContent[] = 'Image Not Available';
			}
		}

		$this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], implode('<br>', $labelContent), 0, 'L', 0, $newLine, '', '', true, 0, true, true, 1, 'M', true);
		if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

    private function addProductNameToLabel($lInfo){
        if(Mage::helper('payperrentals/config')->includeProduct()){
            return substr($lInfo['products_name'], 0, 13) . ' - ' . $lInfo['barcode'];
        } else {return '';}
    }
    private function addAddress($lInfo){
        if ($lInfo['customers_address'] !== false){
            return $lInfo['customers_address'];
        }
    }
    /**
     * @param $lInfo
     * @param $newLine
     */
    private function buildLabel_Address($lInfo, $newLine) {
		$labelContent = array();
        if(Mage::helper('payperrentals/config')->addressFirst()){
            $labelContent[] = $this->addAddress($lInfo);
            $labelContent[] = $this->addProductNameToLabel($lInfo);
        } else {
            $labelContent[] = $this->addProductNameToLabel($lInfo);
            $labelContent[] = $this->addAddress($lInfo);
		}
        $first = 0;
        $labelText = '';
        foreach($labelContent as $iLabel){
            if($first == 1){
                $labelText .= "\n". ltrim(str_replace('<br>',"\n",str_replace('<br />',"\n", str_replace('<br/>',"\n",preg_replace( "/(?:\s\s+|\n|\t)/"," ",  $iLabel)))));
            }else{
                $labelText .= ltrim(str_replace('<br>',"\n",str_replace('<br />',"\n", str_replace('<br/>',"\n",preg_replace( "/(?:\s\s+|\n|\t)/"," ",  $iLabel)))));
            }

            $first++;
        }
        $this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], $labelText, 0, 'L', 0, $newLine, '', '', true, 0, false, true, $this->layoutInfo['labelHeight'], 'M', true);
        if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

    /**
     * @param $lInfo
     * @param $newLine
     */
    private function buildLabel_Barcodes($lInfo, $newLine) {
		$labelContent = array();
		if (($lInfo['barcode']) != ''){

			if (($lInfo['barcode_type']) != ''){
				$labelContent[] = $this->getTcpdfBarcode($lInfo);
			}
			else {
				$labelContent[] = 'Image Not Available';
			}
		}
        $first = 0;
        $labelText = '';
        foreach($labelContent as $iLabel){
            if($first == 1){
                $labelText .= "\n". ltrim(str_replace('<br>',"\n",str_replace('<br />',"\n", str_replace('<br/>',"\n",preg_replace( "/(?:\s\s+|\n|\t)/"," ",  $iLabel)))));
            }else{
                $labelText .= ltrim(str_replace('<br>',"\n",str_replace('<br />',"\n", str_replace('<br/>',"\n",preg_replace( "/(?:\s\s+|\n|\t)/"," ",  $iLabel)))));
            }

            $first++;
        }

        $this->pdf->MultiCell($this->layoutInfo['labelWidth'], $this->layoutInfo['labelHeight'], $labelText, 0, 'C', 0, $newLine, '', '', true, 0, true, true, $this->layoutInfo['labelHeight'], 'M', true);
		if ($newLine == 0){
			$this->pdf->Cell($this->layoutInfo['labelSpacerWidth'], $this->layoutInfo['labelHeight'], '');
		}
	}

    /**
     * @param $bInfo
     * @return string
     */
    private function getTcpdfBarcode($bInfo){
		$style = array(
			'position'     => '',
			'align'        => 'L',
			'stretch'      => false,
			'fitwidth'     => false,
			'cellfitalign' => '',
			'border'       => false,
			'hpadding'     => '0',
			'vpadding'     => '0',
			'fgcolor'      => array(0, 0, 0),
			'bgcolor'      => false, //array(255,255,255),
			'text'         => true,
			'font'         => 'helvetica',
			'fontsize'     => 8,
			'stretchtext'  => false
		);

		$styleQR = array(
			'border'        => 0,
			'vpadding'      => '0',
			'hpadding'      => '0',
			'fgcolor'       => array(0, 0, 0),
			'bgcolor'       => false, //array(255,255,255)
			'module_width'  => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
	   $barcodeStr = '';
		switch($bInfo['barcode_type']){
			case 'Code39':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C39', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code39CS':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C39+', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128Auto':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128A':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128A', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128B':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128B', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code128C':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'C128C', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code2of5':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'S25', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'UpcA':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'UPCA', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'UpcE':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'UPCE', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Ean8':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'EAN8', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Ean13':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'EAN13', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Codabar':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'CODABAR', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Postnet':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'POSTNET', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Code39LibR':
				break;
			case 'Code39LibL':
				break;
			case 'CodabarLibR':
				break;
			case 'CodabarLibL':
				break;
			case 'Code128Ean':
				break;
			case 'Itf14':
				break;
			case 'Planet':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'PLANET', '', '', $this->layoutInfo['barcodeMaxWidth'], $this->layoutInfo['barcodeMaxHeight'], 0.4, $style, 'N'));
				$barcodeStr = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
				break;
			case 'Pdf417':
				break;
			case 'QRCode':
				$params = $this->pdf->serializeTCPDFtagParameters(array($bInfo['barcode'], 'QRCODE', '', '', 1, 1, $styleQR, 'N'));
				$barcodeStr = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
				break;
			case 'IMail':
				break;
		}
		return $barcodeStr;
	}

}
