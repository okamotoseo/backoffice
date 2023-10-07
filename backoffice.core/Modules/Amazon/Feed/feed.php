  <?php
<?xml version="1.0" encoding="iso-8859-1"?>
 <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
 <Header>
   <DocumentVersion>1.01</DocumentVersion>
   <MerchantIdentifier>********</MerchantIdentifier>
 </Header>
 <MessageType>Product</MessageType>
 <PurgeAndReplace>false</PurgeAndReplace>
 <Message>
  <MessageID>1</MessageID>
  <OperationType>Update</OperationType>
  <Product>
   <SKU>98765654356765498776565GTRF546</SKU>
   <StandardProductID>
    <Type>EAN</Type>
    <Value>45201187656</Value>
   </StandardProductID>
   <ProductTaxCode>A_TOY_GENERALL</ProductTaxCode>
   <Condition>
    <ConditionType>New</ConditionType>
   </Condition>
   <NumberOfItems>10</NumberOfItems>
   <DescriptionData>
    <Title>Smartivity EDGE Jurassic Wonders Pack With Many Features </Title>
    <Brand>Smartivity</Brand>
    <Description>Smartivity EDGE LET’S LEARN and play 1,2,3… set includes 10</Description>
    <BulletPoint>Example Bullet Point 1</BulletPoint>
    <BulletPoint>Example Bullet Point 2</BulletPoint>
    <MSRP currency="INR">410</MSRP>
    <Manufacturer>Smartivity Labss Pvt. Ltd.</Manufacturer>
    <MfrPartNumber>SMRT1025</MfrPartNumber>
    <ItemType>toy-figures</ItemType>
    <TargetAudience>Children</TargetAudience>
    <TargetAudience>unisex-adult</TargetAudience>
    <RecommendedBrowseNode>1350381031</RecommendedBrowseNode>
   </DescriptionData>
   <ProductData>
    <Toys>
     <ProductType>
      <ToysAndGames>
       <Color>Blue</Color>
       <ColorMap>Brown</ColorMap>
      </ToysAndGames>
     </ProductType>
     <AgeRecommendation>
      <MinimumManufacturerAgeRecommended unitOfMeasure="years">5</MinimumManufacturerAgeRecommended>
     </AgeRecommendation>
   </Toys>
  </ProductData>
 </Product>
</Message>
</AmazonEnvelope>

  public $feed = <<<EOD
            <?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>mymerchantid</MerchantIdentifier>
</Header>
<MessageType>Product</MessageType>
<PurgeAndReplace>true</PurgeAndReplace>
<Message>
<MessageID>1</MessageID>
<OperationType>Update</OperationType>
<Product>
<SKU>720656549</SKU>
<DescriptionData>
<Title>GIRLS S/S PRINTED COTTON AND PLITED FROCK WITH CONTRAST FRONT BELT AND BOW</Title>
<Brand>mybrand</Brand>
<Description>The Girls Racer Back Neck Tie up Tunic from Oye is the perfect choice to dress your little girl while heading out for the day.It is made of soft and pliable material, which ensures to keep her fresh and comfortable throughout the day.Pair this dress with cute ballerinas and matching hair accessories to complete the casual look.</Description>
<BulletPoint>Made in India</BulletPoint>
<BulletPoint>500 thread count</BulletPoint>
<BulletPoint>plain weave (percale)</BulletPoint>
<BulletPoint>100% Egyptian cotton</BulletPoint>
<Manufacturer>mybrand</Manufacturer>
<SearchTerms>clothes</SearchTerms>
<SearchTerms>baby girl</SearchTerms>
<ItemType>Girls</ItemType>
<IsGiftWrapAvailable>false</IsGiftWrapAvailable>
<IsGiftMessageAvailable>false</IsGiftMessageAvailable>
</DescriptionData>
<ProductData>
<Home>
<Parentage>variation-parent</Parentage>
<VariationData>
<VariationTheme>Size-Color</VariationTheme>
</VariationData>
<Material>cotton</Material>
<ThreadCount>500</ThreadCount>
</Home>
</ProductData>
</Product>
</Message>
<Message>
</AmazonEnvelope>
EOD;
    
   public $feed2 = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
    xsi:noNamespaceSchemaLocation='amzn-envelope.xsd'>
  <Header>
    <DocumentVersion>1.01</DocumentVersion>
    <MerchantIdentifier>M_EXAMPLE_123456</MerchantIdentifier>
  </Header>
  <MessageType>Product</MessageType>
  <PurgeAndReplace>true</PurgeAndReplace>
  <Message>
    <MessageID>1</MessageID>
    <OperationType>Update</OperationType>
    <Product>
      <SKU>56789</SKU>

      <StandardProductID>
        <Type>ASIN</Type>
        <Value>B0EXAMPLEG</Value>
      </StandardProductID>

      <ProductTaxCode>A_GEN_NOTAX</ProductTaxCode>

      <DescriptionData>
        <Title>Example Product Title novoooo</Title>
        <Brand>Example Product Brand</Brand>
        <Description>This is an example product description.</Description>
        <BulletPoint>Example Bullet Point 1</BulletPoint>
        <BulletPoint>Example Bullet Point 2</BulletPoint>
        <MSRP currency='USD'>25.19</MSRP>
        <Manufacturer>Example Product Manufacturer</Manufacturer>
        <ItemType>example-item-type</ItemType>
      </DescriptionData>

      <ProductData>
        <Health>
          <ProductType>
            <HealthMisc>
              <Ingredients>Example Ingredients</Ingredients>
              <Directions>Example Directions</Directions>
            </HealthMisc>
          </ProductType>
        </Health>
      </ProductData>


    </Product>
  </Message>
</AmazonEnvelope>
EOD;