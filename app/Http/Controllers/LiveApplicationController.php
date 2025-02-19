<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\LiveApplications;
use App\Models\LiveHistory;
use App\Models\Myfunction;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LiveApplicationController extends Controller
{
    //

    function liveapplication()
    {
        return view('liveapplication');
    }

    function livehistory()
    {
        return view('livehistory');
    }

    function fetchAllLiveStreamHistory(Request $request)
    {
        $rules = [
            'user_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $result =  LiveHistory::where('user_id', '=', $request->user_id)
            ->offset($request->start)
            ->limit($request->count)
            ->orderBy('id', 'DESC')
            ->get();

        if ($result->isEmpty()) {
            return json_encode([
                'status' => false,
                'message' => 'No data found',
                'data' => $result
            ]);
        }

        return json_encode([
            'status' => true,
            'message' => 'data get successfully',
            'data' => $result
        ]);
    }

    function fetchLiveHistory(Request $request)
    {
        $totalData =  LiveHistory::count();
        $rows = LiveHistory::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = array(
            0 => 'id'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = LiveHistory::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = LiveHistory::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  LiveHistory::Where('title', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%")
                ->orWhere('amount_collected', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = LiveHistory::where('id', 'LIKE', "%{$search}%")
                ->orWhere('amount_collected', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {

            if (count($item->user->images) > 0) {
                $image = '<img src="public/storage/' . $item->user->images[0]->image . '" width="50" height="50">';
            } else {
                $image = '<img src="http://placehold.jp/150x150.png" width="50" height="50">';
            }

            // $introVideo = '<a href=""  rel="' . $item->intro_video . '"   class="btn btn-warning  intro_video mr-2"><i class="fas fa-play"></i></a>';

            $data[] = array(
                $image,
                $item->user->fullname,
                $item->started_at,
                $item->streamed_for,
                $item->amount_collected,
                date('d-M-Y', strtotime($item->created_at)),

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

    function addLiveStreamHistory(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'started_at' => 'required',
            'streamed_for' => 'required',
            'amount_collected' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = Users::where('id', $request->user_id)->first();

        if ($user == null) {
            return json_encode([
                'status' => false,
                'message' => 'user not found!',
            ]);
        }

        $user->total_streams += 1;
        $user->save();

        $liveHistory = new LiveHistory();
        $liveHistory->user_id = $request->user_id;
        $liveHistory->started_at = $request->started_at;
        $liveHistory->streamed_for = $request->streamed_for;
        $liveHistory->amount_collected = $request->amount_collected;


        $result = $liveHistory->save();

        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'History added successfully!',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'something went wrong!',
            ]);
        }
    }

    public function approveApplication(Request $request)
    {
        $liveRequest = LiveApplications::where('id', $request->application_id)->first();
        if ($liveRequest) {

            GlobalFunction::deleteFile($liveRequest->intro_video);

            $user = Users::where('id', $liveRequest->user_id)->first();
            $user->can_go_live = 2;
            $user->save();

            if ($user->is_notification == 1) {
                $title = "Hello " . $user->fullname;
                $message = "Your Livestream application has been approved!";
                Myfunction::sendPushToUser($title, $message, $user->device_token);
            }
            
            $liveRequest->delete();

            return response()->json([
                'status' => true,
                'message' => 'Live Application Approved',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Live Application Not Found',
            ]);    
        }     
    }


    function viewLiveApplication($id)
    {
        $data = LiveApplications::where('id', $id)->first();

        return view('viewLiveApplication', ['data' => $data]);
    }

    function deleteLiveApplication(Request $request)
    {
        $liveRequest = LiveApplications::where('id', $request->application_id)->first();
        if ($liveRequest) {

            GlobalFunction::deleteFile($liveRequest->intro_video);

            $user = Users::where('id', $liveRequest->user_id)->first();
            $user->can_go_live = 0;
            $user->save();

            $liveRequest->delete();

            return response()->json([
                'status' => true,
                'message' => 'Delete Live Application',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Live Application Not Found',
            ]);    
        }       
    }

    function fetchLiveApplications(Request $request)
    {
        $totalData =  LiveApplications::count();
        $rows = LiveApplications::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'title'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = LiveApplications::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = LiveApplications::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  LiveApplications::Where('title', 'LIKE', "%{$search}%")
                ->orWhere('message', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = LiveApplications::where('title', 'LIKE', "%{$search}%")
                ->orWhere('message', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {

            if (count($item->user->images) > 0) {
                $image = '<img src="public/storage/' . $item->user->images[0]->image . '" width="50" height="50">';
            } else {
                $image = '<img src="http://placehold.jp/150x150.png" width="50" height="50">';
            }

            $block = '<span class="float-end"><a href="' . route('viewLiveApplication', $item->id) . '"  rel="' . $item->id . '"   class="btn btn-primary view_application mr-2">View</a><a href = ""  rel = "' . $item->id . '" class = "btn btn-danger reject text-white" > Reject </a></span>';
            // $introVideo = '<a href=""  rel="' . $item->intro_video . '"   class="btn btn-warning  intro_video mr-2"><i class="fas fa-play"></i></a>';

            $data[] = array(
                $image,
                $item->user->fullname,
                $item->languages,
                $block

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

    function applyForLive(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'about_you' => 'required',
            'languages' => 'required',
            'intro_video' => 'required',
            'social_links' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        // Checing if application already exists or not
        $liveApplication = LiveApplications::where('user_id', $request->user_id)->get();

        if (count($liveApplication) > 0) {
            return response()->json(['status' => false, 'message' => 'Application already exists']);
        }


        // verifying if user xists or not
        $user = Users::where('id', $request->user_id)->first();
        if ($user != null) {

            if ($user->can_go_live == 2) {
                return response()->json(['status' => false, 'message' => 'User is already eligible for livestreaming']);
            }

            $user->can_go_live = 1;
            $user->save();
        } else {
            return response()->json(['status' => false, 'message' => 'User does not exists']);
        }

        // Adding new applications
        $file = $request->file('intro_video');
        $videoPath = GlobalFunction::saveFileAndGivePath($file);
        
        $liveApplication = new LiveApplications();
        $liveApplication->user_id = $request->user_id;
        $liveApplication->about_you = $request->about_you;
        $liveApplication->languages = $request->languages;
        $liveApplication->intro_video = $videoPath;
        $liveApplication->social_links = json_encode($request->social_links);
        $result = $liveApplication->save();


        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Application submitted successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
