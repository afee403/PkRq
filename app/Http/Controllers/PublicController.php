<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Validator;
use App\RHonors;

class PublicController extends Controller
{
    // 图片上传
    public function uploadImg(Request $request){
        $inputData = $request->all();
        $rules = [
            'img' => [ 'file','image','max:10240' ]
        ];
        $validator = Validator::make($inputData,$rules);
        if($validator->fails()){
            return returnData(true, "校验失败", back()->withErrors($validator)->withInput());
        }
        $photo = $inputData['img'];
        $file_name = uniqid();
        $file_relative_path = 'resources/images/'.date('Y-m-d');
        $file_path = public_path($file_relative_path);
        try {
            if (!is_dir($file_path)){
                mkdir($file_path);
            }
            // 保存缩略图 resources/images/2020-01-31/5e3319bcafcae-min-200x356.jpg
            $dfile = Image::make($photo);
            $min_file_path = '/'.$file_name.'-min-'.'200x'.round(200*$dfile->height()/$dfile->width()).'.'.$photo->getClientOriginalExtension();
            $image = Image::make($photo)->resize(200, null, function ($constraint) {$constraint->aspectRatio();})->save($file_path.$min_file_path);
            // 保存原图   resources/images/2020-01-31/5e3319bcafcae-1080x1920.jpg
            $original_file_path = '/'.$file_name.'-'.$dfile->width().'x'.$dfile->height().'.'.$photo->getClientOriginalExtension();
            $image = Image::make($photo)->save($file_path.$original_file_path);
            //处理返回网络url
            $imgUrl = 'http://'.$request->server('HTTP_HOST').'/'.$file_relative_path;
            $data = [
                'name' => $photo->getClientOriginalName(),
                'store' => $file_name,
                'extension' => $photo->getClientOriginalExtension(),
                'mimetype' => $photo->getClientMimeType(),
                'size' => $photo->getClientSize(),
                'width' => $dfile->width(),
                'height' => $dfile->height(),
                'mwidth' => 200,
                'mheight' => round(200*$dfile->height()/$dfile->width()),
                'original' => $imgUrl.$original_file_path,
                'thumbnail' => $imgUrl.$min_file_path,
                'error' => $photo->getError()
            ];
            return returnData(true, '上传成功', $data);
        } catch (\Throwable $th) {
            return returnData(false, $th);
        }
    }
    // 获取称号列表
    public function getHonorAll(){
        $honors = RHonors::get();
        $data = [];
        foreach($honors as $honor){
            array_push($data, ['name'=>$honor->name, 'desc'=>$honor->desc]);
        }
        return returnData(true, "操作成功", $data);
    }
    // 获取openid
    public function getOpenid(Request $request){
        if($request->has('code')){
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.env('WX_APPID').'&secret='.env('WX_SECRET').'&js_code='.$request->code.'&grant_type=authorization_code';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//不验证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//不验证主机
            $returnjson=curl_exec($curl);
            if($returnjson){
                //整理返回数据
                $json = json_decode($returnjson);
                if(!property_exists($json, 'errmsg')){
                    return returnData(true, "操作成功", $json);
                }else{
                    return returnData(false, $json->errmsg, null);
                }
            }else{
                return returnData(false, curl_error($curl), null);
            }
        }else{
            return returnData(false, "缺少code", null);
        }
    }
}
