<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\Myfunction;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    function fetchAllInterest(Request $request)
    {
        $totalData =  Interest::count();
        $rows = Interest::orderBy('id', 'DESC')->get();

        $categories = $rows;

        $columns = array(
            0 => 'id',
            1 => 'title'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = Interest::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $categories = Interest::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $categories =  Interest::Where('title', 'LIKE', "%{$search}%")
                                    ->offset($start)
                                    ->limit($limit)
                                    ->orderBy($order, $dir)
                                    ->get();
            $totalFiltered = Interest::where('id', 'LIKE', "%{$search}%")
                                    ->orWhere('title', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = array();
        foreach ($categories as $cat) {
            $edit = '<a href="" data-toggle="modal" id="' . $cat->id . '" rel="' . $cat->id . '"  data-title="' . $cat->title . '" class="btn btn-success mr-2 edit">Edit</a>';
            $delete = '<a rel="'.$cat->id.'" class="btn btn-danger delete text-white"> Delete </a>';

            $action = '<span class="float-end">' . $edit . $delete . ' </span>';

            $data[] = array(
                $cat->title,
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


    function addInterest(Request $req)
    {
        $interest = new Interest();
        $interest->title = Myfunction::customReplace($req->title);
        $interest->save();

        return response()->json([
            'status' => true, 
            'message' => 'Interest added successfully!'
        ]);
        
    }


    function updateInterest(Request $request)
    {
        $interest = Interest::where('id', $request->interest_id)->first();
        $interest->title = $request->title;
        $interest->save();

        return response()->json([
            'status' => true,
            'message' => 'Interest Updated Successfully',
        ]);

    }

    function getInterests()
    {
        $data = Interest::orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'message' => __('app.fetchSuccessful'),
            'data' => $data,
        ]);
    }
    function deleteInterest(Request $request)
    {
        $interest = Interest::where('id', $request->interest_id)->first();
        $interest->delete();

        return response()->json([
            'status' => true,
            'message' => 'Interest Deleted Successfully',
        ]);

    }
}
