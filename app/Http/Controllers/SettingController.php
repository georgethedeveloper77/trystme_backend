<?php

namespace App\Http\Controllers;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Models\AppData;
use App\Models\Gifts;
use App\Models\GlobalFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

include "./app/Class/AgoraDynamicKey/RtcTokenBuilder.php";

class SettingController extends Controller
{

   function generateAgoraToken(Request $request)
   {
      $rules = [
         'channelName' => 'required'
      ];
      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
         $messages = $validator->errors()->all();
         $msg = $messages[0];
         return response()->json(['status' => false, 'message' => $msg]);
      }
      $appID = env('AGORA_APP_ID');
      $appCertificate = env('AGORA_APP_CERT');
      $channelName = $request->channelName;
      $role = RtcTokenBuilder::RolePublisher;
      $expireTimeInSeconds = 7200;
      $currentTimestamp = now()->getTimestamp();
      $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
      $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, 0, $role, $privilegeExpiredTs);
      
      return json_encode(['status' => true, 'message' => "generated successfully", 'token' => $token]);
   }
   
   function changeFromDatingAppToLivestreamApp($value)
   {
      DB::table('appdata')->where('id', 1)->update([
         'is_dating' => $value,
      ]);

      return json_encode(['status' => true, 'message' => __('app.Updatesuccessful')]);
   }

   function changeFromSocialMedia($value)
   {
      DB::table('appdata')->where('id', 1)->update([
         'is_social_media' => $value,
      ]);

      return json_encode(['status' => true, 'message' => __('app.Updatesuccessful')]);
   }

   function updateGift(Request $request)
   {
      $gift = Gifts::where('id', $request->id)->first();
      $gift->coin_price = $request->coin_price;
      if ($request->has('image')) {
         GlobalFunction::deleteFile($gift->image);
         
         $gift->image = GlobalFunction::saveFileAndGivePath($request->image);
      }
      $gift->save();

      return response()->json([
         'status' => true,
         'message' => 'Gift Update Successfully',
      ]);
   }

   function addGift(Request $request)
   {
      $gift = new Gifts();
      $gift->image = GlobalFunction::saveFileAndGivePath($request->image);
      $gift->coin_price = $request->coin_price;
      $gift->save();

      return response()->json([
         'status' => true,
         'message' => "Gift Added Successfully.",
         'data' => $gift,
      ]);
   } 

    function deleteGift(Request $request)
    {
        $gift = Gifts::where('id', $request->gift_id)->first();
        GlobalFunction::deleteFile($gift->image);
        $gift->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Gift Delete Successfully.',
        ]);        
    }

   function fetchAllGifts(Request $request)
   {
      $totalData =  Gifts::count();
      $rows = Gifts::orderBy('id', 'DESC')->get();

      $result = $rows;

      $columns = array(
         0 => 'id',
         1 => 'coin_price'
      );

      $limit = $request->input('length');
      $start = $request->input('start');
      $order = $columns[$request->input('order.0.column')];
      $dir = $request->input('order.0.dir');
      $totalData = Gifts::count();
      $totalFiltered = $totalData;
      if (empty($request->input('search.value'))) {
         $result = Gifts::offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
      } else {
         $search = $request->input('search.value');
         $result =  Gifts::Where('coin_price', 'LIKE', "%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
         $totalFiltered = Gifts::where('coin_price', 'LIKE', "%{$search}%")
            ->count();
      }
      $data = array();
      foreach ($result as $item) {

         $image = '<img src="public/storage/' . $item->image . '" width="50" height="50">';
         $imgUrl = env('image') . $item->image;

         $action = '<a data-img="'.$imgUrl.'" 
                     data-price="'.$item->coin_price.'"
                     rel="' . $item->id . '" 
                     class="btn btn-success edit text-white mr-2">
                     Edit
                     </a>
                     <a rel="' . $item->id . '"
                     class="btn btn-danger delete text-white">
                     Delete
                     </a>';

         $data[] = array(
            $image,
            $item->coin_price,
            $action
         );
      }
      $json_data = array(
         "draw"            => intval($request->input('draw')),
         "recordsTotal"    => intval($totalData),
         "recordsFiltered" => $totalFiltered,
         "data"            => $data
      );
      echo json_encode($json_data);
      exit();
   }

   function gifts()
   {
      return view('gifts');
   }
   
   function setting()
   {
      $appdata = AppData::first();
      return view('setting', ['appdata' => $appdata]);
   }

   function updateAppdata(Request $request)
   {
      $setting = AppData::first();

      if ($setting == null) {
         return response()->json([
            'status' => false,
            'message' => 'Setting Not Found',
         ]);
      }

      if ($request->has('app_name')) {
         $setting->app_name = $request->app_name;
         $request->session()->put('app_name', $setting['app_name']);
      }
      if ($request->has('currency')) {
         $setting->currency = $request->currency;
      }
      if ($request->has('min_threshold')) {
         $setting->min_threshold = $request->min_threshold;
      }
      if ($request->has('min_user_live')) {
         $setting->min_user_live = $request->min_user_live;
      }
      if ($request->has('max_minute_live')) {
         $setting->max_minute_live = $request->max_minute_live;
      }
      if ($request->has('message_price')) {
         $setting->message_price = $request->message_price;
      }
      if ($request->has('reverse_swipe_price')) {
         $setting->reverse_swipe_price = $request->reverse_swipe_price;
      }
      if ($request->has('coin_rate')) {
         $setting->coin_rate = $request->coin_rate;
      }
      if ($request->has('admob_int_ios')) {
         $setting->admob_int_ios = $request->admob_int_ios;
      }
      if ($request->has('admob_banner_ios')) {
         $setting->admob_banner_ios = $request->admob_banner_ios;
      }
      if ($request->has('admob_int')) {
         $setting->admob_int = $request->admob_int;
      }
      if ($request->has('admob_banner')) {
         $setting->admob_banner = $request->admob_banner;
      }
      if ($request->has('live_watching_price')) {
         $setting->live_watching_price = $request->live_watching_price;
      }
      if ($request->has('post_description_limit')) {
         $setting->post_description_limit = $request->post_description_limit;
      }
      if ($request->has('post_upload_image_limit')) {
         $setting->post_upload_image_limit = $request->post_upload_image_limit;
      }
      $setting->save();

      return response()->json([
         'status' => true,
         'message' => __('app.Updatesuccessful'),
      ]);
      
   }

   function getSettingData(Request $req)
   {
      $data['appdata'] = DB::table('appdata')->first();
      $data['gifts'] = Gifts::all();
      return json_encode(['status' => true, 'message' => __('app.fetchSuccessful'), 'data' => $data]);
   }

   public function storeFileGivePath(Request $request)
   {
      $path = GlobalFunction::saveFileAndGivePath($request->file);
      return response()->json([
         'status' => true,
         'message' => __('app.Updatesuccessful'),
         'path' => $path,
      ]);
   }
}
