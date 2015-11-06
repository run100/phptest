<?php
/**
 * @Author: TonyLevid
 * @Copyright: TonyLevid.com
 * @Name: Image Exif Class
 * @Version: 0.0.1
 * ��ӭ��λ���ԣ�����BUG���뵽��վ����
 * I'm pleased if you are willing to test my Image Exif class,if bug exists,you can leave a message.
 **/

//error_reporting(0);   �������û����������������bug  
header("content-type:text/html;charset=utf-8");   //�����htmlҳ���Ѿ����ñ��룬��ɾ������  
class imgExif{
  public $imgPath;
  public $unitFlag;
  public $imgInfoAll;
  public $imgInfoAllCN;
  public $imgInfoCommon;
  public $imgInfoBrief;
  public $imgInfoAllCNUnit;

  /*���캯�������exif��mbstringģ���Ƿ���*/
  function __construct(){
    extension_loaded('exif')&&extension_loaded('mbstring') or
    die('exif module was not loaded,please check it in php.ini<br>NOTICE:On Windows,php_mbstring.dll must be before php_exif.dll');
  }

  /*��ȡͼƬ��ʽ������ͼƬ��ʽ��Ϣ
  *     ���ֻ��ȡͼƬ��ʽ��Ϣ��������ô˷���
  *
  * @param $imgPath(����,�ַ���)��ͼƬ·��������Ϊurl��
  * @param $MimeOrExifOrExtension(��ѡ,�ַ���)����ȡͼƬ��ʽΪMime���ͻ�Exif���ͻ�ͼƬ�����ļ���׺��
  *      ���Ϊ�ַ���'Mime'�����ȡMimeͼƬ���͡�
  *      ���Ϊ�ַ���'Exif'�����ȡExifͼƬ���͡�
  *      ���Ϊ�ַ���'Extension'�����ȡͼƬ���͵��ļ���׺��
  *      �����д�����쳣��ȱʡ����Ĭ�ϻ�ȡMimeͼƬ���͡�
  */

  function getImgtype($imgPath,$MimeOrExifOrExtension = null){
    $exifImgtype = array(
      'IMAGETYPE_GIF' => 1,
      'IMAGETYPE_JPEG' => 2,
      'IMAGETYPE_PNG' => 3,
      'IMAGETYPE_SWF' => 4,
      'IMAGETYPE_PSD' => 5,
      'IMAGETYPE_BMP' => 6,
      'IMAGETYPE_TIFF_II' => 7, //��Intel �ֽ�˳��
      'IMAGETYPE_TIFF_MM' => 8, //��Motorola �ֽ�˳��
      'IMAGETYPE_JPC' => 9,
      'IMAGETYPE_JP2' => 10,
      'IMAGETYPE_JPX' => 11,
      'IMAGETYPE_JB2' => 12,
      'IMAGETYPE_SWC' => 13,
      'IMAGETYPE_IFF' => 14,
      'IMAGETYPE_WBMP' => 15,
      'IMAGETYPE_XBM' => 16
    );
    $exifType = array_search(exif_imagetype($imgPath),$exifImgtype);
    $mimeType = image_type_to_mime_type(exif_imagetype($imgPath));
    $extension = substr(image_type_to_extension(exif_imagetype($imgPath)),1);
    if($MimeOrExifOrExtension){
      if($MimeOrExifOrExtension === 'Mime'){
        return $mimeType;
      }
      elseif($MimeOrExifOrExtension === 'Exif'){
        return $exifType;
      }
      elseif($MimeOrExifOrExtension === 'Extension'){
        return $extension;
      }
      else{
        return $mimeType;
      }
    }
    else{
      return $mimeType;
    }
  }

  /*����Exif��Ϣ*/
  function imgInfo(){
    $imgPath = $this->imgPath;

    $imgInfoAll = exif_read_data($imgPath,0,1);
    foreach($imgInfoAll as $section => $arrValue){
      foreach($arrValue as $key => $value){
        $infoAllKey[] = $key;
        $infoAllValue[] = $value;
      }
    }
    $infoAll = array_combine($infoAllKey,$infoAllValue);

    $translate = array(
      'FileName'=>'�ļ���',
      'FileDateTime' => '�ļ��޸�ʱ��',
      'FileSize' => '�ļ���С',
      'FileType' => 'Exif�ļ�����',
      'MimeType' => 'Mime�ļ�����',
      'SectionsFound' => '�ҵ�Sections',
      'html' => 'html��ͼƬ���',
      'Height' => 'ͼƬ�߶�',
      'Width' => 'ͼƬ���',
      'IsColor' => '�Ƿ��ɫ',
      'ByteOrderMotorola' => '�Ƿ�ΪMotorola�ֽ�˳��',
      'ApertureFNumber' => '��Ȧ��',
      'Comments' => '����ע��',
      'Author' => '����',
      'UserComment' => '�û�ע��',
      'UserCommentEncoding' => '�û�ע�ͱ���',
      'Thumbnail.FileType' => '����ͼExif�ļ�����',
      'Thumbnail.MimeType' => '����ͼMime�ļ�����',
      'Make' => '������',
      'Model' => '�ͺ�',
      'Orientation' => '����',
      'XResolution' => 'ˮƽ�ֱ���',
      'YResolution' => '��ֱ�ֱ���',
      'ResolutionUnit' => '�ֱ��ʵ�λ',
      'Software' => '�������',
      'DateTime' => '����޸�ʱ��',
      'YCbCrPositioning' => 'YCbCrλ�ÿ���',
      'Exif_IFD_Pointer' => 'Exifͼ��IFD��ָ��',
      'Compression' => 'ѹ����ʽ',
      'JPEGInterchangeFormat' => 'JPEG SOIƫ��',
      'JPEGInterchangeFormatLength' => 'JPEG�����ֽ�',
      'ExposureTime' => '�ع�ʱ��',
      'FNumber' => '�������',
      'ExposureProgram' => '�ع����',
      'ISOSpeedRatings' => 'ISO�й��',
      'ExifVersion' => 'Exif�汾',
      'DateTimeOriginal' => '����ʱ��',
      'DateTimeDigitized' => '���ֻ�ʱ��',
      'ComponentsConfiguration' => '��������',
      'CompressedBitsPerPixel' => 'ͼ��ѹ����',
      'ExposureBiasValue' => '�عⲹ��',
      'MaxApertureValue' => '����Ȧֵ',
      'MeteringMode' => '���ģʽ',
      'LightSource' => '��Դ',
      'Flash' => '�����',
      'FocalLength' => '����',
      'SubSecTime' => '����ʱ��',
      'SubSecTimeOriginal' => '���뼶����ʱ��',
      'SubSecTimeDigitized' => '���뼶���ֻ�ʱ��',
      'FlashPixVersion' => 'FlashPix�汾',
      'ColorSpace' => 'ɫ�ʿռ�',
      'ExifImageWidth' => 'ExifͼƬ���',
      'ExifImageLength' => 'EXifͼƬ�߶�',
      'InteroperabilityOffset' => 'IFD��ʽ����ƫ����',
      'SensingMethod' => '��ɫ���򴫸�������',
      'FileSource' => 'ͼƬ��Դ',
      'SceneType' => '��������',
      'CFAPattern' => '�˲�����ͼ��',
      'CustomRendered' => '�Զ���ͼ����',
      'ExposureMode' => '�ع�ģʽ',
      'WhiteBalance' => '��ƽ��',
      'DigitalZoomRatio' => '��λ�佹����',
      'FocalLengthIn35mmFilm' => '�ȼ�35mm����',
      'SceneCaptureType' => 'ȡ��ģʽ',
      'GainControl' => '�������',
      'Contrast' => '�Աȶ�',
      'Saturation' => '���Ͷ�',
      'Sharpness' => '������',
      'SubjectDistanceRange' => '�Խ�����',
      'InterOperabilityIndex' => 'InterOperabilityָ��',
      'InterOperabilityVersion' => 'InterOperability�汾'
    );

    @$translate_unit = array(
      '�ļ���' => $infoAll['FileName'],
      '�ļ��޸�ʱ��' => date('Y:m:d H:i:s',$infoAll['FileDateTime']),
      '�ļ���С' => round($infoAll['FileSize']/1024) . 'kb',
      'Exif�ļ�����' => $this->getImgtype($imgPath,'Exif'),
      'Mime�ļ�����' => $infoAll['MimeType'],
      '�ҵ�Sections' => $infoAll['SectionsFound'],
      'html��ͼƬ���' => $infoAll['html'],
      'ͼƬ�߶�' => $infoAll['Height'] . 'px',
      'ͼƬ���' => $infoAll['Width'] . 'px',
      '�Ƿ��ɫ' => $infoAll['IsColor'] == 1 ? '��' : '��',
      '�Ƿ�ΪMotorola�ֽ�˳��' => $infoAll['ByteOrderMotorola'] == 1 ? '��' : '��',
      '��Ȧ��' => $infoAll['ApertureFNumber'],
      '����ע��' => $infoAll['Comments'],
      '����' => $infoAll['Author'],
      '�û�ע��' => $infoAll['UserComment'],
      '�û�ע�ͱ���' => $infoAll['UserCommentEncoding'],
      '����ͼExif�ļ�����' => $this->getImgtype($imgPath,'Exif'),
      '����ͼMime�ļ�����' => $infoAll['Thumbnail.MimeType'],
      '������' => $infoAll['Make'],
      '�ͺ�' => $infoAll['Model'],
      '����' => array_search($infoAll['Orientation'],array(
        'top left side' => 1,
        'top right side' => 2,
        'bottom right side' => 3,
        'bottom left side' => 4,
        'left side top' => 5,
        'right side top' => 6,
        'right side bottom' => 7,
        'left side bottom' => 8
      )),
      'ˮƽ�ֱ���' => $infoAll['XResolution'],
      '��ֱ�ֱ���' => $infoAll['YResolution'],
      '�ֱ��ʵ�λ' => array_search($infoAll['ResolutionUnit'],array(
        '�޵�λ' => 1,
        'Ӣ��' => 2,
        '����' => 3
      )),
      '�������' => $infoAll['Software'],
      '����޸�ʱ��' => $infoAll['DateTime'],
      'YCbCrλ�ÿ���' => $infoAll['YCbCrPositioning'] == 1 ? '�������е�����' : '��׼��',
      'Exifͼ��IFD��ָ��' => $infoAll['Exif_IFD_Pointer'],
      'ѹ����ʽ' => $infoAll['Compression'] == 6 ? 'jpegѹ��' : '��ѹ��',
      'JPEG SOIƫ��' => $infoAll['JPEGInterchangeFormat'],
      'JPEG�����ֽ�' => $infoAll['JPEGInterchangeFormatLength'],
      '�ع�ʱ��' => $infoAll['ExposureTime'] . '��',
      '�������' => $infoAll['FNumber'],
      '�ع����' => array_search($infoAll['ExposureProgram'],array(
        '�ֶ�����' => 1,
        '�������' => 2,
        '��Ȧ����' => 3,
        '��������' => 4,
        '��������' => 5,
        '�˶�ģʽ' => 6,
        'Ф��ģʽ' => 7,
        '�羰ģʽ' => 8
      )),
      'ISO�й��' => $infoAll['ISOSpeedRatings'],
      'Exif�汾' => $infoAll['ExifVersion'],
      '����ʱ��' => $infoAll['DateTimeOriginal'],
      '���ֻ�ʱ��' => $infoAll['DateTimeDigitized'],
      '��������' => $infoAll['ComponentsConfiguration'],
      'ͼ��ѹ����' => $infoAll['CompressedBitsPerPixel'],
      '�عⲹ��' => $infoAll['ExposureBiasValue'] . '���ӷ���',
      '����Ȧֵ' => $infoAll['MaxApertureValue'],
      '���ģʽ' => array_search($infoAll['MeteringMode'],array(
        'δ֪' => 0,
        'ƽ��' => 1,
        '�����ص�ƽ�����' => 2,
        '���' => 3,
        '����' => 4,
        '����' => 5,
        '�ֲ�' => 6,
        '����' => 255
      )),
      '��Դ' => array_search($infoAll['LightSource'],array(
        'δ֪' => 0,
        '�չ��' => 1,
        'ӫ���' => 2,
        '��˿��' => 3,
        '�����' => 10,
        '��׼�ƹ�A' => 17,
        '��׼�ƹ�B' => 18,
        '��׼�ƹ�C' => 19,
        'D55' => 20,
        'D65' => 21,
        'D75' => 22,
        '����' => 255,
      )),
      '�����' => array_search($infoAll['Flash'],array(
        '�����δ����' => 0,
        '�����������' => 1,
        '����������⵫Ƶ���۲���δ��⵽���ع�Դ' => 5,
        '�����������,Ƶ���۲�����⵽���ع�Դ' => 7
      )),
      '����' => $infoAll['FocalLength'] . '����',
      '����ʱ��' => $infoAll['SubSecTime'],
      '���뼶����ʱ��' => $infoAll['SubSecTimeOriginal'],
      '���뼶���ֻ�ʱ��' => $infoAll['SubSecTimeDigitized'],
      'FlashPix�汾' => $infoAll['FlashPixVersion'],
      'ɫ�ʿռ�' => $infoAll['ColorSpace'] == 1 ? 'sRGB' : 'Uncalibrated',
      'ExifͼƬ���' => $infoAll['ExifImageWidth'] . 'px',
      'EXifͼƬ�߶�' => $infoAll['ExifImageLength'] . 'px',
      'IFD��ʽ����ƫ����' => $infoAll['InteroperabilityOffset'],
      '��ɫ���򴫸�������' => $infoAll['SensingMethod'] == 2 ? '��ɫ��������' : '����',
      'ͼƬ��Դ' => $infoAll['FileSource'] == '0x03' ? '�������' : '����',
      '��������' => $infoAll['SceneType'] == '0x01' ? 'ֱ������' : '����',
      '�˲�����ͼ��' => $infoAll['CFAPattern'],
      '�Զ���ͼ����' => $infoAll['CustomRendered'],
      '�ع�ģʽ' => $infoAll['CustomRendered'] == 1 ? '�ֶ�' : '�Զ�',
      '��ƽ��' => $infoAll['WhiteBalance'] == 1 ? '�ֶ�' : '�Զ�',
      '��λ�佹����' => $infoAll['DigitalZoomRatio'],
      '�ȼ�35mm����' => $infoAll['FocalLengthIn35mmFilm'] . '����',
      'ȡ��ģʽ' => array_search($infoAll['SceneCaptureType'],array(
        '�Զ�' => 0,
        'Ф�񳡾�' => 1,
        '���۳���' => 2,
        '�˶�����' => 3,
        'ҹ��' => 4,
        '�Զ��ع�' => 5,
        '��Ȧ�����Զ��ع�' => 256,
        '���������Զ��ع�' => 512,
        '�ֶ��ع�' => 768,
      )),
      '�������' => $infoAll['GainControl'],
      '�Աȶ�' => array_search($infoAll['Contrast'],array(
        '��' => -1,
        '��ͨ' => 0,
        '��' => 1
      )),
      '���Ͷ�' => array_search($infoAll['Saturation'],array(
        '��' => -1,
        '��ͨ' => 0,
        '��' => 1
      )),
      '������' => array_search($infoAll['Sharpness'],array(
        '��' => -1,
        '��ͨ' => 0,
        '��' => 1
      )),
      '�Խ�����' => array_search($infoAll['SubjectDistanceRange'],array(
        'δ֪' => 0,
        '΢��' => 1,
        '����' => 2,
        'Զ��' => 3
      )),
      'InterOperabilityָ��' => $infoAll['InterOperabilityIndex'],
      'InterOperability�汾' => $infoAll['InterOperabilityVersion']
    );

    $infoAllCNKey = array_keys($translate);
    $infoAllCNName = array_values($translate);
    foreach($infoAllCNKey as $value){
      @$infoAllCNValue[] = $infoAll[$value];
    }
    $infoAllCNUnit = array_combine($infoAllCNName,array_values($translate_unit));
    $infoAllCN = array_combine($infoAllCNName,$infoAllCNValue);
    $infoCommon = array(
      $translate['FileName'] => $infoAll['FileName'],
      $translate['DateTimeOriginal'] => $infoAll['DateTimeOriginal'],
      $translate['MimeType'] => $infoAll['MimeType'],
      $translate['Width'] => $infoAll['Width'],
      $translate['Height'] => $infoAll['Height'],
      $translate['Comments'] => $infoAll['Comments'],
      $translate['Author'] => $infoAll['Author'],
      $translate['Make'] => $infoAll['Make'],
      $translate['Model'] => $infoAll['Model'],
      $translate['CompressedBitsPerPixel'] => $infoAll['CompressedBitsPerPixel'],
      $translate['ExposureBiasValue'] => $infoAll['ExposureBiasValue'],
      $translate['MaxApertureValue'] => $infoAll['MaxApertureValue'],
      $translate['MeteringMode'] => $infoAll['MeteringMode'],
      $translate['LightSource'] => $infoAll['LightSource'],
      $translate['Flash'] => $infoAll['Flash'],
      $translate['FocalLength'] => $infoAll['FocalLength'],
      $translate['SceneType'] => $infoAll['SceneType'],
      $translate['CFAPattern'] => $infoAll['CFAPattern'],
      $translate['CustomRendered'] => $infoAll['CustomRendered'],
      $translate['ExposureMode'] => $infoAll['ExposureMode'],
      $translate['WhiteBalance'] => $infoAll['WhiteBalance'],
      $translate['DigitalZoomRatio'] => $infoAll['DigitalZoomRatio'],
      $translate['FocalLengthIn35mmFilm'] => $infoAll['FocalLengthIn35mmFilm'],
      $translate['SceneCaptureType'] => $infoAll['SceneCaptureType'],
      $translate['GainControl'] => $infoAll['GainControl'],
      $translate['Contrast'] => $infoAll['Contrast'],
      $translate['Saturation'] => $infoAll['Saturation'],
      $translate['Sharpness'] => $infoAll['Sharpness'],
      $translate['SubjectDistanceRange'] => $infoAll['SubjectDistanceRange'],
      $translate['Software'] => $infoAll['Software'],
      $translate['DateTime'] => $infoAll['DateTime'],
      $translate['FileSize'] => $infoAll['FileSize']
    );
    foreach($infoCommon as $cKey => $cKalue){
      $infoCommonUnitKeys[] = $cKey;
      $infoCommonUnitValues[] = $translate_unit[$cKey];
    }
    $infoCommonUnit = array_combine($infoCommonUnitKeys,$infoCommonUnitValues);

    $infoBrief = array(
      $translate['FileName'] => $infoAll['FileName'],
      $translate['Width'] => $infoAll['Width'],
      $translate['Height'] => $infoAll['Height'],
      $translate['DateTimeOriginal'] => $infoAll['DateTimeOriginal'],
      $translate['Make'] => $infoAll['Make'],
      $translate['Model'] => $infoAll['Model'],
      $translate['MimeType'] => $infoAll['MimeType']
    );
    foreach($infoBrief as $bKey => $bValue){
      $infoBriefUnitKeys[] = $bKey;
      $infoBriefUnitValues[] = $translate_unit[$bKey];
    }
    $infoBriefUnit = array_combine($infoBriefUnitKeys,$infoBriefUnitValues);

    $this->imgInfoAll = $infoAll;
    $this->imgInfoAllCN = $infoAllCN;
    $this->imgInfoAllCNUnit = $infoAllCNUnit;
    $this->imgInfoCommon = $this->unitFlag ? $infoCommonUnit : $infoCommon;
    $this->imgInfoBrief = $this->unitFlag ? $infoBriefUnit : $infoBrief;
  }

  /*��ȡͼƬExif��Ϣ������Exif��Ϣһά����
  *
  * @param $imgPath(����,�ַ���)��ͼƬ·��������Ϊurl��
  * @param $iChoice(��ѡ,�ַ�����һά����)
  *    �˲�������������ģʽ��
  *      ���Ϊ�ַ���'All'�����ȡ������Ϣ��
  *      ���Ϊ�ַ���'Common'�����ȡ������Ϣ��
  *      ���Ϊ�ַ���'Brief'�����ȡ��Ҫ��Ϣ��
  *    �û������Զ��������ȡ��ȷ����Ϣ����array('ͼƬ���','ͼƬ�߶�')�����ȡͼƬ��Ⱥ͸߶ȡ�
  *    �����쳣�����ȱʡ�����Զ���ȡ��Ҫ��Ϣ��
  * @param $showUnit(��ѡ���ַ���)��ֻҪ��Ϊnull�����ȡ�Ѿ�ת�����ֵ�������ȡδת�����ֵ��
  */
  function getImgInfo($imgPath,$iChoice = null,$showUnit = null){
    $this->imgPath = $imgPath;
    $this->unitFlag = $showUnit;
    $this->imgInfo();
    $this->imgInfoAllCN = $showUnit ? $this->imgInfoAllCNUnit : $this->imgInfoAllCN;
    if($iChoice){
      if(is_string($iChoice)){
        if($iChoice === 'All'){
          return $this->imgInfoAllCN;
        }
        elseif($iChoice === 'AllUnit'){
          return $this->imgInfoAllCN;
        }
        elseif($iChoice === 'Common'){
          return $this->imgInfoCommon;
        }
        elseif($iChoice === 'Brief'){
          return $this->imgInfoBrief;
        }
        else{
          return $this->imgInfoBrief;
        }
      }
      elseif(is_array($iChoice)){
        foreach($iChoice as $value){
          $arrCustomValue[] = $this->imgInfoAllCN[$value];
        }
        $arrCustom = array_combine($iChoice,$arrCustomValue) or die('Ensure the array $iChoice values match $infoAll keys!');
        return $arrCustom;
      }
      else{
        return $this->imgInfoBrief;
      }
    }
    else{
      return $this->imgInfoBrief;
    }
  }
}

//ʾ����ͬʱ���ű�ִ��ʱ�� 

function exeTime(){
  $micro = microtime();
  list($usec,$sec) = explode(' ',$micro);
  return ($sec + $usec);
}

$start = exeTime();

$i = new imgExif();
//echo '<font color=\'blue\'>ͼƬ��ʽ��' . $i->getImgtype('12.jpg','Extension') . '<br><br></font>';  
$arr = $i->getImgInfo('12.jpg','All','1');
foreach($arr as $key => $value){
  echo $key . ': ' . $value . '<br>';
}

$end = exeTime();
echo '<br><font color=\'red\'>�ű�ִ��ʱ�䣺' . ($end - $start) . '<br></font>';  