<?php

class pdfreport_ficha_de_acompanhamento_xml
{
   var $Db;
   var $Erro;
   var $Ini;
   var $Lookup;
   var $nm_data;

   var $Arquivo;
   var $Arquivo_view;
   var $Tit_doc;
   var $sc_proc_grid; 
   var $NM_cmp_hidden = array();

   //---- 
   function pdfreport_ficha_de_acompanhamento_xml()
   {
      $this->nm_data   = new nm_data("pt_br");
   }

   //---- 
   function monta_xml()
   {
      $this->inicializa_vars();
      $this->grava_arquivo();
      $this->monta_html();
   }

   //----- 
   function inicializa_vars()
   {
      global $nm_lang;
      $dir_raiz          = strrpos($_SERVER['PHP_SELF'],"/") ;  
      $dir_raiz          = substr($_SERVER['PHP_SELF'], 0, $dir_raiz + 1) ;  
      $this->nm_location = $this->Ini->sc_protocolo . $this->Ini->server . $dir_raiz; 
      $this->nm_data    = new nm_data("pt_br");
      $this->Arquivo      = "sc_xml";
      $this->Arquivo     .= "_" . date("YmdHis") . "_" . rand(0, 1000);
      $this->Arquivo     .= "_pdfreport_ficha_de_acompanhamento";
      $this->Arquivo_view = $this->Arquivo . "_view.xml";
      $this->Arquivo     .= ".xml";
      $this->Tit_doc      = "pdfreport_ficha_de_acompanhamento.xml";
      $this->Grava_view   = false;
      if (strtolower($_SESSION['scriptcase']['charset']) != strtolower($_SESSION['scriptcase']['charset_html']))
      {
          $this->Grava_view = true;
      }
   }

   //----- 
   function grava_arquivo()
   {
      global $nm_lang;
      global
             $nm_nada, $nm_lang;

      $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
      $this->sc_proc_grid = false; 
      $nm_raiz_img  = ""; 
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['campos_busca']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['campos_busca']))
      { 
          $Busca_temp = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['campos_busca'];
          if ($_SESSION['scriptcase']['charset'] != "UTF-8")
          {
              $Busca_temp = NM_conv_charset($Busca_temp, $_SESSION['scriptcase']['charset'], "UTF-8");
          }
          $this->id_paciente = $Busca_temp['id_paciente']; 
          $tmp_pos = strpos($this->id_paciente, "##@@");
          if ($tmp_pos !== false)
          {
              $this->id_paciente = substr($this->id_paciente, 0, $tmp_pos);
          }
          $this->id_paciente_2 = $Busca_temp['id_paciente_input_2']; 
      } 
      $this->nm_field_dinamico = array();
      $this->nm_order_dinamico = array();
      $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['where_orig'];
      $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['where_pesq'];
      $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['where_pesq_filtro'];
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_name']))
      {
          $this->Arquivo = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_name'];
          $this->Tit_doc = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_name'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_name']);
      }
      if (!$this->Grava_view)
      {
          $this->Arquivo_view = $this->Arquivo;
      }
      if (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_sybase))
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mysql))
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mssql))
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_oracle))
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
       } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_informix))
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
       } 
      else 
      { 
          $nmgp_select = "SELECT id_paciente from (SELECT      id_paciente FROM      paciente LIMIT 1) nm_sel_esp"; 
      } 
      $nmgp_select .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['where_pesq'];
      $nmgp_order_by = $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['order_grid'];
      $nmgp_select .= $nmgp_order_by; 
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select;
      $rs = $this->Db->Execute($nmgp_select);
      if ($rs === false && !$rs->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }

      $xml_charset = $_SESSION['scriptcase']['charset'];
      $xml_f = fopen($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo, "w");
      fwrite($xml_f, "<?xml version=\"1.0\" encoding=\"$xml_charset\" ?>\r\n");
      fwrite($xml_f, "<root>\r\n");
      if ($this->Grava_view)
      {
          $xml_charset_v = $_SESSION['scriptcase']['charset_html'];
          $xml_v         = fopen($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo_view, "w");
          fwrite($xml_v, "<?xml version=\"1.0\" encoding=\"$xml_charset_v\" ?>\r\n");
          fwrite($xml_v, "<root>\r\n");
      }
      while (!$rs->EOF)
      {
         $this->xml_registro = "<pdfreport_ficha_de_acompanhamento";
         $this->id_paciente = $rs->fields[0] ;  
         $this->id_paciente = (string)$this->id_paciente;
         $this->sc_proc_grid = true; 
         $_SESSION['scriptcase']['pdfreport_ficha_de_acompanhamento']['contr_erro'] = 'on';
if (!isset($_SESSION['global_texto_ficha'])) {$_SESSION['global_texto_ficha'] = "";}
if (!isset($this->sc_temp_global_texto_ficha)) {$this->sc_temp_global_texto_ficha = (isset($_SESSION['global_texto_ficha'])) ? $_SESSION['global_texto_ficha'] : "";}
if (!isset($_SESSION['global_turno'])) {$_SESSION['global_turno'] = "";}
if (!isset($this->sc_temp_global_turno)) {$this->sc_temp_global_turno = (isset($_SESSION['global_turno'])) ? $_SESSION['global_turno'] : "";}
if (!isset($_SESSION['global_matricula'])) {$_SESSION['global_matricula'] = "";}
if (!isset($this->sc_temp_global_matricula)) {$this->sc_temp_global_matricula = (isset($_SESSION['global_matricula'])) ? $_SESSION['global_matricula'] : "";}
if (!isset($_SESSION['global_data_nascimento'])) {$_SESSION['global_data_nascimento'] = "";}
if (!isset($this->sc_temp_global_data_nascimento)) {$this->sc_temp_global_data_nascimento = (isset($_SESSION['global_data_nascimento'])) ? $_SESSION['global_data_nascimento'] : "";}
if (!isset($_SESSION['global_modalidade'])) {$_SESSION['global_modalidade'] = "";}
if (!isset($this->sc_temp_global_modalidade)) {$this->sc_temp_global_modalidade = (isset($_SESSION['global_modalidade'])) ? $_SESSION['global_modalidade'] : "";}
if (!isset($_SESSION['global_curso'])) {$_SESSION['global_curso'] = "";}
if (!isset($this->sc_temp_global_curso)) {$this->sc_temp_global_curso = (isset($_SESSION['global_curso'])) ? $_SESSION['global_curso'] : "";}
if (!isset($_SESSION['global_nome_aluno'])) {$_SESSION['global_nome_aluno'] = "";}
if (!isset($this->sc_temp_global_nome_aluno)) {$this->sc_temp_global_nome_aluno = (isset($_SESSION['global_nome_aluno'])) ? $_SESSION['global_nome_aluno'] : "";}
 $this->nome  = $this->sc_temp_global_nome_aluno ;
$this->curso  = $this->sc_temp_global_curso;
$this->modalidade  = $this->sc_temp_global_modalidade;
$data_auxiliar = $this->sc_temp_global_data_nascimento;
$this->matricula  = $this->sc_temp_global_matricula;
$this->turno  = $this->sc_temp_global_turno;
$this->texto  = $this->sc_temp_global_texto_ficha; 

$this->ano =  substr($data_auxiliar, 0, 4);
$this->mes = substr($data_auxiliar, 5, 2);
$this->dia = substr($data_auxiliar, 8, 2);

$this->dia  = $this->dia;
$this->mes  = $this->mes;
$this->ano  = $this->ano;
if (isset($this->sc_temp_global_nome_aluno)) {$_SESSION['global_nome_aluno'] = $this->sc_temp_global_nome_aluno;}
if (isset($this->sc_temp_global_curso)) {$_SESSION['global_curso'] = $this->sc_temp_global_curso;}
if (isset($this->sc_temp_global_modalidade)) {$_SESSION['global_modalidade'] = $this->sc_temp_global_modalidade;}
if (isset($this->sc_temp_global_data_nascimento)) {$_SESSION['global_data_nascimento'] = $this->sc_temp_global_data_nascimento;}
if (isset($this->sc_temp_global_matricula)) {$_SESSION['global_matricula'] = $this->sc_temp_global_matricula;}
if (isset($this->sc_temp_global_turno)) {$_SESSION['global_turno'] = $this->sc_temp_global_turno;}
if (isset($this->sc_temp_global_texto_ficha)) {$_SESSION['global_texto_ficha'] = $this->sc_temp_global_texto_ficha;}
$_SESSION['scriptcase']['pdfreport_ficha_de_acompanhamento']['contr_erro'] = 'off'; 
         foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['field_order'] as $Cada_col)
         { 
            if (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off")
            { 
                $NM_func_exp = "NM_export_" . $Cada_col;
                $this->$NM_func_exp();
            } 
         } 
         $this->xml_registro .= " />\r\n";
         fwrite($xml_f, $this->xml_registro);
         if ($this->Grava_view)
         {
            fwrite($xml_v, $this->xml_registro);
         }
         $rs->MoveNext();
      }
      fwrite($xml_f, "</root>");
      fclose($xml_f);
      if ($this->Grava_view)
      {
         fwrite($xml_v, "</root>");
         fclose($xml_v);
      }

      $rs->Close();
   }
   //----- id_paciente
   function NM_export_id_paciente()
   {
         nmgp_Form_Num_Val($this->id_paciente, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->id_paciente))
         {
             $this->id_paciente = sc_convert_encoding($this->id_paciente, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " id_paciente =\"" . $this->trata_dados($this->id_paciente) . "\"";
   }
   //----- nome
   function NM_export_nome()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->nome))
         {
             $this->nome = sc_convert_encoding($this->nome, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " nome =\"" . $this->trata_dados($this->nome) . "\"";
   }
   //----- curso
   function NM_export_curso()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->curso))
         {
             $this->curso = sc_convert_encoding($this->curso, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " curso =\"" . $this->trata_dados($this->curso) . "\"";
   }
   //----- modalidade
   function NM_export_modalidade()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->modalidade))
         {
             $this->modalidade = sc_convert_encoding($this->modalidade, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " modalidade =\"" . $this->trata_dados($this->modalidade) . "\"";
   }
   //----- matricula
   function NM_export_matricula()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->matricula))
         {
             $this->matricula = sc_convert_encoding($this->matricula, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " matricula =\"" . $this->trata_dados($this->matricula) . "\"";
   }
   //----- turno
   function NM_export_turno()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->turno))
         {
             $this->turno = sc_convert_encoding($this->turno, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " turno =\"" . $this->trata_dados($this->turno) . "\"";
   }
   //----- texto
   function NM_export_texto()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->texto))
         {
             $this->texto = sc_convert_encoding($this->texto, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " texto =\"" . $this->trata_dados($this->texto) . "\"";
   }
   //----- dia
   function NM_export_dia()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->dia))
         {
             $this->dia = sc_convert_encoding($this->dia, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " dia =\"" . $this->trata_dados($this->dia) . "\"";
   }
   //----- mes
   function NM_export_mes()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->mes))
         {
             $this->mes = sc_convert_encoding($this->mes, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " mes =\"" . $this->trata_dados($this->mes) . "\"";
   }
   //----- ano
   function NM_export_ano()
   {
         if ($_SESSION['scriptcase']['charset'] == "UTF-8" && !NM_is_utf8($this->ano))
         {
             $this->ano = sc_convert_encoding($this->ano, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->xml_registro .= " ano =\"" . $this->trata_dados($this->ano) . "\"";
   }

   //----- 
   function trata_dados($conteudo)
   {
      $str_temp =  $conteudo;
      $str_temp =  str_replace("<br />", "",  $str_temp);
      $str_temp =  str_replace("&", "&amp;",  $str_temp);
      $str_temp =  str_replace("<", "&lt;",   $str_temp);
      $str_temp =  str_replace(">", "&gt;",   $str_temp);
      $str_temp =  str_replace("'", "&apos;", $str_temp);
      $str_temp =  str_replace('"', "&quot;",  $str_temp);
      $str_temp =  str_replace('(', "_",  $str_temp);
      $str_temp =  str_replace(')', "",  $str_temp);
      return ($str_temp);
   }

   function nm_conv_data_db($dt_in, $form_in, $form_out)
   {
       $dt_out = $dt_in;
       if (strtoupper($form_in) == "DB_FORMAT")
       {
           if ($dt_out == "null" || $dt_out == "")
           {
               $dt_out = "";
               return $dt_out;
           }
           $form_in = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "DB_FORMAT")
       {
           if (empty($dt_out))
           {
               $dt_out = "null";
               return $dt_out;
           }
           $form_out = "AAAA-MM-DD";
       }
       nm_conv_form_data($dt_out, $form_in, $form_out);
       return $dt_out;
   }
   //---- 
   function monta_html()
   {
      global $nm_url_saida, $nm_lang;
      include($this->Ini->path_btn . $this->Ini->Str_btn_grid);
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento']['xml_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['pdfreport_ficha_de_acompanhamento'][$path_doc_md5][1] = $this->Tit_doc;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML<?php echo $_SESSION['scriptcase']['reg_conf']['html_dir'] ?>>
<HEAD>
 <TITLE><?php echo $this->Ini->Nm_lang['lang_othr_chart_titl'] ?> - paciente :: XML</TITLE>
 <META http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['scriptcase']['charset_html'] ?>" />
<?php
if ($_SESSION['scriptcase']['proc_mobile'])
{
?>
  <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<?php
}
?>
 <META http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
 <META http-equiv="Last-Modified" content="<?php echo gmdate("D, d M Y H:i:s"); ?> GMT"/>
 <META http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate"/>
 <META http-equiv="Cache-Control" content="post-check=0, pre-check=0"/>
 <META http-equiv="Pragma" content="no-cache"/>
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export<?php echo $_SESSION['scriptcase']['reg_conf']['css_dir'] ?>.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/buttons/<?php echo $this->Ini->Str_btn_css ?>" /> 
</HEAD>
<BODY class="scExportPage">
<?php echo $this->Ini->Ajax_result_set ?>
<table style="border-collapse: collapse; border-width: 0; height: 100%; width: 100%"><tr><td style="padding: 0; text-align: center; vertical-align: middle">
 <table class="scExportTable" align="center">
  <tr>
   <td class="scExportTitle" style="height: 25px">XML</td>
  </tr>
  <tr>
   <td class="scExportLine" style="width: 100%">
    <table style="border-collapse: collapse; border-width: 0; width: 100%"><tr><td class="scExportLineFont" style="padding: 3px 0 0 0" id="idMessage">
    <?php echo $this->Ini->Nm_lang['lang_othr_file_msge'] ?>
    </td><td class="scExportLineFont" style="text-align:right; padding: 3px 0 0 0">
     <?php echo nmButtonOutput($this->arr_buttons, "bexportview", "document.Fview.submit()", "document.Fview.submit()", "idBtnView", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bdownload", "document.Fdown.submit()", "document.Fdown.submit()", "idBtnDown", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bvoltar", "document.F0.submit()", "document.F0.submit()", "idBtnBack", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "");
 ?>
    </td></tr></table>
   </td>
  </tr>
 </table>
</td></tr></table>
<form name="Fview" method="get" action="<?php echo $this->Ini->path_imag_temp . "/" . $this->Arquivo_view ?>" target="_blank" style="display: none"> 
</form>
<form name="Fdown" method="get" action="pdfreport_ficha_de_acompanhamento_download.php" target="_blank" style="display: none"> 
<input type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<input type="hidden" name="nm_tit_doc" value="pdfreport_ficha_de_acompanhamento"> 
<input type="hidden" name="nm_name_doc" value="<?php echo $path_doc_md5 ?>"> 
</form>
<FORM name="F0" method=post action="./" style="display: none"> 
<INPUT type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<INPUT type="hidden" name="script_case_session" value="<?php echo NM_encode_input(session_id()); ?>"> 
<INPUT type="hidden" name="nmgp_opcao" value="volta_grid"> 
</FORM> 
</BODY>
</HTML>
<?php
   }
   function nm_gera_mask(&$nm_campo, $nm_mask)
   { 
      $trab_campo = $nm_campo;
      $trab_mask  = $nm_mask;
      $tam_campo  = strlen($nm_campo);
      $trab_saida = "";
      $mask_num = false;
      for ($x=0; $x < strlen($trab_mask); $x++)
      {
          if (substr($trab_mask, $x, 1) == "#")
          {
              $mask_num = true;
              break;
          }
      }
      if ($mask_num )
      {
          $ver_duas = explode(";", $trab_mask);
          if (isset($ver_duas[1]) && !empty($ver_duas[1]))
          {
              $cont1 = count(explode("#", $ver_duas[0])) - 1;
              $cont2 = count(explode("#", $ver_duas[1])) - 1;
              if ($cont2 >= $tam_campo)
              {
                  $trab_mask = $ver_duas[1];
              }
              else
              {
                  $trab_mask = $ver_duas[0];
              }
          }
          $tam_mask = strlen($trab_mask);
          $xdados = 0;
          for ($x=0; $x < $tam_mask; $x++)
          {
              if (substr($trab_mask, $x, 1) == "#" && $xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_campo, $xdados, 1);
                  $xdados++;
              }
              elseif ($xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_mask, $x, 1);
              }
          }
          if ($xdados < $tam_campo)
          {
              $trab_saida .= substr($trab_campo, $xdados);
          }
          $nm_campo = $trab_saida;
          return;
      }
      for ($ix = strlen($trab_mask); $ix > 0; $ix--)
      {
           $char_mask = substr($trab_mask, $ix - 1, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               $trab_saida = $char_mask . $trab_saida;
           }
           else
           {
               if ($tam_campo != 0)
               {
                   $trab_saida = substr($trab_campo, $tam_campo - 1, 1) . $trab_saida;
                   $tam_campo--;
               }
               else
               {
                   $trab_saida = "0" . $trab_saida;
               }
           }
      }
      if ($tam_campo != 0)
      {
          $trab_saida = substr($trab_campo, 0, $tam_campo) . $trab_saida;
          $trab_mask  = str_repeat("z", $tam_campo) . $trab_mask;
      }
   
      $iz = 0; 
      for ($ix = 0; $ix < strlen($trab_mask); $ix++)
      {
           $char_mask = substr($trab_mask, $ix, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               if ($char_mask == "." || $char_mask == ",")
               {
                   $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
               }
               else
               {
                   $iz++;
               }
           }
           elseif ($char_mask == "x" || substr($trab_saida, $iz, 1) != "0")
           {
               $ix = strlen($trab_mask) + 1;
           }
           else
           {
               $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
           }
      }
      $nm_campo = $trab_saida;
   } 
}

?>
