<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK rel="stylesheet"  HREF="estilo.css"  TYPE="text/css">
<title>Entrada de NFe por XML</title>
</head>

<body>
<h2>Entrada de NFe por XML</h2>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
  <?php
	if (isset($_POST['chave']))
		$chave = $_POST['chave'];
	if (isset($_GET['chave']))
		$chave = $_GET['chave'];
	
	
	if ($chave)
	{
		if ($chave == '')
		{
			echo "<h4>Informe a chave de acesso!</h4>";
			exit;	
		}	
		$arquivo = "arquivos/".$chave.".xml";	
		if (file_exists($arquivo)) 
		{
			 $arquivo = $arquivo;
			$xml = simplexml_load_file($arquivo);
			// imprime os atributos do objeto criado
			
			if (empty($xml->protNFe->infProt->nProt))
			{
				echo "<h4>Arquivo sem dados de autorização!</h4>";
				exit;	
			}
				$chave = $xml->NFe->infNFe->attributes()->Id;
				$chave = strtr(strtoupper($chave), array("NFE" => NULL));
//===============================================================================================================================================		
//<ide>
	$cUF = $xml->NFe->infNFe->ide->cUF;    		    //<cUF>41</cUF>  Código do Estado do Fator gerador
    $cNF = $xml->NFe->infNFe->ide->cNF;       	    //<cNF>21284519</cNF>   Código número da nfe
    $natOp = $xml->NFe->infNFe->ide->natOp;         //<natOp>V E N D A</natOp>  Resumo da Natureza de operação
    $indPag = $xml->NFe->infNFe->ide->indPag;      //<indPag>2</indPag> 0 – pagamento à vista; 1 – pagamento à prazo; 2 - outros
    $mod = $xml->NFe->infNFe->ide->mod;            //<mod>55</mod> Modelo do documento Fiscal
    $serie = $xml->NFe->infNFe->ide->serie;    	   //<serie>2</serie> 
	$nNF =  $xml->NFe->infNFe->ide->nNF;   	       //<nNF>19685</nNF> Número da Nota Fiscal
	$dEmi = $xml->NFe->infNFe->ide->dEmi;          //<dEmi>2011-09-06</dEmi> Data de emissão da Nota Fiscal
	$dEmi = explode('-', $dEmi);
	$dEmi = $dEmi[2]."/".$dEmi[1]."/".$dEmi[0];
	$dSaiEnt = $xml->NFe->infNFe->ide->dSaiEnt;    //<dSaiEnt>2011-09-06</dSaiEnt> Data de entrada ou saida da Nota Fiscal
    $dSaiEnt = explode('-', $dSaiEnt);
	$dSaiEnt = $dSaiEnt[2]."/".$dSaiEnt[1]."/".$dSaiEnt[0];
	$tpNF = $xml->NFe->infNFe->ide->tpNF;         //<tpNF>1</tpNF>  0-entrada / 1-saída
    $cMunFG = $xml->NFe->infNFe->ide->cMunFG;     //<cMunFG>4106407</cMunFG> Código do municipio Tabela do IBGE
	$tpImp = $xml->NFe->infNFe->ide->tpImp;       //<tpImp>1</tpImp> 
	$tpEmis = $xml->NFe->infNFe->ide->tpEmis;     //<tpEmis>1</tpEmis>
	$cDV = $xml->NFe->infNFe->ide->cDV;           //<cDV>0</cDV>
	$tpAmb = $xml->NFe->infNFe->ide->tpAmb;       //<tpAmb>1</tpAmb>
	if ($tpAmb != 1)
	{
		echo "<h4>Documento emitido em ambiente de homologação!</h4>";
		exit;	
	}
	$finNFe = $xml->NFe->infNFe->ide->finNFe;     //<finNFe>1</finNFe>
	$procEmi = $xml->NFe->infNFe->ide->procEmi;   //<procEmi>0</procEmi>
	$verProc = $xml->NFe->infNFe->ide->verProc;   //<verProc>2.0.0</verProc>
//</ide>
	$xMotivo = $xml->protNFe->infProt->xMotivo;	
	$nProt = $xml->protNFe->infProt->nProt;
	
	
?>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="cor0">
      <td align="center">Chave de Acesso da NFE</td>
      <td align="center">Prot. Autorização de uso</td>
      <td  align="center">Nota</td>
      <td  align="center">Série</td>
      <td  align="center">Data Emissão</td>
      <td  align="center">Data Saída</td>
    </tr>
    <tr class="cor1">
      <td  align="left" width="0%"><input type="text" name="chave" size="55" class="cor1" value="<?php echo $chave ?>" readonly="readonly"  /></td>
      <td  align="center" width="0%"><input type="text" name="protocolo" size="10" class="cor1" title="Protocolo de autorização da NFe" value="<?php echo $nProt ?>" readonly="readonly" /></td>
      <td  align="center" width="0%"><input type="text" name="nNF" size="10" class="cor1" value="<?php echo $nNF ?>" readonly="readonly" /></td>
      <td  align="center" width="0%"><input type="text" name="serie" size="3" class="cor1" value="<?php echo $serie ?>" readonly="readonly"	 /></td>
      <td  align="center" width="0%"><input type="text" name="dt_emissao" size="10" class="cor1" value="<?php echo $dEmi ?>" readonly="readonly" /></td>
      <td  align="center" width="0%"><input type="text" name="dt_saida"   size="10" class="cor1" value="<?php echo $dSaiEnt ?>" readonly="readonly" /></td>
    </tr>
  </table>
  <?php
//===============================================================================================================================================	
// <emit> Emitente
	$emit_CPF = $xml->NFe->infNFe->emit->CPF;
	$emit_CNPJ = $xml->NFe->infNFe->emit->CNPJ;  				
	$emit_xNome = $xml->NFe->infNFe->emit->xNome; 				
	$emit_xFant = $xml->NFe->infNFe->emit->xFant;     			
//<enderEmit>
	$emit_xLgr = $xml->NFe->infNFe->emit->enderEmit->xLgr;		//<xLgr>AV. AGOSTINHO DUCCI , 409</xLgr>
	$emit_nro= $xml->NFe->infNFe->emit->enderEmit->nro; 			//<nro>.</nro>
	$emit_xBairro = $xml->NFe->infNFe->emit->enderEmit->xBairro; //<xBairro>PARQUE INDUSTRIAL</xBairro>
	$emit_cMun = $xml->NFe->infNFe->emit->enderEmit->cMun; 		//<cMun>4106407</cMun>
	$emit_xMun = $xml->NFe->infNFe->emit->enderEmit->xMun; 		//<xMun>CORNELIO PROCOPIO</xMun>
	$emit_UF = $xml->NFe->infNFe->emit->enderEmit->UF; 			//<UF>PR</UF>
	$emit_CEP = $xml->NFe->infNFe->emit->enderEmit->CEP; 		//<CEP>86300000</CEP>
	$emit_cPais = $xml->NFe->infNFe->emit->enderEmit->cPais; 	//<cPais>1058</cPais>
	$emit_xPais = $xml->NFe->infNFe->emit->enderEmit->xPais; 	//<xPais>BRASIL</xPais>
	$emit_fone = $xml->NFe->infNFe->emit->enderEmit->fone; 		//<fone>4335242165</fone>
//</enderEmit>
	$emit_IE = $xml->NFe->infNFe->emit->IE; 				   //<IE>9014134104</IE>
	$emit_IM = $xml->NFe->infNFe->emit->IM; 				   //<IM>8636</IM>
	$emit_CNAE = $xml->NFe->infNFe->emit->CNAE; 			   //<CNAE>4789099</CNAE>
	$emit_CRT = $xml->NFe->infNFe->emit->CRT; //<CRT>3</CRT>
//</emit>
	
?>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="cor0">
      <td  align="center" width="0%">DADOS DO FORNECEDOR</td>
    </tr>
  </table>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="header-fixa">
      <td width="43%"  align="left">Nome / Razão Social</td>
      <td width="37%"  align="left">CNPJ / CPF</td>
      <td width="20%"  align="left">Inscrição Estadual</td>
    </tr>
    <tr class="cor1">
      <td  align="left"><input type="text" name="razao_social" size="60" value="<?php echo $emit_xNome ?>" readonly="readonly" class="cor1" /></td>
      <td  align="left"><input class="cor1" type="text" name="cnpj" size="15" value="<?php echo $emit_CNPJ ?>" readonly="readonly" /></td>
      <td  align="left"><input type="text" class="cor1" name="IE" size="15" value="<?php echo $emit_IE ?>" readonly="readonly" /></td>
    </tr>
  </table>
  <?php
//===============================================================================================================================================		
//<dest>
     $dest_cnpj =  $xml->NFe->infNFe->dest->CNPJ;       		        //<CNPJ>01153928000179</CNPJ>
		//<CPF></CPF>
	$dest_xNome = $xml->NFe->infNFe->dest->xNome;       		      //<xNome>AGROVENETO S.A.- INDUSTRIA DE ALIMENTOS  -002825</xNome>

//***********************************************************************************************************************************************	
		

//<enderDest>
    $dest_xLgr = $xml->NFe->infNFe->dest->enderDest->xLgr;            //<xLgr>ALFREDO PESSI, 2.000</xLgr>
    $dest_nro =  $xml->NFe->infNFe->dest->enderDest->nro;     		  //<nro>.</nro>
    $dest_xBairro = $xml->NFe->infNFe->dest->enderDest->xBairro;      //<xBairro>PARQUE INDUSTRIAL</xBairro>
    $dest_cMun = $xml->NFe->infNFe->dest->enderDest->cMun;            //<cMun>4211603</cMun>
	$dest_xMun = $xml->NFe->infNFe->dest->enderDest->xMun;            //<xMun>NOVA VENEZA</xMun>
	$dest_UF = $xml->NFe->infNFe->dest->enderDest->UF;                //<UF>SC</UF>
    $dest_CEP = $xml->NFe->infNFe->dest->enderDest->CEP;              //<CEP>88865000</CEP>
	$dest_cPais = $xml->NFe->infNFe->dest->enderDest->cPais;          //<cPais>1058</cPais>
	$dest_xPais = $xml->NFe->infNFe->dest->enderDest->xPais;          //<xPais>BRASIL</xPais>
//</enderDest>
	$dest_IE = $xml->NFe->infNFe->dest->IE;                           //<IE>253323029</IE>
//</dest>
//===============================================================================================================================================			
//Totais
/*
  <total>
        <ICMSTot>
          <vBC>0.00</vBC>
          <vICMS>0.00</vICMS>
          <vBCST>0.00</vBCST>
          <vST>0.00</vST>
          <vProd>555.00</vProd>
          <vFrete>0.00</vFrete>
          <vSeg>0.00</vSeg>
          <vDesc>0.00</vDesc>
          <vII>0.00</vII>
          <vIPI>0.00</vIPI>
          <vPIS>3.62</vPIS>
          <vCOFINS>16.66</vCOFINS>
          <vOutro>0.00</vOutro>
          <vNF>555.00</vNF>
        </ICMSTot>
      </total>
*/
	
	$vBC = $xml->NFe->infNFe->total->ICMSTot->vBC;
	$vBC = number_format((double) $vBC, 2, ",", ".");
	$vICMS = $xml->NFe->infNFe->total->ICMSTot->vICMS;
	$vICMS = number_format((double) $vICMS, 2, ",", ".");
	$vBCST = $xml->NFe->infNFe->total->ICMSTot->vBCST;
	$vBCST = number_format((double) $vBCST, 2, ",", ".");
	$vST = $xml->NFe->infNFe->total->ICMSTot->vST;
	$vST = number_format((double) $vST, 2, ",", ".");
	$vProd = $xml->NFe->infNFe->total->ICMSTot->vProd;
	$vProd = number_format((double) $vProd, 2, ",", "."); 
	$vNF = $xml->NFe->infNFe->total->ICMSTot->vNF;
	$vNF = number_format((double) $vNF, 2, ",", ".");
	$vFrete = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vFrete, 2, ",", ".");
	$vSeg = number_format((double)   $xml->NFe->infNFe->total->ICMSTot->vSeg, 2, ",", ".");
	$vDesc = number_format((double) $xml->NFe->infNFe->total->ICMSTot->vDesc, 2, ",", ".");
	$vIPI = number_format((double) $xml->NFe->infNFe->total->ICMSTot->	vIPI, 2, ",", ".");	
	
?>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="cor100">
      <td  align="center" width="0%">TOTAIS</td>
    </tr>
  </table>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="header-fixa">
      <td  align="right">BC do ICMS</td>
      <td  align="right">Valor do ICMS</td>
      <td  align="right">BC ICMS ST</td>
      <td  align="right">Valor do ICMS ST</td>
      <td  align="right">Vl Total dos Produtos</td>
    </tr>
    <tr class="cor1">
      <td  align="right" width="0%"><input type="text" name="vBC" size="15" class="cor1" value="<?php echo $vBC ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vICMS" size="15" class="cor1" value="<?php echo $vICMS ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vBCST" size="15" class="cor1" value="<?php echo $vBCST ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vST" size="15" class="cor1" value="<?php echo $vST ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vProd" size="15" class="cor1" value="<?php echo $vProd ?>" readonly="readonly" /></td>
    </tr>
  </table>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="header-fixa">
      <td  align="right">Valor do Frete</td>
      <td  align="right">Valor do Seguro</td>
      <td  align="right">Desconto</td>
      <td  align="right">Vl Total do IPI</td>
      <td  align="right">Vl Total da Nota</td>
    </tr>
    <tr class="cor1">
      <td  align="right" width="0%"><input type="text" name="vFrete" size="15" class="cor1" value="<?php echo $vFrete ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vSeg" size="15" class="cor1" value="<?php echo $vSeg ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vDesc" size="15" class="cor1" value="<?php echo $vDesc ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vIPI" size="15" class="cor1" value="<?php echo $vIPI ?>" readonly="readonly" /></td>
      <td  align="right" width="0%"><input type="text" name="vNF" size="15" class="cor1" value="<?php echo $vNF ?>" readonly="readonly" /></td>
    </tr>
  </table>
  <table width="40%" border="0" cellpadding="1" cellspacing="1">
    <tr class="cor100">
      <td colspan="4">Fatura/Duplicata</td>
    </tr>
    <tr class="header-fixa">
      <td width="33%">Parcela</td>
      <td width="33%">Vencimento</td>
      <td width="33%">Valor</td>
    </tr>
    <?php
	$id = 0;
	if (!empty($xml->NFe->infNFe->cobr->dup))
	{
	
    	foreach($xml->NFe->infNFe->cobr->dup as $dup) 
  		{
			$id++;
			$titulo = $dup->nDup;
			$vencimento = $dup->dVenc;
			$vencimento = explode('-', $vencimento);
			$vencimento = $vencimento[2]."/".$vencimento[1]."/".$vencimento[0];
			$vlr_parcela = number_format((double) $dup->vDup, 2, ",", ".");	
			if($id % 2 == 0)
				$class = "class='cor2'";
			else
				$class = "class='cor1'";
			
			echo
    			"<tr ".$class.">
			      <td><input $class type='text' name='titulo[]' size='15' value='$titulo' readonly='readonly' /></td>
				  <td><input $class type='text' name='vencimento[]' size='15' value='$vencimento' readonly='readonly' /></td>
			      <td><input $class type='text' name='vlr_parcela[]' size='15' value='$vlr_parcela' readonly='readonly' /></td>
			    </tr>";
    	 } 
	}?>
  </table>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="cor100">
      <td  align="center" width="100%">Itens da NFe </td>
    </tr>
  </table>
  <?php
//===============================================================================================================================================			
//Dados dos itens

?>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr class="header-fixa">
      <td width="5%" align="left">Seq</td>
      <td  width="5%" align="left">C&oacute;digo</td>
      <td  width="35%"  align="left">Descri&ccedil;&atilde;o dos produto(s)/Servi&ccedil;o(s)</td>
      <td  width="5%"  align="left">NCM</td>
      <td  width="5%"  align="left">CFOP</td>
      <td  width="5%"  align="left">Un</td>
      <td  width="5%" align="left">Qtde</td>
      <td width="5%" align="left">Vlr Unit&aacute;rio</td>
      <td  width="5%" >Vlr Prod</td>
      <td  width="5%">BC ICMS</td>
      <td  width="5%">Vlr ICMS</td>
      <td  width="5%">Vlr IPI</td>
      <td  width="5%">% ICMS</td>
      <td  width="5%">% IPI</td>
      </tr>
    <?php
	$seq = 0;
	foreach($xml->NFe->infNFe->det as $item) 
	{
		$seq++;
		$codigo = $item->prod->cProd;
		$xProd = $item->prod->xProd;
		$NCM = $item->prod->NCM;
		$CFOP = $item->prod->CFOP;
		$uCom = $item->prod->uCom;
		$qCom = $item->prod->qCom;
		$qCom = number_format((double) $qCom, 2, ",", ".");
		$vUnCom = $item->prod->vUnCom;
		$vUnCom = number_format((double) $vUnCom, 2, ",", ".");
		$vProd = $item->prod->vProd;
		$vProd = number_format((double) $vProd, 2, ",", ".");	
		$vBC_item = $item->imposto->ICMS->ICMS00->vBC;
		$icms00 = $item->imposto->ICMS->ICMS00;
		$icms10 = $item->imposto->ICMS->ICMS10;
		$icms20 = $item->imposto->ICMS->ICMS20;
		$icms30 = $item->imposto->ICMS->ICMS30;
		$icms40 = $item->imposto->ICMS->ICMS40;
		$icms50 = $item->imposto->ICMS->ICMS50;
		$icms51 = $item->imposto->ICMS->ICMS51;
		$icms60 = $item->imposto->ICMS->ICMS60;
		$ICMSSN102 = $item->imposto->ICMS->ICMSSN102; 
		if(!empty($ICMSSN102)) 
			{
				$bc_icms = "0.00";	
				$pICMS = "0	";
				$vlr_icms = "0.00";
			}		
		
		
		if (!empty($icms00))
		{
			$bc_icms = $item->imposto->ICMS->ICMS00->vBC;
			$bc_icms = number_format((double) $bc_icms, 2, ",", ".");
			$pICMS = $item->imposto->ICMS->ICMS00->pICMS;
			$pICMS = round($pICMS,0);
			$vlr_icms = $item->imposto->ICMS->ICMS00->vICMS;
			$vlr_icms = number_format((double) $vlr_icms, 2, ",", ".");
		}
		if (!empty($icms20))
		{
			$bc_icms = $item->imposto->ICMS->ICMS20->vBC;
			$bc_icms = number_format((double) $bc_icms, 2, ",", ".");
			$pICMS = $item->imposto->ICMS->ICMS20->pICMS;
			$pICMS = round($pICMS,0);
			$vlr_icms = $item->imposto->ICMS->ICMS20->vICMS;
			$vlr_icms = number_format((double) $vlr_icms, 2, ",", ".");
		}
			if(!empty($icms30)) 
			{
				$bc_icms = "0.00";	
				$pICMS = "0	";
				$vlr_icms = "0.00";
			}
			if(!empty($icms40)) 
			{
				$bc_icms = "0.00";	
				$pICMS = "0	";
				$vlr_icms = "0.00";
			}
			if(!empty($icms50)) 
			{
				$bc_icms = "0.00";	
				$pICMS = "0	";
				$vlr_icms = "0.00";
			}
			if(!empty($icms51)) 
			{
				$bc_icms = $item->imposto->ICMS->ICMS51->vBC;
				$pICMS = $item->imposto->ICMS->ICMS51->pICMS;
				$pICMS = round($pICMS,0);
				$vlr_icms = $item->imposto->ICMS->ICMS51->vICMS;
			}
		if(!empty($icms60)) 
		{
			$bc_icms = "0,00";	
			$pICMS = "0	";
			$vlr_icms = "0,00";
		}
		$IPITrib = $item->imposto->IPI->IPITrib;
		if (!empty($IPITrib))
		{
			$bc_ipi =$item->imposto->IPI->IPITrib->vBC;
			$bc_ipi = number_format((double) $bc_ipi, 2, ",", ".");
			$perc_ipi =  $item->imposto->IPI->IPITrib->pIPI;
			$perc_ipi = round($perc_ipi,0);
			$vlr_ipi = $item->imposto->IPI->IPITrib->vIPI;
			$vlr_ipi = number_format((double) $vlr_ipi, 2, ",", ".");
		}
		$IPINT = $item->imposto->IPI->IPINT;
		{
			$bc_ipi = "0,00";
			$perc_ipi =  "0";
			$vlr_ipi = "0,00";
		}	
		if($seq % 2 == 0)
			$class = "class='cor2'";
		else
			$class = "class='cor1'";
 
?>
    <tr <?php echo $class ?> >
      <td align="center" ><?php echo $seq ?></td>
      <td><input type="text" name="codigo[]" size="8" <?php echo $class ?> value="<?php echo $codigo ?>" readonly="readonly" /></td>
      <td><input type="text" name="xProd[]" <?php echo $class ?> size="60" value="<?php echo $xProd ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="NCM[]" size="8" value="<?php echo $NCM ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="CFOP[]" size="4" value="<?php echo $CFOP ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="uCom[]" size="2" value="<?php echo $uCom ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="qCom[]" size="10" value="<?php echo $qCom ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="vUnCom[]" size="10" value="<?php echo $vUnCom ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="vProd[]" size="10" value="<?php echo $vProd ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="bc_icms[]" size="10" value="<?php echo $bc_icms ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="vlr_icms[]" size="10" value="<?php echo $vlr_icms ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?>	 name="vlr_ipi[]" size="5" value="<?php echo $vlr_ipi ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="pICMS[]" size="5" value="<?php echo $pICMS ?>" readonly="readonly" /></td>
      <td><input type="text" <?php echo $class ?> name="perc_ipi[]" size="5" value="<?php echo $perc_ipi ?>" readonly="readonly" /></td>
      
    </tr>
    <?php } ?>
  </table>
  <?php
//===============================================================================================================================================			
//Autorização
	$xMotivo = $xml->protNFe->infProt->xMotivo;	
	$nProt = $xml->protNFe->infProt->nProt;
		}
		else
		{
			echo "<h4>Não existe o arquivo com a chave ".$chave." informada!</h4>";
		}
		
		
	}
?>
 

</form>
</body>
</html>