<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Image;
use Validator;

class PublicController extends Controller
{
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
            return returnData(false, $th->errorInfo[2], null);
        }
    }
    
}
