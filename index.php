<?php
$data = $_GET;
$json['code'] = 2; // 返回信息 code=1成功其他为失败，具体信息会返回到msg中
$json['msg'] = '失败';
$key = 'aoishdakjwdiosauydisd';
switch ($data) { // 进行请求参数合法验证
    case empty($data['equipment']):
        $json['msg'] = '请求参数缺少： equipment 设备id';
        echo json_encode($json);die;
        break;
    case empty($data['datatype']):
        $json['msg'] = '请求参数缺少： datatype 数据类型';
        echo json_encode($json);die;
        break;
    case empty($data['time']):
        $json['msg'] = '请求参数缺少： time 请求时间戳';
        echo json_encode($json);die;
        break;
    case empty($data['sign']):
        $json['msg'] = '请求参数缺少： sign 请求秘钥';
        echo json_encode($json);die;
        break;
    case empty($data['merchant']):
        $json['msg'] = '请求参数缺少： merchant 商户id';
        echo json_encode($json);die;
        break;
    case empty($data['branch']):
        $json['msg'] = '请求参数缺少： branch 分店id';
        echo json_encode($json);die;
        break;
    case empty($data['data']):
        $json['msg'] = '请求参数缺少： data 需要打log的数据';
        echo json_encode($json);die;
        break;
    case (time() - $data['time']) > 300:
        $json['msg'] = '请求时间戳超时';
        echo json_encode($json);die;
        break;
    default:
        $sign = md5($data['time'].$data['datatype'].$data['equipment'].$data['merchant'].$data['branch'].'#'.$key);
        if ($sign != $data['sign']) {
            $json['msg'] = 'sign验证错误';
            echo json_encode($json);die;
        }
        break;
}
$file = './'.$data['merchant'].'/'.$data['branch'].'/'.$data['equipment'].'/'; // log存储地址
if (!file_exists($file)) {
    mkdir($file, 777, True);
}
function FileCount($dir){  // 获取文件夹内文件个数
    global $count; 
    if(is_dir($dir)&&file_exists($dir)){ 
        $ob=scandir($dir); 
        foreach($ob as $file){ 
            if($file=="."||$file==".."){ 
                continue; 
            } 
            $file=$dir."/".$file; 
            if(is_file($file)){ 
                $count++; 
            }elseif(is_dir($file)){ 
                FileCount($file); 
            } 
        } 
    }  
}
//调用方法
$count=0;
FileCount($file);
if ($count == 0) {
    $fileName = date('Ymd').'_'.$data['datatype'].'_1.txt';
} else {
    $fileName = date('Ymd').'_'.$data['datatype'].'_'.$count.'.txt';
    $size = filesize($file.$fileName);
    if ($size >= 5242880) {
        $count = $count+1;
        $fileName = date('Ymd').'_'.$data['datatype'].'_'.$count.'.txt';
    }
}
$logdata = date('Y-m-d H:i:s').':'.$data['data']."\r\n";
$myfile = fopen($file.$fileName, "a") or die("Unable to open file!");
fwrite($myfile, $logdata);
fclose($myfile);
$json['code'] = 1;
$json['msg'] = '存储成功';
echo json_encode($json);die;
?>