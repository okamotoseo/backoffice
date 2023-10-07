<?php 
class GenderAgeGroup{
	
	public $productType;

	public function getGenderAgeGroup(){
		
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
			$genderAgeGroup['gender'] = $gender;
			$genderAgeGroup['ageGroup'] = $ageGroup;
		
			return $genderAgeGroup;
		}
	
	
}