<?php
/**
 * @Filename: CentralGeneral.php
 * Location: /_includes/_central
 * Function: Class containing general Library of functions that make web development much easier
 * @Creator: Stefan Harvey (SAH)
 * Changes:
 *  20130101 SAH Created
 *  20130104 SAH Added config option to select_field to select_field function to not include the id attribute
 *  20130124 SAH Added process_text function to replace undisplayed characted with proper value
 *  20130130 SAH Altered js_row_hover function to correctly return $js, and use the DOM original backgound-color 
 *			     rather than passed in value
 *  20130204 SAH Added function input_field_config, so that the input_field function can be called easier 
 *  20130205 SAH Altered function query_function to handle an 'input' type.				
 *  20130206 SAH Altered function dataTableJS to hide Showing _START_ to _END_ of _TOTAL_ entries when amount is below 
 *  20130207 SAH Removed remaining instances of <? and replaced with <?php
 *  20130212 SAH Altered function form_tags to have a config parameter, to use with a 'target' for attritute for the 
 *               form tag, so that it is possible to open a form in a new window
 *  20130213 SAH Added to the dataTables function the ability to define the maximum page numbers which had been 
 *                previously only 5. The can be acheived using array("page_numbers"=><max_pages>) in the config param. 
 *			     Also added "validation" config parement to select_field function.		
 *  20130214 SAH Added to the dataTables function the ability the starting position, so that returning to a page, the 
 *               correct starting position can be shown. Also altered the image_tag function confing options for the 
 *				 height and width attributes.
 *  20130220 SAH Added get_pagination and get_pagination_link functions for pagination
 *  20130306 SAH Created function form_label_tag by removing the label content from the form_data_row function, so that it
 *				 it can be run independantly, and added option to remove colon after label text
 *  20130326 SAH Corrected js_row_hover function so that when leaving a row it put the original background color back in				 
 *  20130327 SAH Added "heading_class" config option to form_table_header function, with it being setable from the call to 
 *				 the table_outer function.
 *  20130419 SAH Added "enctype" config option to form_tags function and added "size" config option to input_field function.
 *  20130425 SAH Added "class" option attribute tidied up var assignment in select_field function
 *  20130610 SAH Added "label_tag_class" config option to the form_label_tag function
 *  20130709 SAH Added "accept" config option to the input_field function
 *  20130712 SAH Updated get_months_array function to include the number of days in the month
 *  20131022 SAH Corrected process_text function to correctly apply remove slashes option, as it wasn't working properly
 *  20140106 SAH Added email function as found in general files on various sites, but added here for global use.
 *  20140406 SAH Made sure all functions have "public static" in front of them since this class can't be instantiated.
 *  20140416 SAH Converted file to use the doxygen standard which is the formatter of PhpDocumentor, changed class name and 
 *               function names to use camelCase to seperate words instead of using underscores
 *  20140415 SAH Altered recordVisitorData so that you can ignore the location information, and use the PHP timestamp rather
 *               than now() so that the correct UK time is recorded for date and time of the activity
 *  20140604 SAH Altered formDivRow so that it takes a outer_class config option
 *  20140608 SAH Added dateDropdown function to create the 3 dropdowns needed for selecting a data in a form. Added min
 *               and max configuration options to inputField function to be used with the HTML5 date input type. Altered formDivRow
 *               function to only show class or style params if they aren't blank, and added configuration option to stop the default
 *				 outer style param being shown at all.
 *  20140617 SAH Added makeSafe function to allow a central place to encode a mysql data. With this function, converted the
 *               recordVisitorData function to correct any bad value being used, thus to stop sql injections etc. 
 *  20140620 SAH Added autocomplete config option so that we can turn autocomplete off on password fields. In the formTags function,
 *               give option to not show the action param in the form
 *  20140625 SAH Changed the formTags function to ignore the replace of http:// if the server is https 
 *	20140630 SAH Added fileUploadJquery function for jquery to handle file uploads with	makeAsyncUploader. Altered logEntry to add 
 *               parmeter for file open mode	 
 *	20140702 SAH Altered jqueryDeleteTrigger function to use a class as well as an id.
 *  20140919 SAH Altered dataTableRow to allow a cell value array to include a cell class element as well as value and style
 *  20140930 SAH Altered recordVisitorData to include a config option of persistant_reference to keep track of unique scans to a site.
 *  20140930 SAH Altered selectField to include a config option for disabling the field.
 *  20141021 SAH Altered fileUploadJquery to have extra parameters to determine location of swfupload.swf and blankButton.png
 *  20141031 SAH Altered formDivRow so that style attributes don't appear twice when using label_style config option
 *  20141112 SAH Altered formDivRow so that style attributes don't appear twice when using content_style config option
 */
 
/*
// In order to use this file as a codeigniter model the file name must be 'centralgeneral.php' in lowercase and you must use 
class Centralgeneral extends CI_Model 
// instead of 
class CentralGeneral 
*/

namespace Central;

class CentralGeneral  
{
  //===================================================================================================================================
  /**
   * Direct output of javascript fuction to control an AJAX request
   * @param string filename of the AJAX resource 
   * @param string releative directory of the AJAX resource
   * @return none
   */
  public static function ajaxProcess($ajax_filename="ajax_process.php", $filepath="_includes/")
  {
    // self::ajaxProcess("ajax_process.php");
?>
    function ajaxProcess(request, url, additional, form_id)
    {
      request    = typeof(request)    != 'undefined' ? request : "";
      url        = typeof(url)        != 'undefined' ? url : "";
      additional = typeof(additional) != 'undefined' ? additional : "default";
      form_id    = typeof(additional) != 'undefined' ? form_id : "";
		
      if (url=="")
      {
        if (additional!="default")
        {
          while(additional.indexOf("&amp;") > 0)
          {
            additional = additional.replace("&amp;", "&");
          }
        }  
        // alert(additional);
        var reqURL = '<?php echo $filepath.$ajax_filename; ?>?request='+request+'&'+'additional='+additional;
      }
      else 
      {
        var reqURL = url;
      }
        
      // alert(reqURL); 
      var request_type = "GET";	
      var params       = null;
      if (form_id!="")
      {
        params = "";
        try
        {
          $('#'+form_id).each(function()
          {
            if ($(this).val()!="")
            {
              if (params!="")
                params += "&";
              params += $(this).attr("id")+"="+$(this).val();
            }
          });
        }
        catch(err)
        {
          alert(err.message);
        }
                
        request_type = "POST";
        // alert(params);
        if (params=="")
          params = null;
      }

      var ajaxReq = new XMLHttpRequest();
      // alert(reqURL);
      ajaxReq.open(request_type, reqURL, false);
      ajaxReq.send(params);
      if(ajaxReq.status!=200)
      {
        alert("Ajax Error:"+ajaxReq.status);
      }
	  else
      {
        // alert("contact established");
        var serverResponse = ajaxReq.responseText;
        if (serverResponse!="")
        {
          // alert(serverResponse);
          eval(serverResponse); 
        }
        else alert("Server Response Blank!");
			
      }
    }
	<?php
  }
  //===================================================================================================================================

  //===================================================================================================================================
  /**
   * Extended Jquery code used for pagination for a specific Jquery plugin for table row paging
   * @param string id of the table being used 
   * @param int total number of rows shown within the table
   * @param int number of rows to be shown on each page
   * @param array configuration options for the function
   * @return string javascript used in the plugin call
   */
  public static function dataTableJS($table, $data_rows, $rows_per_page, $config=array())
  {
    if ((isset($config["page_numbers"])) && ($config["page_numbers"]!=""))
      $page_numbers = $config["page_numbers"];	
    else $page_numbers = 5;
		
    if ((isset($config["start_page"])) && ($config["start_page"]!=""))
      $start = ($config["start_page"] * $rows_per_page);	
    else $start = 0;
		
    return "$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )\n".
		   "{\n".
   		   "\treturn {\n".
           "\t\t\"iStart\":         oSettings._iDisplayStart,\n".
           "\t\t\"iEnd\":           oSettings.fnDisplayEnd(),\n".
           "\t\t\"iLength\":        oSettings._iDisplayLength,\n".
           "\t\t\"iTotal\":         oSettings.fnRecordsTotal(),\n".
		   "\t\t\"iFilteredTotal\": oSettings.fnRecordsDisplay(),\n".
		   "\t\t\"iPage\":          oSettings._iDisplayLength === -1 ? 0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),\n".
		   "\t\t\"iTotalPages\":    oSettings._iDisplayLength === -1 ? 0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)\n".
		   "\t};\n".
		   "};\n".
		   "var o".$table." = '';\n".
		   "$(document).ready(function()\n".
		   "{\n".
		   "\tjQuery.fn.dataTableExt.oPagination.iFullNumbersShowPages = ".$page_numbers.";\n".
		   "\to".$table." = $('#".$table."').dataTable(\n".
		   "{\n".
		   ($data_rows>$rows_per_page?"\t\t\"bPaginate\": true,":"\t\t\"bPaginate\": false,")."\n".
		   "\t\t\"sPaginationType\": \"full_numbers\",\n".
		   "\t\t\"bLengthChange\": false,\n".
		   "\t\t\"iDisplayStart\": ".$start.",\n".
		   "\t\t\"iDisplayLength\": ".$rows_per_page.",\n".
		   "\t\t\"bSort\":false,\n".
		   "\t\t\"bFilter\": false,\n".
		   "\t\t\"bAutoWidth\": false,\n".
		   "\t\t\"fnDrawCallback\": function(oSettings)\n".
		   "\t\t{\n".
		   "\t\t\tif ($('#".$table." tbody tr').length < ".((int)$rows_per_page+1).")\n".
		   "\t\t\t{\n".
		   "\t\t\t\t$('#details_list_info').hide();\n".
		   "\t\t\t}\n".
		   "\t\t},\n".
		   "\t\t\"bAutoWidth\": false\n".
		   "\t});\n".
		   "});\n".
		   "function get_datatable_page_no()\n".
		   "{\n".
		   "\treturn o".$table.".fnPagingInfo().iPage;\n".
		   "}\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return html form tags from passed in parameters
   * @param string id of the form
   * @param string content within form tags
   * @param string the action of the form
   * @param string the method of the form
   * @param array configuration options for the function
   * @return string complete form tags
   */	
  public static function formTags($id="form_id", $content, $action="", $method="post", $config="")
  {
    if ($action=="no_action")
		$action = "";
	else
	{
		if ($action=="")
     		 $action =$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    	if (substr($action, 0, 5) != "https")
			$action = "http://".str_replace("http://", "", $action);
	}

    if ((isset($config["target"])) && ($config["target"]!=""))
	  $target = $config["target"];
    else $target = "";

    if ((isset($config["enctype"])) && ($config["enctype"]!=""))
	  $enctype = $config["enctype"];
    else $enctype = "";

    $output = "<form id=\"".$id."\" ".
			      "name=\"".$id."\" ".
			    "method=\"".$method."\"".
		      ($action !=""?" action=\"". $action ."\"":"").
			  ($target !=""?" target=\"". $target ."\"":"").
		      ($enctype!=""?" enctype=\"".$enctype."\"":"").
		      ">\n".
		      $content.
		      "</form>\n";

    return $output;
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
   * Return html for a table from passed in params
   * @param string title / heading shown at the top of the table
   * @param int number of cells the title will span 
   * @param string contents of the table
   * @param string the headings for the columns in the table
   * @param array configuration options for the function
   * @return string complete table tags
   */	
  public static function tableOuter($title, $colspan, $table_contents, $row_headings="", $config="")
  {
    $table_style = "border-radius:5px;";
    if (is_array($config) && (isset($config["table_style"])) && ($config["table_style"]!=""))
 	  $table_style .= $config["table_style"];		

    if ((!isset($config["no_graph"])) || ($config["no_graph"]==false))
	  $class = "graph";
    else $class = "";

    if (is_array($config) && (isset($config["additional-class"])) && ($config["additional-class"]!=""))
      $class = trim($class." ".$config["additional-class"]);

    $table_id = "";
    if (is_array($config) && (isset($config["table_id"])) && ($config["table_id"]!=""))
      $table_id .= " id=\"".trim($config["table_id"])."\"";
    else $table_id = "";
		
    if (is_array($config) && (isset($config["tbody_id"])) && ($config["tbody_id"]!=""))
      $tbody_id .= " id=\"".trim($config["tbody_id"])."\"";
    else $tbody_id = "";

    if (is_array($config) && (isset($config["tbody_style"])) && ($config["tbody_style"]!=""))
      $tbody_style .= " style=\"".trim($config["tbody_style"])."\"";	
    else $tbody_style = "";

    $heading_config = array();
    if (is_array($config) && (isset($config["heading_style"])) && ($config["heading_style"]!=""))
	  $heading_config["heading_style"] = $config["heading_style"];	
	
    if (is_array($config) && (isset($config["heading_class"])) && ($config["heading_class"]!=""))
      $heading_config["heading_class"] = $config["heading_class"];		
	
    if (is_array($config) && (isset($config["table_footer"])) && ($config["table_footer"]!=""))
      $table_footer =  "<tfoot>\n".$config["table_footer"]."</tfoot>\n";		
    else $table_footer = "";

    $border = "";
    if ((is_array($config)) && (isset($config["border"])) && ($config["border"]!=""))
	  $border = " border=\"".$config["border"]."\"";	

    $output  = "<table style=\"".$table_style."\" class=\"".trim($class)."\"".$table_id.$border.">\n";
		   
    if (($title!="") || (is_array($row_headings)))		   
      $output .= self::formTableHeader($title, $colspan, $row_headings, $heading_config);

    $output .= "<tbody".$tbody_id.$tbody_style.">\n".
		       $table_contents.
		       "</tbody>\n".
		       $table_footer.
		       "</table>\n";

    return $output;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML for a title row and column headings for a table to be displayed from passed in params
   * @param string title / heading shown at the top of the table
   * @param int number of cells the title will span 
   * @param array headings for the table content if any 
   * @param array configuration options for the function
   * @return string complete title / column headings tags
   */
  public static function formTableHeader($title="", $colspan="2", $row_headings="", $config="")
  {
    if (($colspan=="") && (is_array($row_headings)))
      $colspan = count($row_headings);

    $heading_style= '';	

    if ((!isset($config["heading_class"])) || ($config["heading_class"]==""))
    {
      if ((isset($config["heading_style"])) && ($config["heading_style"]!=""))
        $heading_style = $config["heading_style"];
      else $heading_style = "background-color:#CCCCCC;";
      $class = "table_header";
    }
    else $class = $config["heading_class"];

    if ((isset($config["th_style"])) && ($config["th_style"]!=""))
      $th_style = " style=\"".$config["th_style"]."\"";
    else $th_style =  "";

    $output = "<thead>\n";

    if ($title!="")
      $output .= "<tr>\n".
                 "<th colspan=\"".$colspan."\" style=\"".$heading_style."padding-top:5px;padding-bottom:5px;\"".(isset($class)?" class=\"".$class."\"":"").">".
                 $title.
                 "</th>\n".
                 "</tr>\n";
    if (is_array($row_headings))
    {
      $output .= "<tr>\n";
      foreach($row_headings as $row_heading)
      {
        $th_class = "";
		if (is_array($row_heading))
        {  
          $heading_style = " style=\"".$row_heading["style"]."\"";
		  if (isset($row_heading["class"]))
		  	$th_class = " class=\"".$row_heading["class"]."\"";
          $heading_val   = $row_heading["value"];				
        }
        else 
        {
          $heading_style = $th_style;
          $heading_val   = $row_heading;
        }

        $output .= "<th".$heading_style.$th_class.">".$heading_val."</th>\n";
      }
      $output .= "</tr>\n";	
    }

    $output .= "</thead>\n";
    return $output;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML for a row of table with only 1 cell
   * @param string single cell content
   * @param int number of cells this single cell will span 
   * @param array configuration options for the function
   * @return string html of the single cell row
   */
  public static function singleCellRow($content, $colspan, $config="")
  {
    if ((isset($config["cell_style"])) && ($config["cell_style"]))
      $cell_style = " style=\"".$config["cell_style"]."\"";
	else $cell_style = "";
				
    if ((isset($config["cell_class"])) && ($config["cell_class"]!=""))
      $cell_class = " class=\"".$config["cell_class"]."\"";
    else $cell_class = "";
		
    if ((isset($config["tr_id"])) && ($config["tr_id"]!=""))
	  $tr_id = " id=\"".$config["tr_id"]."\"";
    else $tr_id = "";

    if ((isset($config["tr_class"])) && ($config["tr_class"]!=""))
	  $tr_class = " class=\"".$config["tr_class"]."\"";
    else $tr_class = "";

    if ((isset($config["tr_style"])) && ($config["tr_style"]!=""))
	  $tr_style = " style=\"".$config["tr_style"]."\"";
    else $tr_style = "";

    return "<tr".$tr_id.$tr_class.$tr_style.">\n".
		   "<td colspan=\"".$colspan."\"".$cell_style.$cell_class.">\n".
		   $content.
		   "</td>\n".
		   "</tr>\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML for a row of table with only 2 cells, generally used for a form field with the label on the left and field on the right
   * @param string content of the left hand cell
   * @param string content of the right hand cell
   * @param string css styling for the left cell
   * @param string css styling for the right cell
   * @param string css styling for table row
   * @param array configuration options for the function   
   * @return string html of the single cell row
   */
  public static function tableRow($left, $right, $left_style="", $right_style="", $tr_style="", $config="")
  {
    if ($left_style=="")
      $left_style="font-weight:bold;";
    if ($right_style=="")
      $right_style = "font-weight:normal;";

    if ((isset($config["tr_id"])) && ($config["tr_id"]!=""))
      $tr_id = " id=\"".$config["tr_id"]."\"";
    else $tr_id = "";

    if ((isset($config["tr_class"])) && ($config["tr_class"]!=""))
      $tr_class = " class=\"".$config["tr_class"]."\"";
    else $tr_class = "";

    if ($tr_style=="")
    {
      if ((isset($config["tr_style"])) && ($config["tr_style"]!=""))
      $tr_style = " style=\"".$config["tr_style"]."\"";
      else $tr_style = "";
    }

    if ((isset($config["left_class"])) && ($config["left_class"]!=""))
    {
      $left_class = " class=\"".$config["left_class"]."\"";
      if ((!isset($config["inc_label_style"])) || ($config["inc_label_style"]==false))
        $left_style = "";
    }
    else $left_class = "";	

    if ((isset($config["right_class"])) && ($config["right_class"]!=""))
    {
      $right_class = " class=\"".$config["right_class"]."\"";
      $right_style = "";
    }
    else $right_class = "";	

    return "<tr".$tr_id.$tr_class.$tr_style.">\n".
           "<td style=\"".$left_style."\"".$left_class.">\n".
           $left.
           "</td>\n".
           "<td style=\"".$right_style."\"".$right_class.">\n".
           $right.
           "</td>\n".
           "</tr>\n";
  }
  //===================================================================================================================================
	
	
  //===================================================================================================================================
  /**
   * Return HTML for a row of table containing detailed information, such as multiple records from a database
   * @param array content of the cells in the row
   * @param string css style of the row
   * @param string css class of the row
   * @param string id of the row
   * @param array configuration options for the function   
   * @return string html of the single cell row
   */
  public static function dataTableRow($data, $row_style="font-weight:normal;", $class="", $id="", $config="")
  {
    // self::dataTableRow($data, $row_style="font-weight:bold;")
    $output = "<tr style=\"".$row_style."\"".
	          ($class!=""?" class=\"".$class."\"":"").
	          ($id!=""?" id=\"".$id."\"":"").
	          ">\n";
	 
    if ((isset($config["colspan"])) && ($config["colspan"]!=""))
      $colspan = " colspan=\"".$config["colspan"]."\"";
    else $colspan = "";

    foreach($data as $column)
    {

      if (is_array($column))
      {
        $cell_value = $column["value"];
        $cell_style = $column["style"];
		$cell_class = $column["class"];
      }
      else
      {
        $cell_value = $column;
        $cell_style = $row_style;	
		$cell_class = "";
      }
      $output .= "<td style=\"vertical-align:top;".str_replace('"', '', $cell_style)."\"".
	  			 ($cell_class!=""?" class=\"".$cell_class."\"":"").
				 $colspan.">".
	  			 $cell_value.
				 "</td>\n";			
    }
    $output .= "</tr>\n";
    return $output;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
    * Return HTML label tag
    * @param field name of the label
    * @param label content
    * @param array configuration options for the function   
    * @return string html of the label tag
    */		
  public static function formLabelTag($field_name, $label, $config="")
  {
    
	 if ((!isset($config["no_label_style"])) || ($config["no_label_style"]=="false"))
	 {
    	if ((isset($config["label_tag_style"])) && ($config["label_tag_style"]!=""))
      		$label_tag_style .= $config["label_tag_style"];
		else $label_tag_style = "font-weight:bold;";
	 }
	 else $label_tag_style = "";

    $label_tag_class = "";	
    if ((isset($config["label_tag_class"])) && ($config["label_tag_class"]!=""))
	  $label_tag_class = $config["label_tag_class"];	

    if ((!isset($config["no_label_tag"])) || ($config["no_label_tag"]=="false"))
    {
      $label = ((!isset($config["no_colon"])) || ($config["no_colon"]=="false")?$label.":":$label);
      $label = "<label for=\"".$field_name."\"".
	  		   ($label_tag_style!=""?" style=\"".$label_tag_style."\"":"").
			   ($label_tag_class!=""?" class=\"".$label_tag_class."\"":"").
			   ">".
			   $label.
			   "</label>";
    }
	
    if ((isset($config["label_outer_start"])) && ($config["label_outer_start"]!=""))	
      $label = $config["label_outer_start"].$label;
		
    if ((isset($config["label_outer_end"])) && ($config["label_outer_end"]!=""))	
	  $label .= $config["label_outer_end"];		
	
    if ((isset($config["label_extra"])) && ($config["label_extra"]!=""))
      $label .= $config["label_extra"];	
	
    return $label;	
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
    * Return HTML for a table row containing two cells which are the label and field of a form field
    * @param string field name of the label
    * @param string label content
    * @param string form field
	* @param string left cell style
	* @param string right cell style
	* @param array configuration options for the function   
    * @return string html of the table row contain a form field and label  tag
    */			
  public static function formTableRow($field_name, $label, $content, $label_style="", $content_style="", $config="")
  {
    $label = self::formLabelTag($field_name, $label, $config);

    if ($label_style=="")
      $label_style = "width:50px;padding:3px;white-space:nowrap;";

    if ($content_style=="")
      $content_style = "padding:3px;padding-top:6px;white-space:nowrap;";

    $tr_config = array();

    if ((isset($config["tr_style"])) && ($config["tr_style"]!=""))
    {
      $tr_config["tr_style"] =  $config["tr_style"];
      $tr_style = $config["tr_style"];
    }

    if ((isset($config["tr_id"])) && ($config["tr_id"]!=""))
      $tr_config["tr_id"] =  $config["tr_id"];

    if ((isset($config["tr_class"])) && ($config["tr_class"]!=""))
      $tr_config["tr_class"] = $config["tr_class"];

    if ((isset($config["left_class"])) && ($config["left_class"]!=""))
    {
      $tr_config["left_class"] = $config["left_class"];
      if ((!isset($config["inc_label_style"])) || ($config["inc_label_style"]==false))
      {
        $label_style = "";	
      }
      else
      {
        $tr_config["inc_label_style"] = true;
      }
	}

    if ((isset($config["right_class"])) && ($config["right_class"]!=""))
    {
      $tr_config["right_class"] = $config["right_class"];		
      $content_style = "";
    }

    return self::tableRow($label, $content, $label_style, $content_style, ( isset($tr_style) ? $tr_style : '' ), $tr_config);
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
  * Return HTML for a div containing sub divs which are the label and field of a form field
  * @param string name of the field
  * @param string label of the field
  * @param string field contents
  * @param array configuration options for the function   
  * @return string html of the table row contain a form field and label  tag
  */			
  public static function formDivRow($field_name, $label, $content, $config="")
  {
    $label = self::formLabelTag($field_name, $label, $config);
    
	if ((isset($config["outer_class"])) && ($config["outer_class"]!=""))
      $outer_class = $config["outer_class"];
	 else $outer_class = ""; 
	
	if ((!isset($config["no_outer_style"])) || ($config["no_outer_style"]=="false"))
	{
    	if ((isset($config["outer_style"])) && ($config["outer_style"]!=""))
      		$outer_style = $config["outer_style"];
		else $outer_style = "clear:both;float:left;";
	}
	else $outer_style = "";

    if ((isset($config["label_style"])) && ($config["label_style"]!=""))
      $label_style = $config["label_style"];
    else $label_style = "";
	
	if ((isset($config["label_div_class"])) && ($config["label_div_class"]!=""))
		$label_class = $config["label_div_class"];
    else if ((isset($config["label_class"])) && ($config["label_class"]!=""))
      $label_class = $config["label_class"];
    else $label_class = "labeldiv";
	
    if ((isset($config["content_style"])) && ($config["content_style"]!=""))
      $content_style = $config["content_style"];
    else $content_style = "";

    if ((isset($config["content_class"])) && ($config["content_class"]!=""))
      $content_class = $config["content_class"];
    else $content_class = "inputdiv";	
	
    return "<div ".
		   ($outer_style!=""?" style=\"".$outer_style."\"":"").
		   ($outer_class!=""?" class=\"".$outer_class."\"":"").">\n".
           "\t<div ".
		   ($label_class!=""?" class=\"".$label_class."\"":"").
		   ($label_style!=""?" style=\"".$label_style."\"":"").
		   ">".$label  ."</div>\n".
           "\t<div ".
		   ($content_class!=""?" class=\"".$content_class."\"":"").
		   ($content_style!=""?" style=\"".$content_style."\"":"").
		   ">".$content."</div>\n".
           "</div>\n";

  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
  * Return HTML select statement from passed in parameters
  * @param string field value
  * @param string field name / id
  * @param array containing the settings to create the select field options 
  * @param string key of options containing value of option
  * @param string key of options containing option content
  * @param array configuration options for the function   
  * @return string html of the table row contain a form field and label  tag
  */
  public static function stdSelect($field_value, $field_name, $options, $label, $id_field="id", $description_field="desc", $config="")
  {
    return self::selectField($field_value, $field_name, $options, $label, $id_field, $description_field, $config);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
  * Return HTML select statement from passed in parameters
  * @param string field value
  * @param string field name / id
  * @param array containing the settings to create the select field options 
  * @param string key of options containing value of option
  * @param string key of options containing option content
  * @param array configuration options for the function   
  * @return string html of the table row contain a form field and label  tag
  */
  public static function selectField($field_value, $field_name, $options, $label, $id_field="id", $description_field="desc", $config="")
  {
    $label         = ((!isset($config["no_select"]))   || ($config["no_select"]!="true")?"Select ".$label:$label);
    $style         = ((isset($config["style"]))        && ($config["style"]!="")        ?$config["style"]:"");
    $class         = ((isset($config["noinput"]))      && ($config["noinput"]=="true")  ?"":"input");
    $class        .= ((isset($config["class"]))        && ($config["class"]!="")        ?" ".$config["class"]:"");
    $select_style  = ((isset($config["select_style"])) && ($config["select_style"]!="") ?$config["select_style"]:"");
    $onchange      = ((isset($config["onchange"]))     && ($config["onchange"]!="")     ?" onchange=\"".$config["onchange"]."\"":"");
    $name          = ((isset($config["name"]))         && ($config["name"]!="")         ?$config["name"]:$field_name);
    $id_attribute  = ((isset($config["no_id"]))        && ($config["no_id"]==true)      ?"":"id=\"".$field_name."\" ");
    $validation    = ((isset($config["validation"]))   && ($config["validation"]!="")   ?" validation=\"".$config["validation"]."\" ":"");
    $disabled      = ((isset($config["disabled"]))     && ($config["disabled"]=="true") ?" disabled=\"disabled\"":"");
    $tabindex      = ((isset($config["tabindex"]))     && ($config["tabindex"]!="")     ?" tabindex=\"".$config["tabindex"]."\"":"");
	$placeholder   = ((isset($config["placeholder"]))  && ($config["placeholder"]!="")  ?" placeholder=\"".$config["placeholder"]."\"":"");

    $option_style  = "text-align:left;";
    $option_style .= ((isset($config["option_style"])) && ($config["option_style"]!="")?$config["option_style"]:"");	

    if ((isset($config["not_applicable"])) && ($config["not_applicable"]=="true"))
      $options = array_merge(array(array($id_field=>"not_applicable", $description_field=>"Not Applicable")), $options);
				
    $output = "<select ".
		      $id_attribute.
			  "name=\"".$name       ."\" ".
			  "class=\"".trim($class)."\"".
			  ($style!=""?" style=\"text-align:left;".$style."\"":"").
			  $disabled.
			  $tabindex.
			  $onchange.
			  $validation.
			  $placeholder.
			  ">\n".
		      "<option value=\"\" style=\"".$option_style.$select_style."\"".($field_value==""?" selected=\"selected\"":"").">".trim($label)."</option>\n";
    foreach($options as $option)
    {
      $selected = (trim($field_value)==trim(str_replace("&amp;", "&", $option[$id_field]))?" selected=\"selected\"":"");
      $disabled = (isset($option["disabled"]) && $option["disabled"]==true?" disabled=\"disabled\"":"");
	
      $opt_style = $option_style;
      $opt_style .= (isset($option["style"]) && $option["style"]==true?$option["style"]:"");
			
      $disp_option = trim($option[$description_field]);

      $disp_option = (strpos($disp_option, "&nbsp;")===false?self::ampReplace($disp_option):$disp_option);
		
      $opt_class = ((isset($option["class"])) && ($option["class"]==true) ?$option["class"]:"");	
				
      $output .= "<option value=\"".trim($option[$id_field])."\" style=\"".$opt_style."\" class=\"".$opt_class."\"".$selected.$disabled.">".
                 $disp_option.
			     "</option>\n";

    } // foreach($options as $option)
    $output .= "</select>\n";

    return $output;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML input field from passed in parameters
   * @param string field type (e.g. text, hidden, submit, button, radio, checkbox)
   * @param string field name / id
   * @param string field value
   * @param string field class
   * @param string maximum number of characters (only relevant for input[type=text])
   * @param string CSS style of field
   * @param string Javascript trigger for on click (only relevant for input[type=submit] and input[type=button]
   * @param array configuration options for the function   
   * @return string html of an input element
   */	
  public static function inputField($type, $field_name, $value, $class="", $maxlength="255", $style="", $onclick="", $config="") 
  {
	  
    if (($type=="text") && ($maxlength==""))
      $maxlength = "255";

    if (($type=="radio") && (isset($config["sub_opt"])))
      $id_field = $field_name."_".$config["sub_opt"];
    else $id_field = $field_name;
    
	$name_field = $field_name;

    $checked = "";
    if (($type=="checkbox") && (isset($config["checkbox_value"])))
    {
      if ($value==$config["checkbox_value"])
        $checked = "checked";
      $value = $config["checkbox_value"];
    }

    if (($type=="radio") && (isset($config["checked"])))
    {
      if ($config["checked"]==true)
        $checked = "checked";
      else $checked = "";	
    }

    if (isset($config["onkeypress"]))
      $onkeypress = $config["onkeypress"];
    else $onkeypress = "";

    if (isset($config["onkeydown"]))
      $onkeydown = $config["onkeydown"];
    else $onkeydown = "";

    $tabindex = '';
    if (isset($config["tabindex"]))
      $tabindex = $config["tabindex"];

    if ((isset($config["disabled"])) && ($config["disabled"]!=""))
      $disabled = $config["disabled"];
    else $disabled = "";

    if ((isset($config["validation"])) && ($config["validation"]!=""))
      $validation = $config["validation"];
    else $validation = "";

    if ((isset($config["size"])) && ($config["size"]!=""))
      $size = $config["size"];
    else $size = "";

    if ((isset($config["accept"])) && ($config["accept"]!=""))
      $accept = $config["accept"];
    else $accept = "";
	
	if ((isset($config["placeholder"])) && ($config["placeholder"]!=""))
      $placeholder = $config["placeholder"];
    else $placeholder = "";

	if ((isset($config["min"])) && ($config["min"]!=""))
      $min = $config["min"];
    else $min = "";
	
	if ((isset($config["max"])) && ($config["max"]!=""))
      $max = $config["max"];
    else $max = "";
	
	if ((isset($config["autocomplete"])) && ($config["autocomplete"]!=""))
      $autocomplete = $config["autocomplete"];
    else $autocomplete = "";

    return "<input type=\"".$type."\" ".
		            "id=\"".$id_field."\" ".
	              "name=\"".$name_field."\" ".
	             "value=\"".$value."\"".
            (!in_array($type, array("button", "submit", "hidden", "radio", "checkbox"))?" maxlength=\"".$maxlength."\"":"").
            ($class!=""       ?" class=\"".       $class."\" "      :"").
			($style!=""       ?" style=\"".       $style."\""       :"").
            ($onclick!=""     ?" onclick=\"" .    $onclick."\""     :"").
            ($checked!=""     ?" checked=\"".     $checked."\""     :"").
            ($onkeypress!=""  ?" onkeypress=\"".  $onkeypress."\""  :"").
            ($onkeydown!=""   ?" onkeydown=\"".   $onkeydown."\""   :"").
            ($tabindex!=""    ?" tabindex=\"".    $tabindex."\""    :"").
            ($disabled!=""    ?" disabled=\"".    $disabled."\""    :"").
            ($validation!=""  ?" validation=\"".  $validation."\""  :"").
            ($size!=""        ?" size=\"".        $size."\""        :"").
            ($accept!=""      ?" accept=\"".      $accept."\""      :"").
			($placeholder!="" ?" placeholder=\"". $placeholder."\"" :"").
			($min=""          ?" min=\"".         $min."\""         :"").
			($max=""          ?" min=\"".         $max."\""         :"").
			($autocomplete!=""?" autocomplete=\"".$autocomplete."\"":"").
            "/>\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML input field by calling inputField function, sepearating out the passed in by first param
   * @param array configuration options for the function   
   * @return string html of an input element
   */
  public static function inputFieldConfig($config)
  {
    // Set up input_field function call using elements from array
    if ((isset($config["type"])) && ($config["type"]!=""))
      $type = $config["type"];
    else $type = "text";

    if ((isset($config["field_name"])) && ($config["field_name"]!=""))
      $field_name = $config["field_name"];
    else $field_name = "input_field";

    if ((isset($config["value"])) && ($config["value"]!=""))
      $value = $config["value"];
    else $value = "";

    if ((isset($config["class"])) && ($config["class"]!=""))
      $class = $config["class"];
    else $class = "";

    if ((isset($config["maxlength"])) && ($config["maxlength"]!=""))
      $maxlength = $config["maxlength"];
    else $maxlength = "";

    if ((isset($config["style"])) && ($config["style"]!=""))
      $style = $config["style"];
    else $style = "";

    if ((isset($config["onclick"])) && ($config["onclick"]!=""))
      $onclick = $config["onclick"];
    else $onclick = "";

    if ((isset($config["config"])) && ($config["config"]!=""))
      $input_config = $config["config"];
    else $input_config = array();

    if ((isset($config["validation"])) && ($config["validation"]!=""))
      $input_config = array_merge($input_config, array("validation"=>$config["validation"]));

    return self::inputField($type, $field_name, $value, $class, $maxlength, $style, $onclick, $config);
  }
  //===================================================================================================================================
	
	
  //===================================================================================================================================
  /**
  * Return HTML textarea field by from passed in parameters
  * @param string textarea name / id
  * @param int textarea rows
  * @param int textarea columns
  * @param string textarea content
  * @param string textarea class
  * @param array configuration options for the function   
  * @return string html of a textarea element
  */
  public static function textarea($field_name, $rows, $columns, $value, $class="", $config="")
  {
    if ((isset($config["style"])) && ($config["style"]!=""))
      $style = $config["style"];
    else $style = "";

    if ((isset($config["onkeyup"])) && ($config["onkeyup"]!=""))
      $onkeyup = $config["onkeyup"];
    else $onkeyup= "";
	
	if ((isset($config["disabled"])) && ($config["disabled"]=="true"))
      $disabled = "disabled";
    else $disabled = "";

    return "<textarea ".
           "id=\"".$field_name."\" ".
         "name=\"".$field_name."\" ".
         "rows=\"".$rows."\" ".
         "cols=\"".$columns."\" ".
         ($class!=""?" class=\"".$class."\" ":"").
         ($style!=""?" style=\"".$style."\"":"").
         ($onkeyup!=""?" onkeyup=\"".$onkeyup."\"":"").
		 ($disabled!=""?" disabled=\"".$disabled."\"":"").
	     ">\n".
         $value.
         "</textarea>\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
    * Return javascript function to monitor the number of characters in a textarea, so that at a maximum no other characters can be entered
    * @return string javascript of character limit function
    */
  public static function limitText()
  {
    $output = "function limitText(limitField, limitCount, limitNum) \n".
		      "{\n".
	       // "\talert(limitField.val().length);\n".
		      "\tif (limitField.val().length > limitNum)\n".
		      "\t{\n".
		      "\t\tlimitField.val(limitField.val().substring(0, limitNum));\n".
		      "\t}\n".
		      "\telse\n".
		      "\t{\n".
		      "\t\tlimitCount.html(limitNum - limitField.val().length+' chars left');\n".
		      "\t}\n".
		      "}\n";
    return $output;				  
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return HTML for an image tag from passed in parameters
   * @param string image id/name
   * @param string source of the image
   * @param array configuration options for the function   
   * @return string html of a image element
   */
  public static function imageTag($id, $src, $config="")
  {
    if ((isset($config["alt"])) && ($config["alt"]!=""))
    {
      $alt   = $config["alt"];
      $title = $config["alt"];
    }
    else
    {
      $alt   = "";
      $title = "";	
    }

    if ((isset($config["class"])) && ($config["class"]!=""))
      $class = $config["class"];	
    else $class = "";

    if ((isset($config["style"])) && ($config["style"]!=""))
      $style = $config["style"];	
    else $style = "";

    if ((isset($config["height"])) && ($config["height"]!=""))
      $height = $config["height"];	
    else $height = "";

    if ((isset($config["width"])) && ($config["width"]!=""))
      $width = $config["width"];	
    else $width = "";

    return "<img id=\"".$id."\" ".
		       "src=\"".$src."\" ". 
		  ($alt  !=""?"alt=\""   .$$alt . "\" ":"").
		  ($title!=""?"title=\"" .$title. "\" ":"").
		  ($class!=""?"class=\"" .$class. "\" ":"").
		  ($style!=""?"style=\"" .$style. "\" ":"").
	     ($height!=""?"height=\"".$height."\" ":"").
		  ($width!=""?"width=\"" .$width. "\" ":"").
		  "/>\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery code to focus on an element in a form
   * @param string field name
   * @return string jquery function 
   */
  public static function focusField($field_name)
  {
    if (strpos($field_name, "+")===false)
      $field_name = "'#".$field_name."'";
    else $field_name = "'#'".$field_name;
			
    $focus_field = "var tmp_value = $(".$field_name.").val();\n".
				   "$(".$field_name.").val('');\n".
				   "$(".$field_name.").focus();\n".
				   "$(".$field_name.").val(tmp_value);\n";
    return $focus_field;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery code to focus on an element in a form on startup
   * @param string field name
   * @param array configuration options for the function   
   * @return string jquery function 
   */
  public static function onloadFocus($field_name, $config="")
  {
    if ((isset($config["show_message"])) && ($config["show_message"]==true))
	  $show_message = "alert('reached here');\n";
    else $show_message = "";
    return self::jqueryFunction("document", "ready", $show_message.self::focusField($field_name));		
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery / javascript code to check the value of a form element, for example when a form is submitted
   * @param string field name
   * @param string error message to be shown
   * @param string check to be performed
   * @param array configuration options for the function   
   * @return string jquery / javascript code for a function check
   */
  public static function formErrorLine($field, $message, $check="blank", $config="")
  {
    if ((isset($config["javascript"])) && ($config["javascript"]=="true"))
    {
      if ($check=="blank")
        $output = "if (document.getElementById('".$field."').value==\"\")\n";
      else if ($check=="select_chosen")
      {
        $output .= "var element_".$field." = document.getElementById('".$field."');\n".
  		           "if (element_".$field.".options[element_".$field.".selectedIndex].value==\"\")\n";
      }
    }
    else 
    {
      if (($check=="blank") || ($check=="select_chosen"))
      $operation = "val()==\"\")";
      $output = "if ($('#".$field."').".$operation."\n";
    }
  
    $output .= "{\n".
               "\terrors += \"".$message."\\n\";\n".
               "\tif (returnField==\"\") returnField = \"".$field."\";\n".
               "}\n";
	// "alert($('#".$field."').val());\n"
   
    return $output;			  
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
   * Return jquery / javascript code to execute checks on form values when a form is submitted
   * @param string form id/name
   * @param string action of the form (i.e. submit or click)
   * @param string checks to be performed
   * @param string what to do when no errors are found
   * @param boolean determine whether to product jquery or javascrpt code
   * @return string jquery / javascript of form checks 
   */ 
  public static function formErrors($form_id, $action, $checks, $no_error_action="", $jquery=true)
  {
    if ($action=="") $action = "submit";
    if ($no_error_action=="") $no_error_action = "else return true;\n";

    $message = "var errors = '';\n".
               "var returnField = '';\n".
               $checks.
               "if (errors!=\"\")\n".
               "{\n".
               "alert(\"You have the following errors:\\n\\n\"+errors);\n".
               "document.getElementById(returnField).focus();\n".				
               "return false;\n".
               "}\n";

    if ($jquery==true)
    {
      $js_func = $message.
	             $no_error_action;		
      return self::jqueryFunction($form_id, $action, $js_func, "#");	     
    }
    else 
    {
      return "function ".$form_id."()\n".
             "{\n".
           //"alert('reached here');\n".
             $message.
             $no_error_action.
             "};\n";		  
    }

  }
  //===================================================================================================================================
	
	
  //===================================================================================================================================
  /**
   * Return location information gathered from remote address using up2location class
   * @param string remote ipaddress to gather information on
   * @return array location information 
   */ 
  public static function getLocationInformation($ipAddress="")
  {
    if ($ipAdress=="") $ipAddress = $_SERVER["REMOTE_ADDR"];

    $central_location = "_includes/_central/";
    require_once($central_location."ip2location.class.php");
    require_once($central_location."ip2locationlite.class.php");

    $ipinfodb_api_key          = "e2908214f759ab43ea7d27ce7aa9a2111117dfa11111b1bab54390fcf69e6999";
    $ipinfodb_website_username = "bfigab";
    $ipinfodb_website_password = "bfigbluefish";

    //Load the class
    $ipLite = new ip2location_lite;
    $ipLite->setKey($ipinfodb_api_key);	

    $locations = $ipLite->getCity($ipAddress);
    $errors = $ipLite->getError();

    $ipAddress   = $locations["ipAddress"];
    $cityName    = $locations["cityName"];
    $regionName  = ucwords(strtolower(trim($locations["regionName"])));
    $countryName = ucwords(strtolower(trim($locations["countryName"])));

    if (in_array(trim($regionName),  array("", "-"))) $regionName = "Unknown";
    if (in_array(trim($countryName), array("", "-"))) $regionName = "Unknown";

    if (in_array(trim($ipAddress), array("", "-"))) 
      $ipAddress = $data[$head."remote_addr"];
    if (in_array(trim($cityName), array("", "-"))) 
    {
      if (is_array($errors))
      {
        $cityName = "";
        foreach ($errors as $error) 
        {
          $cityName  .= var_dump($error) . "<br />\n";
        }
      }
      else $cityName = "Unknown";
    }
    else $cityName = ucwords(strtolower($cityName));

    return array("cityName"   =>$cityName,
                 "regionName" =>$regionName,
                 "countryName"=>$countryName);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
  * Escape fields so that they are correctly formatted when being used with MySQL
  * @param string value to be escaped
  * @return string escaped value 
  */
  public static function makeSafe($variable)   
  {
    // $variable = strip_tags($variable); I have no idea whether this is the correct one
    $variable = htmlentities($variable);
    $variable = CentralMySQL::escapeString(trim($variable));
    return $variable; 
  }
  //===================================================================================================================================
  
  //===================================================================================================================================
  /**
   * Record information of the browser requesting a page, for use of activity data etc 
   * @param string remote ipaddress to gather information on 
   * @param string database table name to record data into
   * @param string database table field name starter
   * @param string session id of the user
   * @param string alternative connection if not using default $mysql connection
   * @param string promotion id of the current campagin
   * @return array location information 
   */ 	
  public static function recordVisitorData($http_host, $table="data", $head="data_", $session_id="", $alt_connection="", $promo_id="",$config="")
  {
    global $mysql;
		
	$fields = array($head."date"            =>self::makeSafe(date("Y-m-d H:i:s", time())), 
					$head."http_host"       =>self::makeSafe($http_host!=""?$http_host:$_SERVER['HTTP_HOST']),
					$head."http_user_agent" =>self::makeSafe($_SERVER["HTTP_USER_AGENT"]),
					$head."remote_addr"     =>self::makeSafe($_SERVER["REMOTE_ADDR"]),
					$head."remote_addr"     =>self::makeSafe($_SERVER["REMOTE_ADDR"]),
					$head."remote_port"     =>self::makeSafe($_SERVER["REMOTE_PORT"]));
					
	if ((!isset($config["inc_location"])) || ($config["inc_location"]==true))
	{
		$locationData = self::getLocationInformation();				
		$fields[$head."remote_city_name"] = self::makeSafe($locationData["cityName"]);
		$fields[$head."remote_region"]    = self::makeSafe($locationData["regionName"]);
		$fields[$head."remote_country"]   = self::makeSafe($locationData["countryName"]);	
	}
					   
    if ($session_id!="")
	  $fields[$head."session_id"] = self::makeSafe($session_id);	
			
    if ($promo_id!="")
      $fields[$head."promo_id"] = self::makeSafe($promo_id);		

    $pr_id = "persistant_reference";
	if ((isset($config[$pr_id])) && ($config[$pr_id]!=""))
		$fields[$head.$pr_id] = self::makeSafe($config[$pr_id]);
		
	$insert_sql = CentralMySQL::insertQuery($table, $fields);
	
	if (is_object($alt_connection))
      $alt_connection->query($insert_sql);
    else $mysql->query($insert_sql);
    return true;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return array of month information including the number of days 
   * @param string show 3 letter abbreviation of full month name
   * @param boolean determine where to have an id=>desc array or the full month details
   * @return array month information 
   */ 		
  public static function getMonthsArray($style="short", $basic=false)
  {
    $months = array(array("id"=>"01", "days"=>"31", "desc"=>($style=="short"?"Jan":"January")),
		      array("id"=>"02", "days"=>"29", "desc"=>($style=="short"?"Feb":"February")),
		      array("id"=>"03", "days"=>"31", "desc"=>($style=="short"?"Mar":"March")),
		      array("id"=>"04", "days"=>"30", "desc"=>($style=="short"?"Apr":"April")),
		      array("id"=>"05", "days"=>"31", "desc"=>($style=="short"?"May":"May")),
		      array("id"=>"06", "days"=>"30", "desc"=>($style=="short"?"Jun":"June")),
		      array("id"=>"07", "days"=>"31", "desc"=>($style=="short"?"Jul":"July")),
		      array("id"=>"08", "days"=>"31", "desc"=>($style=="short"?"Aug":"August")),
		      array("id"=>"09", "days"=>"30", "desc"=>($style=="short"?"Sep":"September")),
		      array("id"=>"10", "days"=>"31", "desc"=>($style=="short"?"Oct":"October")),
		      array("id"=>"11", "days"=>"30", "desc"=>($style=="short"?"Nov":"November")),
		      array("id"=>"12", "days"=>"31", "desc"=>($style=="short"?"Dec":"December")));
    if ($basic==true)
    {
      $return_months = array();
      foreach($months as $month)
      {
        $return_months[$month["id"]] = $month["desc"];				
      }
    }
    else $return_months = $months;
    return $return_months;
  }
  //===================================================================================================================================

  //===================================================================================================================================
  /**
   * Return the age of a person from their date of birth until the date of their death if applicable
   * @param string date of birth of the person
   * @param string date of death of the person (if applicable)
   * @param boolean determine to show extended text (i.e months and years)
   * @return age of person 
   */
  public static function getAge($DOB, $DOD="", $ext=true) 
  {
    // Get current date
    $CD = date("Y-n-d");
    list($cY,$cm,$cd) = explode("-",$CD);
	
    // Get date of birth
    list($bY,$bm,$bd) = explode("-",$DOB);
    // is there a date of death?

    if ($DOD!="" && $DOD != "0000-00-00") 
	{
      // Animal is dead
      list($dY,$dm,$dd) = explode("-",$DOD);
      if ($bY == $dY) 
	  {
        $months = $dm - $bm;
        if ($months == 0 || $months > 1) 
		{
          return $months.($ext==true?" months":"");
        }
        else return $months.($ext==true?" month":"");
      } 
	  else $years = ( $dm.$dd < $bm.$bd ? $dY-$bY-1 : $dY-$bY );

      if ($years == 0 || $years > 1) 
	  {
        return $years.($ext==true?" years":"");
      } 
	  else 
	  { 
        return $years.($ext==true?" year":"");
      }
    } 
    else 
    {
      // Animal is alive  
      if ($bY != "" && $bY != "0000") 
	  {	
        if ($bY == $cY) 
	    {
          // Birth year is current year
          $months = $cm - $bm;
          if ($months == 0 || $months > 1) 
		  {
            return $months.($ext==true?" months":"");
          } 
		  else return $months.($ext==true?" month":"");
        } 
	    else if ($cY - $bY == 1 && $cm - $bm < 12) 
	    {
          // Born within 12 months, either side of 01 Jan
          // Determine days and therefore proportion of month
          if ($cd - $bd > 0) 
		  {
            $xm = 0;
          } 
		  else 
		  { 
            $xm = 1;
          }
          $months = 12 - $bm + $cm - $xm;
          if ($months == 0 || $months > 1) 
		  {
            return $months.($ext==true?" months":"");
          } 
		  else 
		  { 
            return $months.($ext==true?" month":"");
          }
        } 

        // Animal older than 12 months, return in years
        $years = (date("md") < $bm.$bd ? date("Y")-$bY-1 : date("Y")-$bY );
        if ($years == 0 || $years > 1) 
	    {
          return $years.($ext==true?" years":"");
        } 
	    else 
	    { 
          return $years.($ext==true?" year":"");
        }
      } 
      else return "No Date of Birth!";
    }	
  }
  //===================================================================================================================================
	
	
  //===================================================================================================================================
  /**
   * Return a string of words so that everything is lowercase except the first left of each word which will be capitalized.
   * @param string value to be converted
   * @return coverted string value
   */
  public static function dispWords($string)
  {
    $string = trim($string);
    $string = strtolower($string);
    $string = ucwords($string);

    if ($string=="")
      return "N/A";
    else return $string;	
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
   * Return jquery code that will majcause text of a spefic div to change color when hovered over
   * @param string the original color of the text
   * @param string the original background color of the div
   * @return coverted string value
   */	
  public static function jsRowHover($orig_color="white", $orig_background_color="transparent")
  {
    if ($orig_color=="")            $orig_color            = "white";
    if ($orig_background_color=="") $orig_background_color = "transparent";
		
    $js = "var hovered = false;\n".
		  "var your_application = function()\n".
		  "{\n".
		  "\tvar txtcol = '',\n".
		  "\t    bgcol  = '';\n". 
		  "return {\n".
		  "\tgetcol: function(){\n".
		  "\treturn txtcol;\n".
		  "},\n".
		  "\tsetcol: function(newcol){\n".
          "\ttxtcol = newcol;\n".
		  "},".
		  "\tgetbg: function(){\n".
		  "\treturn bgcol;\n".
		  "},\n".
		  "\tsetbg: function(newbg){\n".
          "\tbgcol = newbg;\n".
		  "}\n".
		  "};\n".
		  "};\n";
		
    $js .= "var app = your_application();\n";
		
    $js .= self::jqueryFunction("hover", 
								"hover",
								"if (hovered==false)\n".
								"{\n".
								"\tvar row_id = $(this).attr(\"id\");\n".
								"\tapp.setcol($('#'+row_id).css('color'));\n".
								"\tapp.setbg($('#'+row_id).css('background-color'));\n".
							// "alert(txtcol);\n".
								"\t$(\"#\"+row_id).css(\"color\", \"grey\");\n".
								"\t$(\"#\"+row_id).css(\"background-color\", \"lightgrey\");\n".
								"\thovered = true;\n".
								"}\n",
									".");
    $js .= self::jqueryFunction("hover",
	 				            "mouseout",
							    "if (hovered==true)\n".
							    "{\n".
							    "\tvar row_id = $(this).attr(\"id\");\n".
							    "\t$(\"#\"+row_id).css(\"color\", app.getcol());\n".
							    "\t$(\"#\"+row_id).css(\"background-color\", app.getbg());\n".
							    "\thovered = false;\n".
							    "}\n",
							    ".");
    return $js;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery code to use the datePicker function
   * @param first date to be shown in calendar
   * @param string the original background color of the div
   * @return coverted string value
   */	
  public static function datepickerJs($start_date="01/01/2011")
  {
    return "$(function()\n".
           "{\n".
	       "\t$('.date-pick').datePicker({startDate:'".$start_date."'});\n".
		   "});\n";
  }
  //===================================================================================================================================

  //===================================================================================================================================
  /**
   * Return jquery code for an event function for such as click
   * @param string element id
   * @param string event to be triggered 
   * @param string code to be done within the function
   * @param string element reference type (i.e. # for id or . for class)
   * @param array configuration options for the function
   * @return coverted string value
   */
  public static function jqueryFunction($element_name, $event="click", $action="alert('Hello');\n", $type="#", $config="")
  {
    if ($element_name!="document")
    {
      if ($type=="input")
        $element = "'input[name=\"".$element_name."\"]'";
      else $element = "'".$type.$element_name."'";
    }
    else $element = $element_name;
	
    if ((isset($config["function_attributes"])) && ($config["function_attributes"]!=""))
      $function_attributes = $config["function_attributes"];
	else $function_attributes = "";
	return "$(".$element.").".$event."(function(".$function_attributes.")\n".
	       "{\n".
	        $action.
	       "});\n";	
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery code for a delete button trigger 
   * @param string parameter id of what is to be deleted (e.g. row_id)
   * @param string parameter value of what is to be delete (e.g. row 1)
   * @param string the id of the button to be clicked to trigger the delete operation
   * @param string the user to send the delete request to
   * @param string the message asking the user to confirm the delete
   * @param array configuration options for the function
   * @return jquery code
   */
  public static function jqueryDeleteTrigger($param_id, $param_value, $button_id, $url="", $message="", $config="")
  {
    if ($url=="")
      $url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

    if ($message=="")
      $message = "'Are you sure you wish to delete this record?'";

	if ((isset($config["type"])) && ($config["type"]!=""))
		$type = $config["type"];
	else $type = "#";

    if ((isset($config["continue_js"])) && ($config["continue_js"]!=""))	
      $continue_js = $config["continue_js"];
    else 
	{
		if ($type==".")
		{
			$continue_js = "var parts = $(this).attr('id').split('_');\n".
						   "window.location = 'http://".$url."?delete=true'+'&'+'".$param_id."='+parts[1];\n";	
		}
		else $continue_js = "window.location = 'http://".$url."?delete=true'+'&'+'".$param_id."=".$param_value."';\n";
	}

	$js_func = "var confirmDelete = confirm(".$message.");\n".
               "if (confirmDelete==true)\n".
               "{\n".
               $continue_js.
               "return false;\n".
               "}\n".
               "else\n". 
               "{\n".
               "alert(\"Delete request cancelled.\");\n".
               "return true;\n".
               "}\n";
    return self::jqueryFunction($button_id, "click", $js_func, $type);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return jquery code for a cancel button trigger 
   * @param string the id of the button to be clicked to trigger the cancel operation
   * @param string the user to send the cancel request to
   * @param string the message asking the user to confirm the cancel
   * @param array configuration options for the function
   * @return jquery code
   */
  public static function jqueryCancelTrigger($button_id, $cancel_url, $cancel_message="", $config="")
  {
    if ($cancel_message=="")
      $cancel_message = "Are you sure you wish to cancel and return to previous page? (No changes will be saved)";
			
    if ((isset($config["continue_js"])) && ($config["continue_js"]!=""))	
      $continue_js = $config["continue_js"];
    else $continue_js = "window.location = '".$cancel_url."';\n";	
			
    $js_func = "var confirmCancel = confirm(\"".$cancel_message."\");\n".
			   "if (confirmCancel==true)\n".
			   "{\n".
			   $continue_js.
			   "return false;\n".
			   "}\n".
			   "else\n". 
			   "{\n".
			   "alert(\"Cancel request aborted.\");\n".
			   "return true;\n".
			   "}\n".
			   "return true;\n";
				   
    return self::jqueryFunction($button_id, "click", $js_func, "#");
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
   * Return jquery code for a tooltip hover event handler
   * @param string the class of the element to hovered over
   * @param string the width of the tooltip to be shown
   * @param string the height of the tooltip to be shown
   * @param string the text of the tooltip to be shown
   * @param array configuration options for the function
   * @return jquery code
   */
  public static function tooltipHover($class_name, $width, $height, $content, $config="")
  {
    $js = "var ".$class_name."_in_area = false;\n".
          "$('.".$class_name."').mouseenter(function(event)\n".
          "{\n".
          "\t".$class_name."_create_tooltip(event, $(this).attr('id'));\n".
          "});".
          "$('.".$class_name."').mouseout(function(){\n".
          "\t".$class_name."_hide_tooltip(); \n".
          "});\n".
          "$('.".$class_name."').mousemove(function(event){\n".
          "if (".$class_name."_in_area==true)\n". 
          "\t".$class_name."_position_tooltip(event);\n".
          "});\n";

    $js .= "function ".$class_name."_create_tooltip(event, element_id)\n".
           "{\n".  
           "var parts = element_id.split('_');\n";
          //  "alert(element_id);\n".

    if ((isset($config["extra_processing"])) && ($config["extra_processing"]!=""))
      $js .= $config["extra_processing"];

    if ((isset($config["extra_style"])) && ($config["extra_style"]!=""))
      $extra_style = $config["extra_style"];	
    else $extra_style = "";

    if ((isset($config["extra_class"])) && ($config["extra_class"]!=""))
      $extra_class = " class=\"".$config["extra_class"]."\"";	
    else $extra_class = "";
	   
    $js .= "var content = '".$content."';\n".
           "if ($('#".$class_name."_tooltip').length)\n".
           "\t$('#".$class_name."_tooltip').html(content);\n".
           "else $('<div id=\"".$class_name."_tooltip\" ".
		   "style=\"width:".($width+5)."px;height:".($height+5)."px;".$extra_style."\"".$extra_class.">".
		   "'+content+".
		   "'</div>').appendTo('body');\n".
          $class_name."_position_tooltip(event);\n".
          $class_name."_in_area=true;\n".
          "};\n";

    $js .= "function ".$class_name."_hide_tooltip(event)\n".
           "{\n".          
           // "alert('reached here');\n".
           "$('#".$class_name."_tooltip').css('display', 'none');\n".
           $class_name."_in_area = false;\n".
           "};\n";

    if ((isset($config["y-position"])) && ($config["y-position"]!=""))
      $y_position = $config["y-position"];
    else $y_position = $height;

    $js .= "function ".$class_name."_position_tooltip(event)\n".
           "{\n".
           "\tvar tPosX = event.pageX + 10;\n".
           "\tvar tPosY = event.pageY - ".$y_position.";\n".
           "$('#".$class_name."_tooltip').css({'position': 'absolute', 'top': tPosY, 'left': tPosX});\n".
           "$('#".$class_name."_tooltip').css('display', 'block');\n".
           "};\n";

    return $js;	
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return random string of determinable length
   * @param int the length of the string that is required
   * @return random text string
   */
  public static function randString($length) 
  {
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	
    $size = strlen( $chars );
    $str = "";
    for( $i = 0; $i < $length; $i++ ) 
    {
      $str .= $chars[ rand( 0, $size - 1 ) ];
    }
    return $str;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * return an empty div contain '&nbsp;' of specific site with clear:both CSS
   * @param string height of div
   * @return HTML div element
   */	
  public static function clearRow($height="20px")
  {
    return self::clearLine($height);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * return an empty div contain '&nbsp;' of specific site with clear:both CSS
   * @param string height of div
   * @return HTML div element
   */		
  public static function clearLine($height="20px")
  {
    return "<div class=\"clear\" style=\"clear:both;height:".$height.";\">&nbsp;</div>\n";
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * replace & characters with &amp; for correct browser display 
   * @param string string to be converted
   * @return HTML div element
   */
  public static function ampReplace($string)
  {
    $tmp_string = str_replace("amp;", "",     $string);
    $tmp_string = str_replace("&",   "&amp;", $tmp_string);
    return $tmp_string;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Record a line of text into a text/ document, with a proceeding date
   * @param string text to be entered into the log file
   * @param string name of the log file to write the line too
   * @param mode mode of how to open the file e.g. w+ or a+
   * @param boolean determine whether to include date with entry line
   * @return N/A
   */	
  public static function logEntry($text, $log_file="query_log.txt", $mode="a+", $addDate=true)
  {

    $handle = fopen($log_file, $mode);
    fwrite($handle, ($addDate=="true"?date("Y-m-d H:i:s")." - ":"").trim($text)."\n"); 	
    fclose($handle);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return javascript <script> opening and/or closing comment tags which stops browsers mistaking javascript code from being or contain HTML
   * @param string type of comment tags required (i.e. both, top, bottom)
   * @param string jquery code to put in between the comment tags, only relevant if type is both
   * @return javascript code
   */	
  public static function javascriptEncapsulators($type="both", $js="")
  {
    $output = "";

    if (in_array($type, array("both", "top")))
      $output .= "/*<![CDATA[*/\n";

    if ($js!="")
      $output .= $js;

    if (in_array($type, array("both", "bottom")))
      $output .= "/*]]>*/\n";

    return $output;
  }
  //===================================================================================================================================
	
  
   //===================================================================================================================================
  /**
   * Return HTML select tags for a day, month and year to select a date in a form
   * @param array configuration_options
   * @return array day, month and year select statements
   */	
  public static function dateDropdown($config=array())
  {
  	$options = array();
	for($i=1; $i<=31; $i++) {$options[] = array("id"=>(strlen($i)<2?"0".$i:$i), "desc"=>$i); }
    $day_config   = array("class"=>$config["day_class"], "no_select"=>"true", "placeholder"=>"DD");
	$day          = self::selectField($config["day_value"], $config["day_field"], $options, "DD", "id", "desc", $day_config);
	
	$options      = self::getMonthsArray();
	$month_config = array("class"=>$config["month_class"], "no_select"=>"true", "placeholder"=>"MM");
	$month        = self::selectField($config["month_value"], $config["month_field"], $options, "MM", "id", "desc", $month_config);
	
	$options = array();
	for($i=(int) date("Y", time()); $i>=((int) date("Y", time()))-100; $i--) { $options[] = array("year"=>$i);}
	$year_config  = array("class"=>$config["year_class"], "no_select"=>"true", "placeholder"=>"YYYY");
	$year         = self::selectField($config["year_value"], $config["year_field"], $options, "YYYY", "year", "year", $year_config);
	
	return array("day"=>$day, "month"=>$month, "year"=>$year);
	
	/*
	// Example on how to use this function:
	$date_fields = array("dob_day", "dob_month", "dob_year");
	$config = array("day_field"  =>$date_fields[0],
	                "day_value"  =>(isset($_REQUEST[$date_fields[0]])?$_REQUEST[$date_fields[0]]:""),
					"day_class"  =>"date_field_day",
					"month_field"=>$date_fields[1],
					"month_value"=>(isset($_REQUEST[$date_fields[1]])?$_REQUEST[$date_fields[1]]:""), 
					"month_class"=>"date_field_month",
					"year_field" =>$date_fields[2],
					"year_value" =>(isset($_REQUEST[$date_fields[2]])?$_REQUEST[$date_fields[2]]:""), 
					"year_class" =>"date_field_year");
	$tmpArray = CentralGeneral::dateDropdown($config);
	$field_value = $tmpArray["day"].$tmpArray["month"].$tmpArray["year"];
	*/
	
  }
  //===================================================================================================================================
  
  
  //===================================================================================================================================
  /**
   * Return HTML select tags for a hours and minutes to select a time in a form
   * @param string the id / name of the hour select tag
   * @param string the value of the hour select tag
   * @param string the id / name of the mins select tag
   * @param string the value of the min select tag
   * @return array hour and min select statements
   */	
  public static function timeDropdown($hour_field="hours", $hour_value="", $min_field="mins", $min_value="")
  {
    $options = array();
    for($i=0; $i<24; $i++)
      $options[] = array("id"=>$i, "desc"=>($i<10?"0".$i:$i));
    $hours = self::stdSelect($hour_value, 
		        			 $hour_field, 
				        	 $options, 
					         "HH", 
					         "id", 
					         "desc",
					        array("class"    =>"input",
						          "style"    =>"height:22px;width:45px;",
						    "no_select"=>"true"));
							
    $options = array();
    for($i=0; $i<59; $i++)
      $options[] = array("id"=>$i, "desc"=>($i<10?"0".$i:$i));
    $mins = self::stdSelect($min_value, 
	        				$min_field, 
			          		$options, 
					        "MM", 
					        "id", 
					        "desc",
					        array("class"    =>"input",
						          "style"    =>"height:22px;width:45px;",
						          "no_select"=>"true"));		
    return array("hours"=>$hours, "mins"=>$mins);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return a string contains days, hours and minutes from a starting timestamp to an ending timestamp
   * @param int the timestamp to start from, if checking the remaining time from now, you would pass in time()
   * @param int the timestamp to determine the remaining time
   * @param boolean determine whether to return the number of seconds 
   * @return string description of remaining time
   */
  public static function getRemainingTime($now,$future, $include_seconds)
  {
    if($future <= $now)
    {
      // Time has already elapsed
      return false;
    }
    else
    {
      // Get difference between times
      $time = $future - $now;
      $minutesFloat = $time/60;
      $minutes = floor($minutesFloat);
      $hoursFloat = $minutes/60;
      $hours = floor($hoursFloat);
      $daysFloat = $hours/24;
      $days = floor($daysFloat);
      $parts = array('days'   =>$days, 
	                 'hours'  =>round(($daysFloat-$days)*24),
			         'minutes'=>round(($hoursFloat-$hours)*60),
			         'seconds'=>round(($minutesFloat-$minutes)*60));
	  $remaining_time = $parts["days"]." days, ".$parts["hours"]." hours, ".$parts["minutes"]." minutes";
	  if ($include_seconds==true)
	  	$remaining_time .= ", ".$parts["seconds"]." seconds";
    }
  }
  //===================================================================================================================================
		
  //===================================================================================================================================
  /**
   * Replace incorrect characters with ther actually textual representation
   * @param int the timestamp to start from, if checking the remaining time from now, you would pass in time()
   * @param int the timestamp to determine the remaining time
   * @param boolean determine whether to return the number of seconds 
   * @return string description of remaining time
   */
  public static function processText($text, $strip_slashes=true, $replace_and=false)
  {
    $text = str_replace(chr(145), "'",  $text);
    $text = str_replace(chr(146), "'",  $text);
    $text = str_replace(chr(147), "\"", $text);
    $text = str_replace(chr(148), "\"", $text);
    $text = str_replace(chr(149), "•",  $text);
    $text = str_replace(chr(150), "–",  $text);
    $text = str_replace(chr(153), "™",  $text);
    $text = str_replace(chr(194), "",   $text);  // "Â" char, but appaers in strange places especially infront of £ signs
    $text = str_replace(chr(163), "£",  $text);
    $text = str_replace(chr(174), "®",  $text);
    $text = str_replace(chr(249), "•",  $text);
    /*
     * Added by keni
     */
    $text = str_replace('è', "e",  $text);
    $text = str_replace('é', "e",  $text);
    $text = str_replace('®', "",  $text);
    $text = str_replace('™', "",  $text);


    if ($replace_and==true)
	  $text = self::ampReplace($text);

	if ($strip_slashes==true)
	  return stripslashes($text);
    else return $text;
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return pagination for database data depending upon input params
   * @param array configuration options for the function
   * @return string HTML pagination data 
   */
  public static function getPagination($config)
  {
    // How many adjacent pages should be shown on each side?
    if ((isset($config["ajacents"])) && ($config["ajacents"]!=""))	
      $adjacents = (int) $config["ajacents"];
    else $adjacents = 3;

    // The current page that data is being viewed on
    if ((isset($config["page"])) && ($config["page"]!=""))	
      $page = (int) $config["page"];
    else $page = 1;

    if ((isset($config["pagelink"])) && ($config["pagelink"]!=""))	
      $pagelink = $config["pagelink"];
    else $pagelink = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

    if ((isset($config["total_records"])) && ($config["total_records"]!=""))	
      $total_records = $config["total_records"];
    else $total_records = 10;

    //how many items to show per page
    if ((isset($config["limit"])) && ($config["limit"]!=""))	
      $limit = $config["limit"];
    else $limit = 10;

    if ((isset($config["form_link"])) && ($config["form_link"]!=""))
      $pagelink = "javascript:void(0)";


    /* 
    First get total number of rows in data table. 
    If you have a WHERE clause in your query, make sure you mirror it here.
    */
    /* Setup vars for query. */
    if ($page > 0) 
      $start = ($page - 1) * $limit; 	//first item to display on this page
    else $start = 0;					//if no page var is given, set start to 0

    /* Get data. */

    /* Setup page vars for display. */
    if ($page == 0) $page = 1;					//if no page var is given, default to 1.
    $prev = $page - 1;							//previous page is page - 1
    $next = $page + 1;							//next page is page + 1
    $lastpage = ceil($total_records/$limit);	//lastpage is = total pages / items per page, rounded up.
    $lpm1 = $lastpage - 1;						//last page minus 1

    /* 
    Now we apply our rules and draw the pagination object. 
    We're actually saving the code to a variable in case we want to draw it more than once.
    */
    $pagination = "";
    if ($lastpage > 1)
    {	
      $pagination .= "<div class=\"pagination\">";
      // previous button
      if ($page > 1)
	    $pagination.= self::getPaginationLink($prev, "« previous", $pagelink, $config);
      else
	    $pagination.= "<span class=\"disabled\">« previous</span>";	

      //pages	
      if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
      {    	
        for ($counter = 1; $counter <= $lastpage; $counter++)
        {
          if ($counter == $page)
            $pagination.= "<span class=\"current\">$counter</span>";
          else
            $pagination.= self::getPaginationLink($counter, $counter, $pagelink, $config);
        }
      }
      elseif ($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
      {
        //close to beginning; only hide later pages
        if ($page < 1 + ($adjacents * 2))		
        {
          for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
          {
            if ($counter == $page)
              $pagination.= "<span class=\"current\">".$counter."</span>";
            else $pagination.= self::getPaginationLink($counter, $counter, $pagelink, $config);
          }
          $pagination.= "...";
          $pagination.= self::getPaginationLink($lpm1,     $lpm1,     $pagelink, $config).
	                    self::getPaginationLink($lastpage, $lastpage, $pagelink, $config);	
        }
        //in middle; hide some front and some back
        elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
        {
          $pagination.= self::getPaginationLink("1", "1", $pagelink, $config).
	                    self::getPaginationLink("2", "2", $pagelink, $config);
          $pagination.= "...";
          for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
          {
            if ($counter == $page)
              $pagination.= "<span class=\"current\">".$counter."</span>";
            else
              $pagination.= self::getPaginationLink($counter, $counter, $pagelink, $config);
          }
          $pagination.= "...";
          $pagination.= self::getPaginationLink($lpm1,     $lpm1,     $pagelink, $config).
                        self::getPaginationLink($lastpage, $lastpage, $pagelink, $config);
        }
        //close to end; only hide early pages
        else
        {
          $pagination.= self::getPaginationLink("1", "1", $pagelink, $config).
	                    self::getPaginationLink("2", "2", $pagelink, $config);
          $pagination.= "...";
          for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
          {
            if ($counter == $page)
              $pagination.= "<span class=\"current\">$counter</span>";
            else
              $pagination.= self::getPaginationLink($counter, $counter, $pagelink, $config);
          }
        }
      }

      //next button
      if ($page < $counter - 1)
        $pagination.= self::getPaginationLink($next, "next »", $pagelink, $config);
      else $pagination.= "<span class=\"disabled\">next »</span>";
        $pagination.= "</div>\n";		
    }

    if ((isset($config["form_link"])) && ($config["form_link"]!=""))
    {
      $js .= "function page_click(page_num)\n".
	         "{\n".
	         "\t$('input[name=\"page\"]').val(page_num);\n".
	         "\t$('form[name=\"".$config["form_link"]."\"]').submit();\n".
	         "}\n";
    }
    else $js = "";

    return array("pagination"=>$pagination, "start"=>$start, "limit"=>$limit, "js"=>$js);
  }
  //===================================================================================================================================
	
  //===================================================================================================================================
  /**
   * Return an hyperlink containing to move to a different page using a pagination method (see getPagination above)
   * @param string id of the page to be moved to
   * @param string the display value of the hyperlink
   * @param string the url for the href of the hyperlink
   * @param array configuration options for the function
   * @return string HTML pagination hyperlink 
   */	
  public static function getPaginationLink($value, $disp_value, $pagelink, $config)
  {
    if ((isset($config["form_link"])) && ($config["form_link"]!=""))
    {
      $onclick    = " onclick=\"javascript:page_click('".$value."');\"";
      $link_value = $pagelink;	
    }
    else 
    {
      $link_value = $pagelink."?page=".$value;
      $onclick    = "";
    }
	return "<a href=\"".$link_value."\"".$onclick.">".$disp_value."</a>\n";
  }
  //*********************************************************************************************************************************

  //*********************************************************************************************************************************
  /**
   * Send enhanced emails including attachments
   * @param string recipient of the email
   * @param string subject of the email
   * @param string message of the email
   * @param array  array of attachments each of which include the name of the file, path to it, and the the file will have in the attachment
   * @param boolean include the html footer
   * @param string sender name
   * @param string sender email
   * @param boolean determine whether to convert carriage returns in the message of the email, into <br/> tags.
   * @return boolean success of sending the email
   */	
  public static function email($email, $subject, $message, $pdf_attachments="", $inc_footer=true, $sender_name="", $sender_email="", $convert_lines=true) 
  {
    if ($sender_name=="")  $sender_name  = "Dev Clever Info";
    if ($sender_email=="") $sender_email = "info@devclever.co.uk";

    if ($convert_lines==true) $message = str_replace("\n", "<br>", $message);  
    $message = $message;
    if ($inc_footer==true)
      $message .= "<br><br><small>----------<br>".$sender_name."</small>";
    
    $headers  = "MIME-Version: 1.0\n";

    if ($sender_name=="none")
      $headers .= "From: ".$sender_email."\n";
    else $headers .= "From: ".$sender_name." <".$sender_email.">\n";

    $headers .= "X-Mailer: PHP's mail() Function\n";
    $headers .= "Reply-To: ".$sender_email."\n";
    $headers .= "Return-Path: ".$sender_email."\n";

    if (is_array($pdf_attachments))
    {
      if (!isset($pdf_attachments[0]))
      $pdf_attachments = array("0"=>$pdf_attachments);

      $semi_rand = md5(time()); 
      $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

      // headers for attachment 
      $headers .=  "Content-Type: multipart/mixed;\n" . 
		          " boundary=\"{$mime_boundary}\""; 

      // multipart boundary 
      $message = "This is a multi-part message in MIME format.\n\n" . 
	             "--{$mime_boundary}\n" . 
	             "Content-Type: text/html; charset=\"iso-8859-1\"\n" . 
	             "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
      $message .= "--{$mime_boundary}\n";

      // preparing attachments
      // echo "<pre>".print_r($pdf_attachments)."</pre>";

      for($x=0;$x<count($pdf_attachments);$x++)
      {
        $file = fopen($pdf_attachments[$x]["fileatt"],"rb");
        $data = fread($file,filesize($pdf_attachments[$x]["fileatt"]));
        fclose($file);
	
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: {\"application/octet-stream\"};\n" . 
		        	" name=\"".$pdf_attachments[$x]["fileattname"]."\"\n" . 
          			"Content-Disposition: attachment;\n";
        if (isset($pdf_attachments[$x]["fileattfilename"]))
	      $message .= " filename=\"".$pdf_attachments[$x]["fileattfilename"]."\"\n";
					
        $message .= "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        if ($x!=count($pdf_attachments)-1)
	      $message .= "--{$mime_boundary}\n";
      }
    }
    else $headers .= "Content-type: text/html; charset=iso-8859-1\n";

    if (!ini_get('safe_mode'))
    {
	  $result = mail($email, $subject, $message, $headers, '-f '.$sender_email);
    }
    else
    {
      $result = mail($email, $subject, $message, $headers);      
    }

    if ($result==false) 
    {
      // echo $message."<br/>";
      echo "<span style=\"font-weight:bold;color:red;\">Email to ".$email." was not sent!<br/>";
      return false;
    }
    else return true;
  }
  //*********************************************************************************************************************************
  
 	/*********************************************************************************************************************************
	/**
	* Return jquery code to call the ajax file uploader
 	* @param string name of the file field
	* @param string url of the ajax handler   
	* @return jquery code for file uploader
	*/
  	public static function fileUploadJquery($field_name, $upload_url, $host="", $swf_dir="_scripts/", $img_dir="_images/")
	{
		$output =  "$(function() {\n".
				   "$(\"#".$field_name."\").makeAsyncUploader({\n".
				   "upload_url: \"".$upload_url."\",\n" .
				   "flash_url: '".$host.$swf_dir."swfupload.swf',\n".
				   "file_size_limit: '32 MB',\n".
				   "button_image_url: '".$img_dir."blankButton.png',\n".
				   "disableDuringUpload: 'INPUT[type=\"submit\"]',\n".
				   "file_types: \"*.gif; *.jpg; *.jpeg; *.png\"\n".
				   "});\n".
				   "var filename = document.getElementById('".$field_name."_file');\n".
				   "if (filename!=null) alert('value:'+filename.innerHTML);\n";
		$output .= "});\n";
		
		return $output;
	}
	 //*********************************************************************************************************************************

	
	/***********************************************************************************************************************************/
	/**
	* nl2p: Return a string of text with paragraphs surrounded by <p> tags
 	* @param 	string	$str	String to be converted
	* @return	string	$out	Converted string
	*/
	function nl2p($str) 
	{
		$arr = explode(PHP_EOL,$str);
		$out = '';

		for($i=0; $i<count($arr); $i++):

			if(strlen(trim($arr[$i]))>0):
				$out.='<p>'.trim($arr[$i]).'</p>';
			endif;

		endfor;

		return $out;
	}
	/**********************************************************************************************************************************/
	
}


?>