{"sku":"produto_pai",
 "ean":"21313213131",
 "ncm":"111",
 "name":"Nome do produto pai",
 "external":"Codigo_do_produto_pai_no_outro_sistema",
 "quantity":2,
 "special_price":9.90,
 "price":12.45,
 "short_description":"Descrição resumida do produto",
 "description": "descrição detalada do produto",
 "brand":"marca",
 "cost":2.21,
 "warranty_time":3,
 "warranty_message":"3 meses de garantia",
 "link":"http://site.com.br/esseproduto",
 "available":1,
 "categories":[
    {"name":"categoria base"},
    {"name":"categoria base > subcatgoria"},
    {"name":"categoria base > subcatgoria > tipo"},
    {"name":"categoria base > outrasubcategoria > outrotipo"}
 ],
 "handling_time":1,
 "manufacture_time":1,
 "dimension":{
    "length":22,
    "width":22,
    "height":22,
    "weight":22
 },
 "attributes":
    [
        {
            "code":"gender","label":"Genero","value":{"code":"f","label":"feminino"}
        } 
    ],
 "photos":[
    {
        "url": "http://www.site.com.br/enderecoimagem.jpg",
        "name": "imagem 1 do produto",
        "title": "imagem 1 do produto",
        "order": 1,
        "external": "http://www.site.com.br/enderecoimagem.jpg"
    },
    {
        "url": "http://www.site.com.br/enderecoimagem2.jpg",
        "name": "imagem 2 do produto",
        "title": "imagem 2 do produto",
        "order": 2,
        "external": "http://www.site.com.br/enderecoimagem.jpg"
    }
 ],
 
 "price_table":[
    {
     "code":"reseller",
     "label":"Preço para reseller",
     "price":5.50,
     "special_price":4.32,
     "action":"overwrite"
    },
     {
     "code":"mercadolivre",
     "label":"Preço para o mercadolivre",
     "price":22.50,
     "special_price":14.32,
     "action":"overwrite"
    },
     {
     "code":"b2w",
     "label":"Preço para a b2w",
     "action":"increase",
     "percentage":10
    }
    ],
"stock_table":[
      {
             "code":"{YOUR_CODE}",
             "type":"{own OR supplier}",
             "priority":"{0 is the high priority}",
             "quantity":2,
             "price":2.22,
             "special_price":1.22,
             "handling_time":10
      },
     {
             "code":"{YOUR_CODE_2}",
             "type":"{own OR supplier}",
             "priority":"{0 is the high priority}",
             "quantity":3,
             "price":2.22,
             "special_price":1.22,
             "handling_time":10
             }
],
"variations":[
    {
    "sku": "produto_filho",
    "ean": "21313213131",
    "name":"Nome do produto pai",
    "external":"Codigo_do_produto_pai_no_outro_sistema",
    "quantity":2,
    "special_price":9.90,
    "price":12.45,
    "attributes":
    [
        {
            "code":"size","label":"Tamanho","value":{"code":"p","label":"pequeno"}
        } 
    ],
    "stock_table":[
             {
             "code":"{YOUR_CODE}",
             "type":"{own OR supplier}",
             "priority":"{0 is the high priority}",
             "quantity":2,
             "price":2.22,
             "special_price":1.22,
             "handling_time":10
             },
             {
             "code":"{YOUR_CODE_2}",
             "type":"{own OR supplier}",
             "priority":"{0 is the high priority}",
             "quantity":3,
             "price":2.22,
             "special_price":1.22,
             "handling_time":10
             }
    ],
     "photos":[
            {
                "url": "http://www.site.com.br/enderecoimagem.jpg",
                "name": "imagem 1 do produto",
                "title": "imagem 1 do produto",
                "order": 1,
                "external": "http://www.site.com.br/enderecoimagem.jpg"
            },
            {
                "url": "http://www.site.com.br/enderecoimagem2.jpg",
                "name": "imagem 2 do produto",
                "title": "imagem 2 do produto",
                "order": 2,
                "external": "http://www.site.com.br/enderecoimagem.jpg"
            }
            ]
    }
    ]
}