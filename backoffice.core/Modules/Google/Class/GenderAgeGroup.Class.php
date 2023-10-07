<?php 
class GenderAgeGroup{
	
	
	public $ageGroup;
	
	public $gender;
	
	public $productType;
	
	/**
	 * Retorna o valor de(a/o) ageGroup.
	 *
	 * @return string
	*/
	public function getAgeGroup() {
	         return $this->ageGroup;
	}
	
	/**
	 * Seta o valor de(a/o) ageGroup.
	 *
	 * @param string $ageGroup
	 */
	public function setAgeGroup($ageGroup) {
	         $this->ageGroup = $ageGroup;
	}
	
	
	/**
	 * Retorna o valor de(a/o) gender.
	 *
	 * @return string
	*/
	public function getGender() {
	         return $this->gender;
	}
	
	/**
	 * Seta o valor de(a/o) gender.
	 *
	 * @param string $gender
	 */
	public function setGender($gender) {
	         $this->gender = $gender;
	}
	
		
	
	/**
	 * Retorna o valor de(a/o) productType.
	 *
	 * @return string
	*/
	public function getProductType() {
	         return $this->productType;
	}
	
	/**
	 * Seta o valor de(a/o) productType.
	 *
	 * @param string $productType
	 */
	public function setProductType($productType) {
	         $this->productType = $productType;
	}
	
		

	public function genderAgeGroup(){
		
			$parts = explode(">", $this->productType);
		
			switch(trim($parts[0])){
				case "Masculinos" :
					$gender = "male";
					$ageGroup = "adult";
					break;
				case "Femininos" :
					$gender = "female";
					$ageGroup = "adult";
					break;
				case "Infantil Meninas" :
					$gender = "female";
					$ageGroup = "kids";
					break;
				case "Infantil Meninos" :
					$gender = "male";
					$ageGroup = "kids";
					break;
				case "Casamento" :
					switch(trim($parts[1])){
						case "Noivo" :
							$gender = "male";
							$ageGroup = "adult";
							break;
						case "Pajem" :
							$gender = "male";
							$ageGroup = "kids";
							break;
						case "Daminha" :
							$gender = "female";
							$ageGroup = "kids";
							break;
						case "Padrinho" :
							$gender = "male";
							$ageGroup = "adult";
							break;
						default:
							$gender = "female";
							$ageGroup = "adult";
							break;
					}
					break;
				case "Utilidades e Acessórios" :
					switch (trim($parts[1])){
						case "Masculinos" :
							$gender = "male";
							$ageGroup = "adult";
							break;
						case "Femininos" :
							$gender = "female";
							$ageGroup = "adult";
							break;
					}
				case "Promoção" :
					switch (trim($parts[1])){
						case "Masculinos" :
							$gender = "male";
							$ageGroup = "adult";
							break;
						case "Femininos" :
							$gender = "female";
							$ageGroup = "adult";
							break;
					}
				case "Acessórios Femininos" :
					$gender = "female";
					$ageGroup = "adult";
					break;
				case "Acessórios Masculinos" :
					$gender = "male";
					$ageGroup = "adult";
					break;
				case "Viagem":
					$gender = "unisex";
					$ageGroup = "adult";
					break;
				case "Utilidades":
					$gender = "unisex";
					$ageGroup = "adult";
					break;
				case "Óculos de Sol":
					$gender = "unisex";
					$ageGroup = "adult";
					break;
				default:
					$gender = "";
					$ageGroup = "adult";
			}
			$this->setGender($gender);
			$this->setAgeGroup($ageGroup);
		}
	
}