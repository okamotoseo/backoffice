<?php 

class CopyXmlStore
{
	private $urlXml;
	private $outputFile;
	private $msgStatus;
	
	/**
	 * Retorna o valor de(a/o) urlXml.
	 *
	 * @return string
	*/
	public function getUrlXml() {
	         return $this->urlXml;
	}
	
	/**
	 * Seta o valor de(a/o) urlXml.
	 *
	 * @param string $urlXml
	 */
	public function setUrlXml($urlXml) {
	         $this->urlXml = $urlXml;
	}
	
	/**
	 * Retorna o valor de(a/o) outputFile.
	 *
	 * @return string
	*/
	public function getOutputFile() {
	         return $this->outputFile;
	}
	
	/**
	 * Seta o valor de(a/o) outputFile.
	 *
	 * @param string $outputFile
	 */
	public function setOutputFile($outputFile) {
	         $this->outputFile = $outputFile;
	}
	
	/**
	 * Retorna o valor de(a/o) msgStatus.
	 *
	 * @return string
	*/
	public function getMsgStatus() {
	         return $this->msgStatus;
	}
	
	/**
	 * Seta o valor de(a/o) msgStatus.
	 *
	 * @param string $msgStatus
	 */
	public function setMsgStatus($msgStatus) {
	         $this->msgStatus = $msgStatus;
	}
	
	
	
	
	
	public function copytXmlStore(){
		
		if(!empty($this->urlXml)){
			
			if(url_exists($this->urlXml)){
				
				$sql = "SELECT id FROM config_xml_store";
				$formdata = mysql_fetch_assoc(mysql_query($sql));
		
				if($formdata['id']){
					$sql = "UPDATE `config_xml_store` SET `url_xml`= '{$this->urlXml}' WHERE `id` = {$formdata['id']}";
		
				}else{
					$sql = "INSERT INTO `config_xml_store`(`url_xml`) VALUES ('{$this->urlXml}')";
				}
				mysql_query($sql);
// 				unlink($this->outputFile);
// 				setLog($_SESSION['user'], $_SESSION['email']." Cadastrou um novo XML");
// 				unlink($this->outputFile);
// 				echo $this->outputFile;die;
				$res = shell_exec ("rm -rf {$this->outputFile}");
				$res = shell_exec("wget -q \"$this->urlXml\" -O $this->outputFile");
				
				
				
				if(file_exists($this->outputFile)){
					$this->msgStatus = "success|XML atualizado com sucesso!";
					return true;
				}else{
					$this->msgStatus = "error|Erro ao tentar copiar arquivo XML";
				}
		
			}else{
				$this->msgStatus = "error|Não é possivel copiar o XML. Verifique a URL informada!";
			}
		}
	}
	
	
}