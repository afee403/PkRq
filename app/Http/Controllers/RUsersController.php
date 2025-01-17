<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\RUsers;
use App\RMedals;
use App\LinkUMs;
use App\RSettings;
use Image;
use Validator;

class RUsersController extends Controller
{
    /**
     * 用户授权注册
     */
    public function regster(Request $request){
        if ($request->has('openid')) {
            $user = new RUsers();
            try {
                DB::beginTransaction();
                    // 用户数据
                    $user->fillable(array_keys($request->all()));
                    $user->fill($request->all());
                    $user->save();
                DB::commit();
                // 获取用户基本信息
                $data = RUsers::where('rid', $user->id)->first();
                // 获取勋章
                $data = LinkUMs::where('rid', $request->rid)->get();
                //        ->leftJoin('r_medals', 'link_u_ms.meid', '=', 'r_medals.meid')
                //        ->select('link_u_ms.*', 'r_medals.mkey', 'r_medals.type', 'r_medals.name', 'r_medals.desc', 'r_medals.img')
                //        ->orderBy('created_at', 'asc')
                //        ->get();
                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                DB::rollBack();
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, '缺少openid');
        }
    }

    /**
     * 获取用户信息
     */
    public function getUser(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            if ($request->has('openid')) {
                try {
                    $user = RUsers::where('openid', $request->openid)->first();
                    if($user) return returnData(true, '操作成功', $user);
                    else return returnData(false, '未注册');
                } catch (\Throwable $th) {
                    return returnData(false, $th->getMessage());
                }
            }else{
                try {
                    return returnData(true, '操作成功', RUsers::where('rid', $request->rid)->first());
                } catch (\Throwable $th) {
                    return returnData(false, $th->getMessage());
                }
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }

    /**
     * 获取用户信息（含勋章称号）
     */
    public function getUserAll(Request $request){
        if ($request->has('rid')) {
            // try {
                // 获取用户基本信息
                $data = RUsers::where('rid', $request->rid)->first();
                // 获取勋章
                $data = LinkUMs::where('rid', $request->rid)->get();
                //        ->leftJoin('r_medals', 'link_u_ms.meid', '=', 'r_medals.meid')
                //        ->select('link_u_ms.*', 'r_medals.mkey', 'r_medals.type', 'r_medals.name', 'r_medals.desc', 'r_medals.img')
                //        ->orderBy('created_at', 'asc')
                //        ->get();
                return returnData(true, '操作成功', $data);
            // } catch (\Throwable $th) {
            //     return returnData(false, $th->getMessage());
            // }
        }else{
            return returnData(false, '缺少rid', null);
        }
    }


    /**
     * 获取徽章
     */
    public function lightMedal(Request $request){
        if ($request->has('rid') && $request->has('meid')) {
            try {
                DB::beginTransaction();
                    // 勋章授予
                    $me = new LinkUMs();
                    $me->fill([
                        'rid' => $request->rid,
                        'meid' => $request->meid
                    ]);
                    $me->save();
                DB::commit();
                return returnData(true, '操作成功', null);
            } catch (\Throwable $th) {
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, '缺少信息', null);
        }
    }
    

    /**
     * 获取已获勋章
     */
    public function getMedal(Request $request){
        if ($request->has('rid')) {
            try {
                // 获取勋章
                $data = LinkUMs::where('rid', $request->rid)->get();
                //        ->leftJoin('r_medals', 'link_u_ms.meid', '=', 'r_medals.meid')
                //        ->select('link_u_ms.*', 'r_medals.mkey', 'r_medals.type', 'r_medals.name', 'r_medals.desc', 'r_medals.img')
                //        ->orderBy('created_at', 'asc')
                //        ->get();
                return returnData(true, '操作成功', $data);
            } catch (\Throwable $th) {
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, '缺少rid', null);
        }

    }

    /**
     * 上传头像
     */
    public function uploadImg(Request $request){
        if($request->has('img')){
            $rules = [
                'img' => [ 'file','image','max:10240' ]
            ];
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return returnData(false, "校验失败", back()->withErrors($validator)->withInput());
            }
            $photo = $request->img;
            $file_name = uniqid();
            $file_relative_path = 'resources/userImgs/';
            $file_path = public_path($file_relative_path);
            try {
                if (!is_dir($file_path)){
                    mkdir($file_path);
                }
                //压缩储存图片 resources/userImgs/5e8867ed44bd4.jpg
                $image = Image::make($photo)
                                ->resize(200, null, function ($constraint) {$constraint->aspectRatio();})
                                ->save($file_path.'/'.$file_name.'.'.$photo->getClientOriginalExtension());
                //处理url
                $fileurl = 'http://'.$request->server('HTTP_HOST').'/'.$file_relative_path.$file_name.'.'.$photo->getClientOriginalExtension();
                //返回数据
                return returnData(true, '上传成功', ['url' => $fileurl]);
            } catch (\Throwable $th) {
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, "缺少参数", $request->all());
        }
    }

    /**
     * 修改用户信息
     */
    public function doUpdate(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            $user = null;
            try {
                // 获取信息
                if ($request->has('openid')) {
                    $user = RUsers::where('openid', $request->openid);
                }else{
                    $user = RUsers::where('rid', $request->rid);
                }
                // 更新
                if($user->first()){
                    if($user->update($request->all())){
                        if ($request->has('openid')) {
                            $data = RUsers::where('openid', $request->openid)->first();
                        }else{
                            $data = RUsers::where('rid', $request->rid)->first();
                        }
                        return returnData(true, '操作成功', $data);
                    }
                    else return returnData(false, '保存失败', null);
                }else{
                    return returnData(false, '不存在该用户', null);
                }
            } catch (\Throwable $th) {
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }

    /**
     * 注销账户
     */
    public function doUnset(Request $request){
        if ($request->has('openid') || $request->has('rid')) {
            try {
                if ($request->has('openid')) {
                    RUsers::where('openid', $request->openid)->delete();
                }else{
                    RUsers::where('rid', $request->rid)->delete();
                }
                return returnData(true, "操作成功", null);
            } catch (\Throwable $th) {
                return returnData(false, $th->getMessage());
            }
        }else{
            return returnData(false, '缺少openid或rid', null);
        }
    }

    /**
     * 隐私设置
     * public function doSettings(Request $request){
     *     if ($request->has('rid')) {
     *         $setting = null;
     *         try {
     *             // 获取隐私设置数据
     *             $setting = RSettings::where('rid', $request->rid);
     *             // 更新
     *             if($setting->first()){
     *                 if($setting->update($request->all())){
     *                     $data = RSettings::where('rid', $request->rid)->first();
     *                     return returnData(true, '操作成功', $data);
     *                 }
     *                 else return returnData(false, '保存失败', null);
     *             }else{
     *                 return returnData(false, '读取数据库失败', null);
     *             }
     *         } catch (\Throwable $th) {
     *             return returnData(false, $th->getMessage());
     *         }
     *     }else{
     *         return returnData(false, '缺少rid', null);
     *     }
     * }
     */

    /**
     * 隐私设置-重置
     * public function resetSettings(Request $request){
     *     if ($request->has('rid')){
     *         if($this->initProvicySettings($request->rid)){
     *             return returnData(true, '操作成功', RSettings::where('rid', $request->rid)->first());
     *         }else{
     *             return returnData(false, '重置失败', null);
     *         }
     *     }else{
     *         return returnData(false, '缺少rid', null);
     *     }
     * }
     */

    /**
     * 个人主页访问权限
     * public function getProvicy(Request $request){
     *     if($request->has('rid')){
     *         try {
     *             return returnData(true, '操作成功', RSettings::where('rid', $request->rid)->first());
     *         } catch (\Throwable $th) {
     *             return returnData(false, $th->getMessage());
     *         }
     *     }else{
     *         return returnData(false, '缺少rid');
     *     }
     * }
     */

    /**
     * 初始化隐私设置
     * private function initProvicySettings($rid){
     *     if(isset($rid)){
     *         $user = RUsers::where('rid', $rid)->get();
     *         if($user){
     *             DB::table('r_settings')->where('rid', $rid)->delete();
     *             $setting = new RSettings();
     *             $setting->rid = $rid;
     *             $setting->job = 1; $setting->team = 1; $setting->run = 1;
     *             return $setting->save();
     *         }else{
     *             return false;
     *         }
     *     }else{
     *         return false;
     *     }
     * }
     */

    /**
     * 查询所有校区
     * public function getSchools(Request $request){
     *     try {
     *         return returnData(true, '操作成功', RUsers::where('team', '<>', 'system')
     *             ->whereNotIn('team', ['system', ''])
     *             ->whereNotNull('team')
     *             ->select('team')
     *             ->distinct()
     *             ->get());
     *     } catch (\Throwable $th) {
     *         return returnData(false, $th->getMessage());
     *     }
     * }
     */
}
