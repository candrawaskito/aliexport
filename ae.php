<?php
/**
 * Created by PhpStorm.
 * User: Leayee
 * Date: 2016/7/13
 * Time: 20:35
 */

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');
   // curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

//通过url下载图片
function GrabImage($url,$filename="") {
    if($url=="") return false;

    if($filename=="") {
        $ext=strrchr($url,".");
//if($ext!=".gif" && $ext!=".jpg" && $ext!=".png") return false;
        $filename=date("YmdHis").$ext;

    }
    $base_path="C:/Users/leayee/Desktop/tmp/";
    if(!file_exists($base_path)){
        if (@mkdir(rtrim($base_path, '/'), 0777))
        {
            @chmod($base_path, 0777);
        }
    }
    $filename=$base_path.$filename;
    ob_start();
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);

    $fp2=@fopen($filename, "a");
    fwrite($fp2,$img);
    fclose($fp2);

    return $filename;
}
function SaveText($txt,$filename)
{
    //$base_path="../wp-content/uploads/wpallimport/files/";
    $base_path="C:/Users/leayee/Desktop/tmp/";
    if(!file_exists($base_path)){
        if (@mkdir(rtrim($base_path, '/'), 0777))
        {
            @chmod($base_path, 0777);
        }
    }
    $k=fopen($base_path.$filename,"w+");
    fwrite($k,$txt);
    fclose($k);
}
//通过产品的SMT页面源文件获取其对应的产品描述源文件
function get_aliexpress_description($itemcode)
{
    $dn=strpos($itemcode,"descUrl");
    $itemurl=substr($itemcode,$dn+9);
    $dm=strpos($itemurl,"\"");
    $itemurl=substr($itemurl,2,$dm-2);

    $itemcodeX=file_get_contents_curl($itemurl);
    $dn=strpos($itemcodeX,"window.productDescription=");
    $itemcodeX=substr($itemcodeX,$dn+26);
    $itemcodeX=substr($itemcodeX,0,-4);
    
    //过滤掉相关产品链接
    $n1=strpos($itemcodeX,".aliexpress.com/item");
    if($n1){
    //获取左边字符串
    $leftstr=substr($itemcodeX,0,$n1-19);
    
    //获取右边字符串
    while($n1=strpos($itemcodeX,".aliexpress.com/item")){
        $itemcodeX=substr($itemcodeX,$n1+2);
        $n2=strpos($itemcodeX,"\"");
        $itemcodeX=substr($itemcodeX,$n2+1);
    }
    $n3=strpos($itemcodeX,"<img");
    $itemcodeX=substr($itemcodeX,$n3);
    $n4=strpos($itemcodeX,">");
    $itemcodeX=substr($itemcodeX,$n4+1);
    $itemcodeX=$leftstr.$itemcodeX;
    }
    //过滤掉相关产品链接
    $n1=strpos($itemcodeX,".aliexpress.com/store/product");
    if($n1){
    //获取左边字符串
    $leftstr=substr($itemcodeX,0,$n1-19);
    
    //获取右边字符串
    while($n1=strpos($itemcodeX,".aliexpress.com/store/product")){
        $itemcodeX=substr($itemcodeX,$n1+2);
        $n2=strpos($itemcodeX,"\"");
        $itemcodeX=substr($itemcodeX,$n2+1);
    }
    $n3=strpos($itemcodeX,"<img");
    $itemcodeX=substr($itemcodeX,$n3);
    $n4=strpos($itemcodeX,">");
    $itemcodeX=substr($itemcodeX,$n4+1);
    $itemcodeX=$leftstr.$itemcodeX;
    }
    
    $itemcodeX= str_replace(",",'',$itemcodeX);
    $itemcodeX= str_replace("\n",'',$itemcodeX);
    $itemcodeX= str_replace("aliexpress",'10meijin',$itemcodeX);
    $itemcodeX= str_replace("Aliexpress",'10meijin',$itemcodeX);
    $itemcodeX="<div style=\"width:830px;text-align:left ; vertical-align:middle;\">".$itemcodeX."</div>";
    return $itemcodeX;
}
//获取Item specifics
function get_short_des($itemcode)
{
    $gn=strpos($itemcode,"Item specifics");
    $itemstr=substr($itemcode,$gn);
    $gn1=strpos($itemstr,"<div");
    $itemstr=substr($itemstr,$gn1);
    $gm1=strpos($itemstr,"</div>");
    $result=substr($itemstr,0,$gm1+6);
    $result= str_replace(",",'',$result);
    $result= str_replace("\n",'',$result);
    return $result;
}
//获取SKU
function get_aliexpress_sku_pic($itemcode)
{
    $itemstr=$itemcode;
    $total="";
    if(strpos($itemstr,"item-sku-image")==false)
    {
     while(strpos($itemstr,"data-sku-id"))
    {
        $gn=strpos($itemstr,"data-sku-id");

        $itemstr=substr($itemstr,$gn);

        $gn1=strpos($itemstr,"title");
        $itemstr=substr($itemstr,$gn1+7);
        $gm1=strpos($itemstr,"\"");
        $title=substr($itemstr,0,$gm1);
        $total="$title"."| ;".$total;

    }
    }
    else{
    while(strpos($itemstr,"item-sku-image"))
    {
        $gn=strpos($itemstr,"item-sku-image");

        $itemstr=substr($itemstr,$gn);

        $gn1=strpos($itemstr,"title");
        $itemstr=substr($itemstr,$gn1+7);
        $gm1=strpos($itemstr,"\"");
        $title=substr($itemstr,0,$gm1);
        $gn2=strpos($itemstr,"src");
        $itemstr=substr($itemstr,$gn2+5);
        $gm2=strpos($itemstr,"_50x50.jpg");
        $imgurl=substr($itemstr,0,$gm2);
        $total="$title"."|".$imgurl.";".$total;

    }
    }
    return $total;
}
//获取SMT相册图片
function get_album_sku_pic($itemcode)
{
    $itemstr=$itemcode;
    $k=0;
    $total="";
    if(!strpos($itemstr,"img-thumb-item")){
        $gn = strpos($itemstr, "ui-image-viewer-thumb-frame");
        $itemstr = substr($itemstr, $gn);
        $gn2 = strpos($itemstr, "src");
        $itemstr = substr($itemstr, $gn2 + 5);
        $gm2 = strpos($itemstr, "_640x640.jpg");
        $imgurl = substr($itemstr, 0, $gm2);
        return $imgurl;
    }
    while(strpos($itemstr,"img-thumb-item"))
    {

        $gn=strpos($itemstr,"img-thumb-item");
        $itemstr=substr($itemstr,$gn);
        $gn2=strpos($itemstr,"src");
        $itemstr=substr($itemstr,$gn2+5);
        $gm2=strpos($itemstr,"_50x50.jpg");
        $imgurl=substr($itemstr,0,$gm2);
        $title="t".$k.".jpg";
        $total=$imgurl."|".$total;
        $k++;
    }
    //将相册图片倒序排列
    $total=substr($total,0,-1);
    $array=explode("|",$total);
    krsort($array);
    $total=implode("|",$array);
    return $total;
}
//获取标准售价
function get_startard_price($itemcode){
        $itemstr=$itemcode;
        $gn=strpos($itemstr,"j-sku-price");
        $itemstr=substr($itemstr,$gn);
        $gn2=strpos($itemstr,">");
        $itemstr=substr($itemstr,$gn2+1);
        $gm2=strpos($itemstr,"<");
        $startard_price=substr($itemstr,0,$gm2);
        if(strpos($startard_price,"-")!=false){
            $gm3=strpos($itemstr,"-");
            $startard_price=substr($startard_price,0,$gm3);
        }
        return $startard_price;
}
function get_sale_price($itemcode){
    
    $itemstr=$itemcode;
    $st_price=get_startard_price($itemstr);
    $gn=strpos($itemstr,"p-discount-rate");
    if($gn==false){
        return $st_price;
    }
    $itemstr=substr($itemstr,$gn+17);
    $gm2=strpos($itemstr,"%");
    $ratio=substr($itemstr,0,$gm2);
    $sale_price=floatval($st_price)*(1-floatval($ratio)/100);
    return round($sale_price,2);
}
//获取重量
function get_weight($itemcode){
        $itemstr=$itemcode;
        $gn=strpos($itemstr,"Package Weight");
        $itemstr=substr($itemstr,$gn);
        $gn2=strpos($itemstr,"(");
        $itemstr=substr($itemstr,$gn2+1);
        $gm2=strpos($itemstr,"lb.");
        $weight=substr($itemstr,0,$gm2);
        return $weight;
}
//获取体积
function get_size($itemcode){
        $itemstr=$itemcode;
        $gn=strpos($itemstr,"Package Size");
        $itemstr=substr($itemstr,$gn);
        $gn2=strpos($itemstr,"(");
        $itemstr=substr($itemstr,$gn2+1);
        $gm2=strpos($itemstr,")");
        $size=substr($itemstr,0,$gm2);
        return $size;
}
//获取产品ID
function get_pid($itemcode){
        $itemstr=$itemcode;
        $gn=strpos($itemstr,"productId");
        $itemstr=substr($itemstr,$gn);
        $gn2=strpos($itemstr,"=");
        $itemstr=substr($itemstr,$gn2+1);
        $gm2=strpos($itemstr,"\"");
        $pid=substr($itemstr,0,$gm2);
        return $pid;
}
//通过单条SMT链接转换单个SMT信息
function add($itemurl)
{
    //如果链接是网址
    if(strpos($itemurl,"ttp://www.aliexpress.com/")!=false){
       $itemcodeX=file_get_contents_curl($itemurl);
       
       if(strpos($itemcodeX,"product-name")==false){
           
           return "PHP can't get web content, please copy the source code of the page and paste it on locate txt file, then input the path of the file.";
           
       }
       else{
       $itemcode=$itemcodeX;
       insertone($itemcode);
       return "add one item successfully in the temp table";
       }
       
    }
    else{
       $itemcodeX=file_get_contents($itemurl);
       $i=0;
       while($p=strpos($itemcodeX,"</html>")){
         $itemcode=substr($itemcodeX,0,$p+7);
         insertone($itemcode);
         $i++;
         $itemcodeX=substr($itemcodeX,$p+7);
       }
       return "add  ".$i." item(s) successfully in the temp table";
    }
        
    
}
function insertone($itemcode){
    //获取SMT产品页面的产品描述的源文件
     // $description=get_aliexpress_description($itemcode);
        //获取 Item specifics
        $short_des=  get_short_des($itemcode);
        //判断是否导入了相同的产品
        $pid=get_pid($itemcode);
        global $wpdb;
        $table_name = $wpdb->prefix . "aliexport"; 
        $cn=$wpdb->get_var("select count(*) from ".$table_name." where sku like ".$pid);
        if($cn>0){
            return "The product is already in the temp table";
        }
        $table_name1 = $wpdb->prefix . "postmeta";
        $cn=$wpdb->get_var("select count(*) from ".$table_name1." where meta_key like '_sku' and meta_value like ".$pid);
        if($cn>0){
            return "The product is already in the 10meijin.com";
        }
        //获取产品标题
        $gn=strpos($itemcode,"product-name");
        $itemcodeA=substr($itemcode,$gn+30);
        $gm=strpos($itemcodeA,"</h1>");
        $title=substr($itemcodeA,0,$gm);
        $title= str_replace(",",'',$title);
        $title= str_replace("\'",'',$title);
        //获取属性图片
        $sku_info=get_aliexpress_sku_pic($itemcode);
        //获取相册图片
        $album=get_album_sku_pic($itemcode);
        //获取标准售价
        $st_price=  get_startard_price($itemcode);
        //获取折扣价
        $sale_price=  get_sale_price($itemcode);
        //获取重量
        $weight=  get_weight($itemcode);
        //获取尺寸
        $size=get_size($itemcode);
        $asize=explode("in x ",$size);
        $lengh=$asize[0];
        $width=$asize[1];
        $height=$asize[2];
        $height=substr($height,0,-2);
        
      //  $sku_info=substr($sku_info,0,-1);
        $array_sku=explode(";",$sku_info);
        for($i=0;$i<count($array_sku)+1;$i++){
            
           
         if($i==0){
         $wpdb->insert(
                 $table_name, array(
                'sku' => $pid,
                'parent_sku' => "",
                'title' => $title,
                'startard_price' =>$st_price,
                'sale_price' =>$sale_price,
                'color' => "",
            //    'description' => $description,
                'short_description' =>$short_des,
                'image' => $album,
                'weight' => $weight,
                'length' => $lengh,
                'width' => $width,
                'height' => $height
                    )
            );
        }
        else{
            $array_var=explode("|",$array_sku[$i-1]);
            $wpdb->insert(
                 $table_name, array(
                'sku' => $pid."_".$i,
                'parent_sku' => $pid,
                'title' => $title,
                'startard_price' =>$st_price,
                'sale_price' =>$sale_price,
                'color' => $array_var[0],
                'description' => "",
                'short_description' => "",
                'image' => $array_var[1],
                'weight' => $weight,
                'length' => $lengh,
                'width' => $width,
                'height' => $height
                    )
            );
        }
        
        }
}
function export(){
    global $wpdb;
    $table_name = $wpdb->prefix . "aliexport"; 
    $result=$wpdb->get_results("select * from ".$table_name);
    $i=0;
    foreach($result as $datarow){
        $sku[$i]=$datarow->sku;
        $parent_sku[$i]=$datarow->parent_sku;
        $title[$i]=$datarow->title;
        $startard_price[$i]=$datarow->startard_price;
        $sale_price[$i]=$datarow->sale_price;
        $color[$i]=$datarow->color;
        $description[$i]=$datarow->description;
        $short_description[$i]=$datarow->short_description;
        $image[$i]=$datarow->image;
        $weight[$i]=$datarow->weight;
        $length[$i]=$datarow->length;
        $width[$i]=$datarow->width;
        $height[$i]=$datarow->height;
        $i++;
    }
   $data="sku".","."parent_sku".","."title".","."startard_price".","."sale_price".","."color".","."short_description".","."image".","."weight".","."length".","."width".","."height"."\n"; 
    for($j=0;$j<$i;$j++){
        $data.=l($sku[$j]).",".l($parent_sku[$j]).",".l($title[$j]).",".l($startard_price[$j]).",".l($sale_price[$j]).",".l($color[$j]).",".l($short_description[$j]).",".l($image[$j]).",".l($weight[$j]).",".l($length[$j]).",".l($width[$j]).",".l($height[$j])."\n";
    }
    $filename ="export_test.csv";//文件名
    SaveText($data, $filename);
    $wpdb->query("TRUNCATE TABLE ".$table_name);
    return "export sucussfully";
}

function l($strInput) {
    return iconv('utf-8','gb2312',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
}
