<?php 

class Categories{
    
    public $URL = 'https://api.mercadolibre.com/sites/MLB/categories';
    
    public $defaultCategories;
    
    public function getMlRootCategories(){
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $this->URL);
            $data = curl_exec($ch);
            curl_close($ch);
            
            $this->defaultCategories = json_decode($data);
            
            return;

        
    }
    
    public function defaultCategoriesML(){
        
        $this->defaultCategories = '
            [
              {
                "id": "MLB5672",
                "name": "Acessórios para Veículos"
              },
              {
                "id": "MLB1499",
                "name": "Agro, Indústria e Comércio"
              },
              {
                "id": "MLB1403",
                "name": "Alimentos e Bebidas"
              },
              {
                "id": "MLB1071",
                "name": "Animais"
              },
              {
                "id": "MLB1367",
                "name": "Antiguidades"
              },
              {
                "id": "MLB1368",
                "name": "Arte e Artesanato"
              },
              {
                "id": "MLB1384",
                "name": "Bebês"
              },
              {
                "id": "MLB1246",
                "name": "Beleza e Cuidado Pessoal"
              },
              {
                "id": "MLB1132",
                "name": "Brinquedos e Hobbies"
              },
              {
                "id": "MLB1430",
                "name": "Calçados, Roupas e Bolsas"
              },
              {
                "id": "MLB1039",
                "name": "Câmeras e Acessórios"
              },
              {
                "id": "MLB1743",
                "name": "Carros, Motos e Outros"
              },
              {
                "id": "MLB1574",
                "name": "Casa, Móveis e Decoração"
              },
              {
                "id": "MLB1051",
                "name": "Celulares e Telefones"
              },
              {
                "id": "MLB1798",
                "name": "Coleções e Comics"
              },
              {
                "id": "MLB5726",
                "name": "Eletrodomésticos"
              },
              {
                "id": "MLB1000",
                "name": "Eletrônicos, Áudio e Vídeo"
              },
              {
                "id": "MLB1276",
                "name": "Esportes e Fitness"
              },
              {
                "id": "MLB263532",
                "name": "Ferramentas e Construção"
              },
              {
                "id": "MLB3281",
                "name": "Filmes e Seriados"
              },
              {
                "id": "MLB1144",
                "name": "Games"
              },
              {
                "id": "MLB1459",
                "name": "Imóveis"
              },
              {
                "id": "MLB1648",
                "name": "Informática"
              },
              {
                "id": "MLB218519",
                "name": "Ingressos"
              },
              {
                "id": "MLB1182",
                "name": "Instrumentos Musicais"
              },
              {
                "id": "MLB3937",
                "name": "Joias e Relógios"
              },
              {
                "id": "MLB1196",
                "name": "Livros"
              },
              {
                "id": "MLB1168",
                "name": "Música"
              },
              {
                "id": "MLB264586",
                "name": "Saúde"
              },
              {
                "id": "MLB1540",
                "name": "Serviços"
              },
              {
                "id": "MLB1953",
                "name": "Mais Categorias"
              }
            ]
        ';
        return json_decode($this->defaultCategories);
        
    }
    
}