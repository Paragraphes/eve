<?php

namespace Library\Pdf;

class generateCampagn {
	public static function generate_bkp(array $pInfos, \Library\Entities\Campagne $pCamp, $bons, $visuel, $pLocation, $anchor = array("A"=>"{CHER}", "B"=>"{TITRE}", "C"=>"{NOM}", "D"=>"{PRENOM}", "E"=>"{TELEPHON}", "F"=>"{MAIL}", "G"=>"{NP}", "H"=>"{LOCALITE}", "I"=>"{ADRESSE}")) {
		echo 1;
		//*
		$lBons = '<table style="position:absolute;bottom:9px;width:100%;height:150px;">
					<tr>';
		$i = 0;
		foreach($bons AS $bon){
			$i++;
			$lBons .= '<td style="width:' . (100/count($bons)) . '%;height:150px;" align="center">'
						. $bon->code()
					. '</td>';
		}
		echo 2;
		$lBons .= '</tr>'
				. '</table>';
		//*/
		if(!is_writable($pLocation) || !is_dir($pLocation)){
			echo 'error location';
			return 0;
		}
		echo 3;
		require('Library/Pdf/html2pdf.class.php');
		echo 4;
		$text = '';
			
		$pdf = new \HTML2PDF('P','A4','fr', true, 'UTF-8', array(0, 0, 0, 0));
		
		$n = 0;
		echo 5;
		foreach($pInfos AS $info){
				$cTxt = '<page orientation="portrait" format="' . $visuel->size() . '"><div style="position:absolute; top: 100px; right: 20px;">
	{TITRE}<br />
	{NOM} {PRENOM}<br />
	{ADRESSE}
	{NP} {LOCALITE}
	</div><div style="position: absolute;top:220px; left:10%;width:80%;">'.$pCamp->text().'</div></page>'.$lBons;
				//*
				foreach($anchor AS $key=>$ank){
					$cTxt = str_replace($ank, $info[$key], $cTxt);
				}//*/
				
				$text .= $cTxt;
		}
		echo 8;
		
		try{
			$pdf->WriteHTML($text);
		}catch(\Exception $e){
			var_dump($e);
		}
		
		echo 9;
		$pdf->Output($pLocation.'file_'.$pCamp->nom() . '.pdf', 'F');
		echo 10;
		
		return 1;
	}
	
	public static function generate(array $pInfos, \Library\Entities\Campagne $pCamp, $bons, $visuel, $pLocation, $anchor = array("A"=>"{CHER}", "B"=>"{TITRE}", "C"=>"{NOM}", "D"=>"{PRENOM}", "E"=>"{TELEPHON}", "F"=>"{MAIL}", "G"=>"{NP}", "H"=>"{LOCALITE}", "I"=>"{ADRESSE}")) {
		
		ob_start();
		
		?>
		<page format="<?php echo $visuel->size(); ?>" backtop="0mm" backbottom="0mm" backleft="0mm" backright="0mm">
		
		<div style="position:absolute; top: 100px; right: 20px;">
		{TITRE}<br />
		{NOM} {PRENOM}<br />
		{ADRESSE}
		{NP} {LOCALITE}
		</div>
		
		<div style="position: absolute;top:320px; left:10%;width:80%;">
		<?php
		echo $pCamp->text();
		?>
		</div>
		
		<table style="position:absolute;bottom:9px;width:100%;height:150px;left:0;">
		<tr>
		<?php
		//*
		foreach($bons AS $bon){
		?>
			<td style="width:<?php echo (100/count($bons));?>%;height:150px;" align="left">
				<?php echo $bon->code();?>
			</td>
			<?php
		}
		//*/
		?>
		</tr>
		</table>
		</page>
		<?php
		
		$b_content = ob_get_clean();
		
		require('Library/Pdf/html2pdf.class.php');

		$content = "";
		$pdf = new \HTML2PDF('P','A4','fr', true, 'UTF-8', array(0, 0, 0, 0));
		
		$grp = 500;
		
		for ($i = 2; $i < 1000; $i++) {
			$content .= $b_content;
			foreach ($pInfos[$i] AS $key => $val) {
				if(isset($anchor[$key])){
					$content = str_replace($anchor[$key], $val, $content);
				}
				
			}
			
			if($i%$grp == 0){
				$pdf->writeHTML($content);
			
				$pdf->Output($pLocation.'test_' . ($i/$grp) . '.pdf', 'F');

				$content = "";
				$pdf = new \HTML2PDF('P','A4','fr', true, 'UTF-8', array(0, 0, 0, 0));
			}
		}
		
		if(($i-1)%$grp != 0){
			$pdf->writeHTML($content);
				
			$pdf->Output($pLocation.'test_' . ((($i-($i%$grp))/$grp)+1) . '.pdf', 'F');
		}
		
	}
}

?>