<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "garretdepass@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "aa4d79" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha||ajax|', "|{$mod}|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    if( !phpfmg_user_isLogin() ){
        exit;
    };

    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $filelink =  base64_decode($_REQUEST['filelink']);
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . basename($filelink);

    // 2016-12-05:  to prevent *LFD/LFI* attack. patch provided by Pouya Darabi, a security researcher in cert.org
    $real_basePath = realpath(PHPFMG_SAVE_ATTACHMENTS_DIR); 
    $real_requestPath = realpath($file);
    if ($real_requestPath === false || strpos($real_requestPath, $real_basePath) !== 0) { 
        return; 
    }; 

    if( !file_exists($file) ){
        return ;
    };
    
    phpfmg_util_download( $file, $filelink );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function __construct(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }

    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function __construct( $text = '', $len = 4 ){
        $this->phpfmgImage( $text, $len );
    }

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'BA68' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGaY6IIkFTGEMYXR0CAhAFmtlbWVtcHQQQVEn0ujawABTB3ZSaNS0lalTV03NQnIfWB2GeaKhrg2BqOa1gswLxLDDEU1vaIBIowOamwcq/KgIsbgPALlvzoiUmxQwAAAAAElFTkSuQmCC',
			'5931' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGVqRxQIaWFtZGx2mooqJNDo0BIQiiwUGAMUaHWB6wU4Km7Z0adbUVUtR3NfKGIikDirGADIP1d5WFgwxkSlgt6CIsQaA3RwaMAjCj4oQi/sADKXNlhKsxBgAAAAASUVORK5CYII=',
			'9A03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIQ6IImJTGEMYQhldAhAEgtoZW1ldHRoEEERE2l0bQhoCEBy37Sp01amropamoXkPlZXFHUQ2CoaChJDNk8AaJ4jmh0iU0QaHdDcwhoAFENz80CFHxUhFvcBACsZzTSSUVimAAAAAElFTkSuQmCC',
			'2572' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QwQ2AMAgA6YMNcB/cAJPycRr66AbVDfx0StuXGH1qUkhIuAC5APURBiPlL34ok6LKxo5RoVZFxDHJnS1MfjtThMRG3m/fjnrUuno/aVOlT167gVsvkG8uRmlmKJ6RYcZ+wTHVENGCxgH+92G++J1Vj8wJegz/fAAAAABJRU5ErkJggg==',
			'FD76' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA6Y6IIkFNIi0AsmAAFSxRoeGQAcBdLFGRwdk94VGTVuZtXRlahaS+8DqpjBimhfA6CCCJubogCHWytrAgKYX6OYGBhQ3D1T4URFicR8AxnDOGKc1nJAAAAAASUVORK5CYII=',
			'12C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHVqRxVgdWFsZHQKmOiCJiTqINLo2CAQEoOhlAIoxOogguW9l1qqlS4HkNCT3AdVNYUWog4kFYIoxOrBi2AFSheaWENFQBzQ3D1T4URFicR8AIMLI719OKl8AAAAASUVORK5CYII=',
			'0C00' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMLQii7EGsDY6hDJMdUASE5ki0uDo6BAQgCQW0CrSwNoQ6CCC5L6opdNWLV0VmTUNyX1o6nCKYbMDm1uwuXmgwo+KEIv7AAIBzBEm72DzAAAAAElFTkSuQmCC',
			'4445' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjC0MjQ6hgYgi4UwTGVodXRAVscYwhDKMBVVjHUKoytDoKOrA5L7pk1bunRlZmZUFJL7AqaItLI2OjSIIOkNDRUNdQXaKoLpFgdMMYeAAEyxqQ6DIfyoB7G4DwBUusvW+w53LgAAAABJRU5ErkJggg==',
			'E450' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMYWllDHVqRxQIaGKayArEDqlgoUCwgAEWM0ZV1KqODCJL7QqOWLl2amZk1Dcl9AQ0iQPMDYeqgYqKhDhhiQLc0BKDZwdDK6OiA4haQmxlCGVDcPFDhR0WIxX0A0j7Mwa33li8AAAAASUVORK5CYII=',
			'0661' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mIouJTBFpZG1wCEUWC2gVaWBtgOsFOylq6bSwpVNXLUV2X0CraCuro0Mrmt5GVyCJbge6GNQtKGJQN4cGDILwoyLE4j4A+/XLZCEKtbYAAAAASUVORK5CYII=',
			'5F2B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMdkMQCGkQaGB0dHQLQxFgbAh1EkMQCA0C8QJg6sJPCpk0NW7UyMzQL2X2tQHWtjCjmgcWmMKKYFwASC0AVE5kCdIsDql5WoL2soYEobh6o8KMixOI+AOW3yuxokxlSAAAAAElFTkSuQmCC',
			'B564' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QsQ3AIAwETcEGZB8o0jsSbpjGFN4ARqBhylBikjJR4u9OL/tk6Jdh+FNe8SPcCAgYJ4bFsQk+KyaOLXtZetEyFJz8KNXWak9p8sMCeQ/B632D8UFR3xgMFxcrw0UxQhNX56/+92Bu/E46wc+CLyswrQAAAABJRU5ErkJggg==',
			'103C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGaYGIImxOjCGsDY6BIggiYk6sLYyNAQ6sKDoFWl0aHR0QHbfyqxpK7OmrsxCdh+aOoQY0DxUMWx2YHFLCKabByr8qAixuA8A2yvIyKhY83YAAAAASUVORK5CYII=',
			'42C6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpI37pjCGMIQ6THVAFgthbWV0CAgIQBJjDBFpdG0QdBBAEmOdwgAUY3RAdt+0aauWLl21MjULyX0BUximsDYwopgXGsoQABRzEEF1iwMr0A5UMZAqVLcwTBENdUB380CFH/UgFvcBAO3qy0L/+i94AAAAAElFTkSuQmCC',
			'5523' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkNEQxmA0AFJLKBBpIHR0dEhAE2MFUwixAIDREJAMgFI7gubNnXpqpVZS7OQ3dfK0OjQytCAbB5YbAoDinkBrSKNDgGoYiJTWFsZHRhR3MIawBjCGhqA4uaBCj8qQizuAwD8psyrWxnx6gAAAABJRU5ErkJggg==',
			'0954' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHRoCkMRYA1hbWRsYGpHFRKaINLo2MLQiiwW0AsWmMkwJQHJf1NKlS1Mzs6KikNwX0MoY6NAQ6ICql6ERKBYagmIHC9COAAy3MDqiug/kZoZQBhSxgQo/KkIs7gMA2djNirQLZccAAAAASUVORK5CYII=',
			'35E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQ1lDHaY6IIkFTBFpYG1gCAhAVtkKEmN0EEEWmyISgiQGdtLKqKlLl4auigpDdt8UhkbXBoapKHpbwWINqGIiIDEUOwKmsLaiu0U0gDEE3c0DFX5UhFjcBwD/GMtRdRqcyQAAAABJRU5ErkJggg==',
			'3805' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYQximMIYGIIkFTGFtZQhldEBR2SrS6OjoiCoGVMfaEOjqgOS+lVErw5auioyKQnYfWF1Agwiaea5YxEB2iGC4hSEA2X0QNzNMdRgE4UdFiMV9ALlcyzgpTyAdAAAAAElFTkSuQmCC',
			'C8F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA0NDkMREWllbWUE0klhAo0ijK7pYA0RdAJL7olatDFsaumplFpL7oOpaGVD0gs2bwoBpRwCyGMQtjA4YbkYTG6jwoyLE4j4A3RjLq5RalZMAAAAASUVORK5CYII=',
			'F0E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHaY6IIkFNDCGsDYwBASgiLG2sjYwOoigiIk0uiLEwE4KjZq2MjV0VVQYkvsg6himYuplaBDBsIMBzQ5sbsF080CFHxUhFvcBADkSzFBxgpFbAAAAAElFTkSuQmCC',
			'E6D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaY6IIkFNLC2sjY6BASgiIk0sjYEOoigijUgiYGdFBo1LWzpqqioMCT3BTSItrI2BExF09voCjYBQwzNDky3YHPzQIUfFSEW9wEAQqrN+odjWgIAAAAASUVORK5CYII=',
			'57CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CHUNDkMQCGhgaHR0CHRjQxFwbBFHEAgMYWlkbGGFiYCeFTVs1bemqlaFZyO5rZQhAUgcVY3RAFwsAmsaKZofIFBGgKlS3sAaINDCEOqKaN0DhR0WIxX0AeNnJmupySXEAAAAASUVORK5CYII=',
			'D52A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGVqRxQKmiDQwOjpMdUAWaxVpYG0ICAhAFQthaAh0EEFyX9TSqUtXrczMmobkvoBWhkaHVkaYOoTYFMbQEFTzGh0C0NRNYQXqRBULDWAMYQ0NRBEbqPCjIsTiPgA0FszIBkk+wgAAAABJRU5ErkJggg==',
			'AEEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHaYGIImxBog0sDYwAEmEmMgUkBijAwuSWEArRAzZfVFLp4YtDV2Zhew+NHVgGBqKKQZTh2kHqlsCWjHdPFDhR0WIxX0A08LKk6cPmTsAAAAASUVORK5CYII=',
			'A499' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2Quw2AMAxELwUbhH1MQW8kTJEN2MKNNyAjpIApEVTmU4LgrnuSrafDconiT33FLxAMgkyOVYwcGmJ2LE6QSjuKjrGF1rFdKZVS5jGlwfmxRUPP2d+K1ELKevwHC8p0YSeXjZ2dv9rvwd74rYSbzApYUmUfAAAAAElFTkSuQmCC',
			'C9D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUNDkMREWllbWRsdGkSQxAIaRRpdGwJQxRogYgFI7otatXRp6qqolVlI7gtoYAwEqmtlQNHLANI7BUWskQUkFsCA4RZHByxuRhEbqPCjIsTiPgAkBs2BmdowIwAAAABJRU5ErkJggg==',
			'78F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIu2srayNjAEBKCIiTS6NjA6iCCLTQGraxBBdl/UyrCloUAKyX2MDmB1jch2sDaAzGNoRXaLCERsCrJYQAPELahiQDc3MIaGDILwoyLE4j4AXQrLjcmdepIAAAAASUVORK5CYII=',
			'FC70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDA1qRxQIaWBsdGgKmOqCIiTQAxQIC0MQYGh0dRJDcFxo1bdWqpSuzpiG5D6xuCiNMHUIsAFPM0YEBzQ7WRtcGBjS3AN3cwIDi5oEKPypCLO4DAOI3ziVnxGNbAAAAAElFTkSuQmCC',
			'DF19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMEx1QBILmCLSwBDCEBCALNYq0sAYwugggibGMAUuBnZS1NKpYaumrYoKQ3IfRB3DVEy9DA1YxFDtmAIWQ3FLaADQLaEOKG4eqPCjIsTiPgBQs80wn3+M6QAAAABJRU5ErkJggg==',
			'315D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RAMYAlhDHUMdkMQCpjAGsDYwOgQgq2xlBYuJIItNAeqdChcDO2ll1KqopZmZWdOQ3QdUx9AQiKq3FbsYK5pYAFAvo6MjiltEgS5mCGVEcfNAhR8VIRb3AQCBh8hzk8//DAAAAABJRU5ErkJggg==',
			'F1B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGaY6IIkFNDAGsDY6BASgiLEGsDYEOgigiAH1Njo6ILsvNGpV1NLQlalZSO6DqkMzjwFsnggxYphuCUV380CFHxUhFvcBAERoy4wvMKflAAAAAElFTkSuQmCC',
			'4946' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjCGMDQ6THVAFgthbWVodQgIQBJjDBEBqnJ0EEASY50CFAt0dEB237RpS5dmZmamZiG5L2AKY6BroyOKeaGhDI2uoYEOIihuYWl0aHREEwO6pRHVLVjdPFDhRz2IxX0Azt3M0bQH+JUAAAAASUVORK5CYII=',
			'BD62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGaY6IIkFTBFpZXR0CAhAFmsVaXRtcHQQQVUHFGNoEEFyX2jUtJWpU1etikJyH1ido0OjA4Z5Aa0MmGJTGLC4BdPNjKEhgyD8qAixuA8AbePOwcJaSRcAAAAASUVORK5CYII=',
			'0DD2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaY6IImxBoi0sjY6BAQgiYlMEWl0bQh0EEESC2gFiQU0iCC5L2rptJWpq6KAEOE+qLpGB0y9rQwYdgRMYcDiFkw3M4aGDILwoyLE4j4AI/XNuXQEdh4AAAAASUVORK5CYII=',
			'8C29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGaY6IImJTGFtdHR0CAhAEgtoFWlwbQh0EEFRB+LBxcBOWho1bdWqlVlRYUjuA6trZZgqgmYewxSgHJqYQwADmh1AtzgwoLgF5GbW0AAUNw9U+FERYnEfAF2ozFGLh0KiAAAAAElFTkSuQmCC',
			'17F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxOjA0ujYwBAQgiYmCxRgdRFD0MrSyAmkRJPetzFo1bWnoqlVRSO4DqgsAqmt0QNHL6AAUa0V1C2sDUGwKqpgISCwAWUw0BCTGGBoyCMKPihCL+wAl98jCmbgfrgAAAABJRU5ErkJggg==',
			'0C86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGaY6IImxBrA2Ojo6BAQgiYlMEWlwbQh0EEASC2gVaWAEKkR2X9TSaatWha5MzUJyH1QdinkgMVageSJY7BAh4BZsbh6o8KMixOI+AFKIy5C9s0WlAAAAAElFTkSuQmCC',
			'92BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMdkMREprC2sjY6OgQgiQW0ijS6NgQ6iKCIMTS6AtWJILlv2tRVS5eGrsyahuQ+VleGKawIdRDYyhDAimaeQCujA7oY0C0N6G5hDRANdUVz80CFHxUhFvcBAHF+y3s1qF1XAAAAAElFTkSuQmCC',
			'8DAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhldAhAEgtoFWl0dHR0EEFV1+jaEAhTB3bS0qhpK1NXRYZmIbkPTR3cPNfQQBTzwGINgeh2tLKi6QW5GSiG4uaBCj8qQizuAwBM381TKs9KzgAAAABJRU5ErkJggg==',
			'C659' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHaY6IImJtLK2sjYwBAQgiQU0ijSyNjA6iCCLNYg0sE6Fi4GdFLVqWtjSzKyoMCT3BTSItgLJqWh6Gx3AJqDa4doQgGIHyC2Mjg4obgG5mSGUAcXNAxV+VIRY3AcADNvMM/MGEUQAAAAASUVORK5CYII=',
			'0FB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGaY6IImxBog0sDY6BAQgiYlMAYo1BDoIIIkFtILUOToguy9q6dSwpaErU7OQ3AdVh2IeWAxonggWO0QIuIURqIIVzc0DFX5UhFjcBwDk7cv++pegGwAAAABJRU5ErkJggg==',
			'1151' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHVqRxVgdGANYGximIouJOrCCxEIx9E5lgOkFO2ll1qqopZlZS5HdB1LH0BDQiq4XmxgrFjFGR1T3iYawhgJdEhowCMKPihCL+wBMVsbJw9ZklgAAAABJRU5ErkJggg==',
			'9DB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGVqRxUSmiLSyNjpMRRYLaBVpdG0ICMUQa3SA6QU7adrUaStTQ1ctRXYfqyuKOgiEmIciJoBFDOoWFDGom0MDBkH4URFicR8A7xfNlfoyA4oAAAAASUVORK5CYII=',
			'2CC2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxlCHaY6IImJTGFtdHQICAhAEgtoFWlwbRB0EEHWDRRjBalHdt+0aauWAqkoZPcFgNU1ItvB6AAWa0VxSwPIDoEpyGJAVWC3IIuFhoLc7BgaMgjCj4oQi/sAZxjMRuIq484AAAAASUVORK5CYII=',
			'BB16' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQximMEx1QBILmCLSyhDCEBCALNYq0ugYwugggK5uCqMDsvtCo6aGrZq2MjULyX1QdRjmOQD1ihASA+tFdQvIzYyhDihuHqjwoyLE4j4APHDNKap7mm0AAAAASUVORK5CYII=',
			'43F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37prCGsIYGTHVAFgsRaWVtYAgIQBJjDGFodG1gdBBBEmOdwgBS1yCC5L5p01aFLQ1dtSoKyX0BEHWNyHaEhoLMY2hFdQtYbAqqGMQtGG5uYAwNGQzhRz2IxX0APZTLdPa5yTIAAAAASUVORK5CYII=',
			'354F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7RANEQxkaHUNDkMQCpog0MLQ6OqCobAWKTUUTmyISwhAIFwM7aWXU1KUrMzNDs5DdN4Wh0bUR3TygWGgguh2NDmjqAqawAlWiiokGMIagiw1U+FERYnEfADyIyozpVvJ3AAAAAElFTkSuQmCC',
			'4AD9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37pjAEsIYyTHVAFgthDGFtdAgIQBIDirSyNgQ6iCCJsU4RaXRFiIGdNG3atJWpq6KiwpDcFwBWFzAVWW9oqGgoUKxBBMUtYHUOGGJobgGLobt5oMKPehCL+wA+dM1/5ppcbwAAAABJRU5ErkJggg==',
			'DB57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUNDkMQCpoi0sgJpEWSxVpFGV0yxVtapQBrJfVFLp4YtzcxamYXkPpA6EMmAZp4D0CZ0MdeGgAAGNLcwOjo6oLuZIZQRRWygwo+KEIv7AHW2zcfrkY1zAAAAAElFTkSuQmCC',
			'D633' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUIdkMQCprC2sjY6OgQgi7WKNALJBhFUsQaGRoeGACT3RS2dFrZq6qqlWUjuC2gVbUVSBzfPAdM8TDEsbsHm5oEKPypCLO4DABzFz2Z9mCU1AAAAAElFTkSuQmCC',
			'7C43' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQxkaHUIdkEVbWRsdWh0dAlDERBocpjo0iCCLTQHyAh0aApDdFzVt1crMrKVZSO5jdBBpAJrYgGweK9Ak1tAAFPNEgNChEdWOAJDORlS3BDRgcfMAhR8VIRb3AQCem84/fPN0KgAAAABJRU5ErkJggg==',
			'F592' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIkFNIg0MDo6BASgibE2BDqIoIqFsIJlEO4LjZq6dGVm1KooJPcBzWl0CAloRLUDKNYQ0MqAal6jY0PAFFQx1laQW1DFGEMYQhlDQwZB+FERYnEfACsNzc2zuu3tAAAAAElFTkSuQmCC',
			'D4E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYWllDHUIdkMQCpjBMZW1gdAhAFmtlCGUF0iIoYoyuILEAJPdFLQWC0FVLs5DcF9Aq0oqkDiomGuqKYR5DK4YdU0BiqG7B5uaBCj8qQizuAwDSgs2IQitgwwAAAABJRU5ErkJggg==',
			'2B09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQKtLo6OjoIIKsu1WklbUhECYGcdO0qWFLV0VFhSG7LwCkLmAqsl5GB5FG14aABmQx1gaQHQ4odog0YLolNBTTzQMVflSEWNwHAG9Ry6lo4DL5AAAAAElFTkSuQmCC',
			'2B96' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoFWl0bQh0EEDW3SrSygoUQ3HftKlhKzMjU7OQ3Rcg0soQEohiHqODSKMDUK8IslsaRBod0cREGjDdEhqK6eaBCj8qQizuAwAzYst0TgFZ+QAAAABJRU5ErkJggg==',
			'211F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYAhhdEBWF9DKGsCIJsbQCtYLE4O4adqqqFXTVoZmIbsvAEUdGAJ5GGKsDZhiIljEQkNZQxlDHVHdMkDhR0WIxX0AuDrGK4wLWpkAAAAASUVORK5CYII=',
			'EB62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6iKCpYwXSIkjuC42aGrZ06qpVUUjuA6tzdGh0wDAvoJUBU2wKAxa3YLqZMTRkEIQfFSEW9wEASlDN3v3VEd0AAAAASUVORK5CYII=',
			'4540' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpI37poiGMjQ6tKKIhYg0MLQ6THVAEmMEiU11CAhAEmOdIhLCEOjoIILkvmnTpi5dmZmZNQ3JfQFTGBpdG+HqwDAUaKtraCCKGMMUkUaHRlQ7GKawtgLdh+IWhimMIRhuHqjwox7E4j4AEAfNDuGmazsAAAAASUVORK5CYII=',
			'3A2C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7RAMYAhhCGaYGIIkFTGEMYXR0CBBBVtnK2sraEOjAgiw2RaTRASiG7L6VUdNWZq3MzEJxH0hdK6MDis2toqEOU9DFgOoCGFHsCADqdXRgQHGLaIBIo2toAIqbByr8qAixuA8AjL3LF3DXJ/IAAAAASUVORK5CYII=',
			'48D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpI37pjCGsIYyhoYgi4WwtrI2OjSIIIkxhog0ujYEoIixTgGqA4oFILlv2rSVYUtXRa3MQnJfAERdK7K9oaFg86agugUsFoAqBnKLowMWN6OKDVT4UQ9icR8A0cLMmXT+OpwAAAAASUVORK5CYII=',
			'9F38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQx1DGaY6IImJTBFpYG10CAhAEgtoFQGSgQ4i6GIIdWAnTZs6NWzV1FVTs5Dcx+qKog4CsZgngEUMm1tYA0QaGNHcPFDhR0WIxX0APS/M7hSWR1wAAAAASUVORK5CYII=',
			'D432' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGaY6IIkFTGGYytroEBCALNbKEMrQEOgggiLG6MrQ6NAgguS+qKVLl66aCqSR3BfQKtIKVNeIYkeraKgDyFRUO1rBtqO6pRXkFkw3M4aGDILwoyLE4j4AQKnOnyeQjUwAAAAASUVORK5CYII=',
			'022E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYA1hbGR0dHZDViUwRaXRtCEQRC2hlaHRAiIGdFLV01dJVKzNDs5DcB1Q3haGVEV1vAMMURjQ7gPwAVDGgW0BuRBFjdBANdQ0NRHHzQIUfFSEW9wEAAtDIu69plkMAAAAASUVORK5CYII=',
			'2645' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ2AQAhFsbgNcJ9/hT0mnoUbuAUUbKDuoFOqHUZLTeR3L5/wAm23UfpTPvFLUnVkuUhgPCUnz4g9cTaar4ycldrcIPotS7+O4zBEP6k9GZTDbgW25rgaWVI2WEZkrIeLQaJfKaczZvzgfy/mwW8HUMTLu9CwVI8AAAAASUVORK5CYII=',
			'1AE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHVqRxVgdGENYGximIouJOrC2AsVCUfWKNLo2MMD0gp20MmvaytTQVUuR3YemDiomGoophk0dpphoCFAs1CE0YBCEHxUhFvcBAC6SyV8ZWlHCAAAAAElFTkSuQmCC',
			'0BB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGaY6IImxBoi0sjY6BAQgiYlMEWl0bQh0EEASC2gFqXN0QHZf1NKpYUtDV6ZmIbkPqg7FPKAY2DwRLHaIEHALNjcPVPhREWJxHwBbssxpqb43UAAAAABJRU5ErkJggg==',
			'236C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaYGIImJTBFpZXR0CBBBEgtoZWh0bXB0YEHW3crQytrA6IDivmmrwpZOXZmF4r4AoDpHRwdke4G6gOYFooixNkDEkO0QacB0S2goppsHKvyoCLG4DwDleMplJbJduQAAAABJRU5ErkJggg==',
			'85D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGVqRxUSmiDSwNjpMRRYLaAWKNQSEoqkLAYrB9IKdtDRq6tKlq6KWIrtPZApDoytCHdQ8bGIiGGIiU1hbgW5BEWMNYAwBujk0YBCEHxUhFvcBALrvzYtl+boZAAAAAElFTkSuQmCC',
			'F1D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGaY6IIkFNDAGsDY6BASgiLEGsDYEOoigiDEgi4GdFBq1KmrpqqioMCT3QdQFTMXUG9CARQzTDky3hKK7eaDCj4oQi/sAu33L7x+mgi8AAAAASUVORK5CYII=',
			'B213' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMIQ6IIkFTGFtZQhhdAhAFmsVaXQMYWgQQVHH0OgwBUgjuS80atXSVdNWLc1Cch9QHQg2oJrHEAASQzGvldEBQ2wKawPDFFS3hAaIhjqGOqC4eaDCj4oQi/sAG+/Nw1MKwxMAAAAASUVORK5CYII=',
			'1950' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHVqRxVgdWFtZGximOiCJiTqINLo2MAQEoOgFik0FkkjuW5m1dGlqZmbWNCT3Ae0IdGgIhKmDijE0YoqxAO0IQLODtZXR0QHVLSGMIQyhDChuHqjwoyLE4j4ApN7JZagvWLoAAAAASUVORK5CYII=',
			'1747' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHUNDkMRYHRgaHVodGkSQxERBYlNRxRgdGFoZAh0aApDctzJr1bSVmVlACuE+oLoAVqCJqPYyOrCGBkxBFWNtANoSgCoGtLHR0QFZTDQEU2ygwo+KEIv7AKDTydH8+b+OAAAAAElFTkSuQmCC',
			'E14A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMYAhgaHVqRxQIaGAMYWh2mOqCIsQYwTHUICEARA+oNdHQQQXJfaNSqqJWZmVnTkNwHUsfaCFeHEAsNDA1BNw+LOnSx0BDWUHSxgQo/KkIs7gMAPozLR5Ux5boAAAAASUVORK5CYII=',
			'6A3F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGUNDkMREpjCGsDY6OiCrC2hhbWVoCEQVaxBpdECoAzspMmrayqypK0OzkNwXMgVFHURvq2ioA7p5rUB1aGIiQL2uaHpZA0QaHUMZUcQGKvyoCLG4DwBVG8uyIzd6ygAAAABJRU5ErkJggg==',
			'9C65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGUMDkMREprA2Ojo6OiCrC2gVaXBtwBRjbWB0dUBy37Sp01YtnboyKgrJfayuQHWODg0iyDaD9QagiAmA7Qh0EMFwi0MAsvsgbmaY6jAIwo+KEIv7AHajy7lML8L3AAAAAElFTkSuQmCC',
			'F312' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNZQximMEx1QBILaBBpZQhhCAhAEWNodAxhdBBBFWsF6m0QQXJfaNSqsFXTVq2KQnIfVF2jA5p5DlOA4phiUxjQ3TKFIQBVjDWEMdQxNGQQhB8VIRb3AQBO+c0sBF2YPQAAAABJRU5ErkJggg==',
			'F204' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nM3QMQ7AIAhAURy8QXsfHNxpIgunwcEbeAUXT1lHpR3btLB9Y/IC9Mso/Glf8TG7BBWUpkbqCzDktW05BCxrgxyVKk0+lt5aF5HJN96r1wPNXxqN09IcuoDWosNi2s5ozF/d78G98Z2db87z0I15lwAAAABJRU5ErkJggg==',
			'04FF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7GB0YWllDA0NDkMRYAximsoJkkMREpjCEoosFtDK6IomBnRS1FAhCV4ZmIbkvoFWkFVOvaKgrph0Y6oBuwRADuxlNbKDCj4oQi/sAE7rH/M2uwngAAAAASUVORK5CYII=',
			'3471' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWllDA1qRxQKmMEwFklNRVLYyhALFQlHEpjC6MjQ6wPSCnbQyaunSVSCI7L4pIq0MUxhaUc0TDXUIQBdjaGV0YEB3SytrA6oY2M0NDKEBgyD8qAixuA8An7rLl7APHmUAAAAASUVORK5CYII=',
			'1BFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6YGIImxOoi0sjYwBIggiYk6iDS6AlWzoOgFqWN0QHbfyqypYUtDV2Yhuw9NHUwMbB42MUw70NwSAnRzAwOKmwcq/KgIsbgPANkYx++kyV5CAAAAAElFTkSuQmCC',
			'CC35' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WEMYQ0EwAElMpJW10bXR0QFZXUCjSINDQyCqWINIA0Ojo6sDkvuiVk1btWrqyqgoJPdB1DmASFS9UBLdDmQxiFscApDdB3Ezw1SHQRB+VIRY3AcApdTNh9aKLfoAAAAASUVORK5CYII=',
			'75B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGaY6IIu2ijSwNjoEBKCLNQQ6iCCLTREJQVIHcVPU1KVLQ1dNzUJyH6MDQ6MrmnmsDUAxNPNEGkQwxAIaWFvR3RLQwBiC4eYBCj8qQizuAwAZ680SzdXhHAAAAABJRU5ErkJggg==',
			'DDD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDGUMDkMQCpoi0sjY6OiCrC2gVaXRtCMQm5uqA5L6opdNWpq6KjIpCch9EXUCDCIZebGKBDiIYbnEIQHYfxM0MUx0GQfhREWJxHwCXb88W4ZaYfgAAAABJRU5ErkJggg==',
			'E2B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGVqRxQIaWFtZGx2mOqCIiTS6NgQEBKCIMTS6Njo6iCC5LzRq1dKloSuzpiG5D6huCitCHUwsgLUhEE2M0YEVww7WBnS3hIaIhrqiuXmgwo+KEIv7AG2RzfVIgTeDAAAAAElFTkSuQmCC',
			'042E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUMDkMRYAximMjo6OiCrE5nCEMraEIgiFtDK6MqAEAM7KWrp0qWrVmaGZiG5L6BVpJWhlRFNr2iowxRGdDtaGQJQxYBuAepEFQO5mTU0EMXNAxV+VIRY3AcAi2zIdLudPgkAAAAASUVORK5CYII=',
			'DF75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA0MDkMQCpogAyUAHZHUBrTjEGh1dHZDcF7V0atiqpSujopDcB1Y3haFBBF1vAKYYowOjA4oY0C2sQJXI7gsNAItNdRgE4UdFiMV9AKbHzUChg/WNAAAAAElFTkSuQmCC',
			'7AB5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGUMDkEVbGUNYGx0dUFS2srayNgSiik0RaXRtdHR1QHZf1LSVqaEro6KQ3MfoAFLn0CCCpJe1QTTUtSEARUykAagOaAeyWEADWG9AALpYKMNUh0EQflSEWNwHAELwzNSgHRYpAAAAAElFTkSuQmCC',
			'C81D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQximMIY6IImJtLK2MoQwOgQgiQU0ijQ6AsVEkMUagOqmwMXATopatTJs1bSVWdOQ3IemDiom0uiALtaIKQZ2yxRUt4DczBjqiOLmgQo/KkIs7gMAxvXLLBg9Uh8AAAAASUVORK5CYII=',
			'6FA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQx2mMLQii4lMEWlgCGWY6oAkFtAi0sDo6BAQgCzWINLA2hDoIILkvsioqWFLV0VmTUNyX8gUFHUQva1AsVAsYg0BKHaIgPUGoLiFNQAshuLmgQo/KkIs7gMANOTNPLWCrTsAAAAASUVORK5CYII=',
			'D30B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNYQximMIY6IIkFTBFpZQhldAhAFmtlaHR0dHQQQRVrZW0IhKkDOylq6aqwpasiQ7OQ3IemDm6eK1BMhJAdWNyCzc0DFX5UhFjcBwAJBszhlmCN7AAAAABJRU5ErkJggg==',
			'BFCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHUMdkMQCpog0MDoEOgQgi7WKNLA2CDqIoKljBaoMQHJfaNTUsKWrVoZmIbkPTR2SeYyo5uGwA90toQFAFWhuHqjwoyLE4j4A4WDMsxjwkFcAAAAASUVORK5CYII=',
			'B1B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUIdkMQCpjAGsDY6OgQgi7WyBrA2BDSIoKgD6m10aAhAcl9o1KqopaGrlmYhuQ9NHdQ8BkzzsImB9aK6JRToYnQ3D1T4URFicR8A/7/M3QtA1uIAAAAASUVORK5CYII=',
			'A62C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWlkbQh0YEESC2gFqQh0QHZf1NJpYatWZmYhuy+gVbSVoZXRAdne0FCRRocpqGJA8xodAhjR7GAF6URxS0ArYwhraACKmwcq/KgIsbgPAEnLyvE3EtZ6AAAAAElFTkSuQmCC',
			'FF73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DA0IdkMQCGkSAZKBDAIYYhEQRa3RoCEByX2jU1LBVS1ctzUJyH1jdFIYGDPMCGDDMY3TAFGMFiqLrZW1gQHHzQIUfFSEW9wEADKTOQ8XHyo4AAAAASUVORK5CYII=',
			'F36E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUMDkMQCGkRaGR0dHRhQxBgaXRswxFpZGxhhYmAnhUatCls6dWVoFpL7wOqwmhdIhBg2t2C6eaDCj4oQi/sA1orLMjJAQlQAAAAASUVORK5CYII=',
			'29A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMLQii4lMYW1lCGWY6oAkFtAq0ujo6BAQgKwbKObaEOggguy+aUuXpq6KzJqG7L4AxkAkdWDI6MDQ6BqKKsbawAI0LwDFDpEG1lbWhgAUt4SGMoYAxVDcPFDhR0WIxX0AbyfMmTQgilYAAAAASUVORK5CYII=',
			'332A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANYQxhCGVqRxQKmiLQyOjpMdUBW2crQ6NoQEBCALDYFpC/QQQTJfSujVoWtWpmZNQ3ZfSB1rYwwdXDzHKYwhoagiwWgqgO7xQFVDORm1tBAVPMGKPyoCLG4DwCFw8qhhvXPGgAAAABJRU5ErkJggg==',
			'B2D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGRoCkMQCprC2sjY6NKKItYo0ugJJVHUMILEpAUjuC41atXTpqqioKCT3AdVNYW0IdEA1jyEAKBYagiLG6MAKdAmaWxqAbkERCw0QDXVFc/NAhR8VIRb3AQA+ndA2GmD25AAAAABJRU5ErkJggg==',
			'B049' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgMYAhgaHaY6IIkFTGEMYWh1CAhAFmtlbWWY6ugggqJOpNEhEC4GdlJo1LSVmZlZUWFI7gOpcwXagaK3FSgWGtAggm5HowOaHUC3NKK6BZubByr8qAixuA8AhYHOH3BFu6sAAAAASUVORK5CYII=',
			'F5C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVqRxQIaRBoYHQKmOqCJsTYIBASgioWwAlWKILkvNGrq0qWrVmZNQ3IfUE+jK0IdHjERoBi6HaytmG5hDEF380CFHxUhFvcBALhBzX4UbfOdAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>