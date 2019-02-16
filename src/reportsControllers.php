<?php
use Zend\Config\Config;
use Zend\Config\Factory;


class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = 'https://romancini.com.br/img/romancini.png';
		$array = explode('.', $image_file);
		$extension = end($array);
		$this->Image($image_file, 7, 7, '', 12, $extension, '', 'T', false, 300, '', false, false, 0, false, false, false);
    $this->RoundedRect(5, 5, 200, 15, 2);
		$this->SetFont('helvetica', '', 8);
    $this->SetTextColor(0,0,0);

    // Title
    $this->SetY(6);    
    $this->Cell(188, 0, ('linha1 acentuação'), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $this->SetY(10.6);
    $this->Cell(188, 0, utf8_encode('linha2'), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    $this->SetY(15.2);
    $this->Cell(188, 0, utf8_encode('linha3'), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

    // // Line break
    $this->Ln(1);

	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-7);
		// Set font
		$this->SetFont('helvetica', 'I', 8);
		// Page number
		//$this->Cell(97, 5.5, 'Proposta Nº.: '.$GLOBALS['nProposta'], 0, false, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(113, 5.5, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

    // if ($this->PageNo() > 1) {
    //   $this->RoundedRect(5, 25, 200, 265, 2);

    // }

  }

  public function cabecalho($header,$data, $width) {
    // Colors, line width and bold font
    $config = Factory::fromFile('../config/config.php', true);

    $r = floatval($config['customFillHeader'][0]);
    $g = floatval($config['customFillHeader'][1]);
    $b = floatval($config['customFillHeader'][2]);
    $this->SetFillColor($r, $g, $b);
    $this->SetTextColor(255);


    //$this->SetDrawColor(128, 0, 0);
    $this->SetDrawColor(255, 255, 255);
    $this->SetLineWidth(0.2);
    $this->SetFont('', 'B');

    $w = $width;
    $num_headers = count($header);
    $this->SetX(7);

    for($i = 0; $i < $num_headers; ++$i) {
      if ($i == 0) {
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'L', 1);
      } else if ($i == $num_headers-1){
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'R', 1);
      } else {
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
      }
    }

    $this->Ln();
    // Color and font restoration
    $r = floatval($config['customFillZebra'][0]);
    $g = floatval($config['customFillZebra'][1]);
    $b = floatval($config['customFillZebra'][2]);
    $this->SetFillColor($r, $g, $b);
    $this->SetTextColor(0);
    $this->SetFont('');
  }

  // Colored table
    public function ColoredTable($header,$data, $width) {
			$w = $width;
      $this->cabecalho($header,$data, $width);
      // Data
      $fill = 0;

      foreach($data as $row) {
          $row = array_values($row);
          $this->SetX(7);

          $line = $this->getNumLines($row[0]);


          if ($this->GetY() > 258) { //previne que fique tabela partida
            $this->AddPage();
            $this->SetY($this->GetY()+3);
            $this->SetX($this->GetX()+1.5);
            $this->cabecalho($header,$data, $width);
            $this->SetX($this->GetX()+1.5);
          }


          $this->MultiCell($w[0], $line*5, $row[0], 'LR', 'L', $fill, 0, '', '', true, 0, false, true, 0, 'M', true);
          $this->MultiCell($w[1], $line*5, number_format($row[1], 2, ',', '.'), 'LR', 'C', $fill, 0, '', '', true, 0, false, true, 0, 'M', true);
          $this->MultiCell($w[2], $line*5, number_format($row[2], 2, ',', '.'), 'LR', 'C', $fill, 0, '', '', true, 0, false, true, 0, 'M', true);
          $this->MultiCell($w[3], $line*5, number_format($row[3], 2, ',', '.'), 'LR', 'C', $fill, 1, '', '', true, 0, false, true, 0, 'M', true);


          // $this->Ln();
          $fill=!$fill;
      }
      $this->SetX(7);
      $this->Cell(array_sum($w), 0, '', 'T');
  }
}

?>
