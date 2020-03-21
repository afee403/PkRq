<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RHonors;
use App\RMedals;
use DB;

class AdminController extends Controller
{
    // 初始数据-等级称号
    protected $honors = [
        ['name' => '赤脚', 'desc' => 'lv0'],
        ['name' => '草鞋', 'desc' => 'lv1'],
        ['name' => '棉鞋', 'desc' => 'lv2'],
        ['name' => '布鞋', 'desc' => 'lv3'],
        ['name' => '板鞋', 'desc' => 'lv4'],
        ['name' => '高跟鞋', 'desc' => 'lv5'],
        ['name' => '球鞋', 'desc' => 'lv6'],
        ['name' => '运动鞋', 'desc' => 'lv7'],
        ['name' => '跑鞋', 'desc' => 'lv8']
    ];
    // 初始数据-勋章 type:0不可重复获得 1可重复获得 
    protected $medals = [
        ['type'=>0, 'name'=>'1星跑者', 'mkey'=>'star_1_act', 'img'=>'star_1_act.png', 'desc' => '单次运动里程达5km'],
        ['type'=>0, 'name'=>'2星跑者', 'mkey'=>'star_2_act', 'img'=>'star_2_act.png', 'desc' => '单次运动里程达10km'],
        ['type'=>0, 'name'=>'3星跑者', 'mkey'=>'star_3_act', 'img'=>'star_3_act.png', 'desc' => '单次运动里程达15km'],
        ['type'=>0, 'name'=>'4星跑者', 'mkey'=>'star_4_act', 'img'=>'star_4_act.png', 'desc' => '单次运动里程达20km'],
        ['type'=>0, 'name'=>'5星跑者', 'mkey'=>'star_5_act', 'img'=>'star_5_act.png', 'desc' => '单次运动里程达21.0975km（半马）'],
        ['type'=>0, 'name'=>'6星跑者', 'mkey'=>'star_6_act', 'img'=>'star_6_act.png', 'desc' => '单次运动里程达42.195km（全马）'],
        ['type'=>0, 'name'=>'7星跑者', 'mkey'=>'star_7_act', 'img'=>'star_7_act.png', 'desc' => '单次运动里程达50km'],
        ['type'=>0, 'name'=>'8星跑者', 'mkey'=>'star_8_act', 'img'=>'star_8_act.png', 'desc' => '单次运动里程达100km'],
        ['type'=>0, 'name'=>'2020.01', 'mkey'=>'2020_01', 'img'=>'2020_01.png', 'desc' => '您2020.01累计跑步7次，授予您2020.01月活跃勋章'],
        ['type'=>0, 'name'=>'2020.02', 'mkey'=>'2020_02', 'img'=>'2020_02.png', 'desc' => '您2020.02累计跑步7次，授予您2020.02月活跃勋章'],
        ['type'=>0, 'name'=>'2020.03', 'mkey'=>'2020_03', 'img'=>'2020_03.png', 'desc' => '您2020.03累计跑步7次，授予您2020.03月活跃勋章'],
        ['type'=>0, 'name'=>'2020.04', 'mkey'=>'2020_04', 'img'=>'2020_04.png', 'desc' => '您2020.04累计跑步7次，授予您2020.04月活跃勋章'],
        ['type'=>0, 'name'=>'2020.05', 'mkey'=>'2020_05', 'img'=>'2020_05.png', 'desc' => '您2020.05累计跑步7次，授予您2020.05月活跃勋章'],
        ['type'=>0, 'name'=>'2020.06', 'mkey'=>'2020_06', 'img'=>'2020_06.png', 'desc' => '您2020.06累计跑步7次，授予您2020.06月活跃勋章'],
        ['type'=>0, 'name'=>'2020.07', 'mkey'=>'2020_07', 'img'=>'2020_07.png', 'desc' => '您2020.07累计跑步7次，授予您2020.07月活跃勋章'],
        ['type'=>0, 'name'=>'2020.08', 'mkey'=>'2020_08', 'img'=>'2020_08.png', 'desc' => '您2020.08累计跑步7次，授予您2020.08月活跃勋章'],
        ['type'=>0, 'name'=>'2020.09', 'mkey'=>'2020_09', 'img'=>'2020_09.png', 'desc' => '您2020.09累计跑步7次，授予您2020.09月活跃勋章'],
        ['type'=>0, 'name'=>'2020.10', 'mkey'=>'2020_10', 'img'=>'2020_10.png', 'desc' => '您2020.10累计跑步7次，授予您2020.10月活跃勋章'],
        ['type'=>0, 'name'=>'2020.11', 'mkey'=>'2020_11', 'img'=>'2020_11.png', 'desc' => '您2020.11累计跑步7次，授予您2020.11月活跃勋章'],
        ['type'=>0, 'name'=>'2020.12', 'mkey'=>'2020_12', 'img'=>'2020_12.png', 'desc' => '您2020.12累计跑步7次，授予您2020.12月活跃勋章'],
        ['type'=>0, 'name'=>'Spring2020', 'mkey'=>'2020_a', 'img'=>'2020_a.png', 'desc' => '您2020年春季累计跑步45次，授予您Spring2020活跃勋章'],
        ['type'=>0, 'name'=>'Summer2020', 'mkey'=>'2020_b', 'img'=>'2020_b.png', 'desc' => '您2020年夏季累计跑步45次，授予您Summer2020活跃勋章'],
        ['type'=>0, 'name'=>'Autumn2020', 'mkey'=>'2020_c', 'img'=>'2020_c.png', 'desc' => '您2020年秋季累计跑步45次，授予您Autumn2020活跃勋章'],
        ['type'=>0, 'name'=>'Winter2020', 'mkey'=>'2020_d', 'img'=>'2020_d.png', 'desc' => '您2020年冬季累计跑步45次，授予您Winter2020活跃勋章'],
        ['type'=>1, 'name'=>'青铜', 'mkey'=>'rank_a', 'img'=>'rank_a.png', 'desc' => '您在上月累计里程在校区前100名，授予您一枚青铜勋章'],
        ['type'=>1, 'name'=>'白银', 'mkey'=>'rank_b', 'img'=>'rank_b.png', 'desc' => '您在上月累计里程在校区前10名，授予您一枚白银勋章'],
        ['type'=>1, 'name'=>'黄金', 'mkey'=>'rank_c', 'img'=>'rank_c.png', 'desc' => '您在上月累计里程在省内前100名，授予您一枚黄金勋章'],
        ['type'=>1, 'name'=>'钻石', 'mkey'=>'rank_d', 'img'=>'rank_d.png', 'desc' => '您在上月累计里程在全国前100名，授予您一枚钻石勋章']
    ];
    // 初始化  key=123123 data=honors/medals
    public function initData(Request $request){
        if($request->has('key')){
            if($request->key == "123123"){
                if($request->data == "honors"){  // 初始化称号数据
                    try {
                        DB::beginTransaction();
                        foreach($this->honors as $honor){
                            $rHonors = new RHonors();
                            $rHonors->fillable(array_keys($honor));
                            $rHonors->fill($honor);
                            $rHonors->save();
                        }
                        DB::commit();
                        return returnData(true, "称号初始化完成", null);
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return returnData(false, $th);
                    }
                }else if($request->data == "medals"){ // 初始化勋章
                    $filepath = 'http://'.$request->server('HTTP_HOST').'/resources/medals/';
                    try {
                        DB::beginTransaction();
                        foreach($this->medals as $medal){
                            $medal['img'] = $filepath.$medal['img'];  //转化图片路径
                            $rMedals = new RMedals();
                            $rMedals->fillable(array_keys($medal));
                            $rMedals->fill($medal);
                            $rMedals->save();
                        }
                        DB::commit();
                        return returnData(true, "勋章初始化完成", null);
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        return returnData(false, $th);
                    }
                }
                
            }else{
                return returnData(false, 'key错误，非法操作', null);
            }
        }else{
            return returnData(false, '没有key，非法操作', null);
        }
    }
}
