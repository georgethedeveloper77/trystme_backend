<?php

namespace App\Http\Controllers;

use App\Models\DiamondPacks;
use Illuminate\Http\Request;

class DiamondPackController extends Controller
{
    //

    function diamondpacks()
    {
        return view('diamondpacks');
    }

    function getDiamondPacks(Request $request){
        $data = DiamondPacks::all();

        return json_encode([
            'status' => true,
            'message' => 'diamond packs get successfully',
            'data' => $data
        ]);
    }

    public function addDiamondPack(Request $request)
    {
        $pack = new DiamondPacks();
        $pack->amount = $request->amount;
        $pack->android_product_id = $request->android_product_id;
        $pack->ios_product_id = $request->ios_product_id;
        $pack->save();

        return response()->json([
            'status' => true,
            'message' => 'Diamond Pack Added Successfully',
        ]);
    }

    function getDiamondPackById($id)
    {
        $data = DiamondPacks::where('id', $id)->first();
        echo response()->json($data);
    }

    function updateDiamondPack(Request $request)
    {
        $pack = DiamondPacks::where('id', $request->id)->first();
        $pack->amount = $request->amount;
        $pack->android_product_id = $request->android_product_id;
        $pack->ios_product_id = $request->ios_product_id;
        $pack->save();

        return response()->json([
            'status' => true,
            'message' => 'Diamond Pack Updated Successfully',
        ]);
    }

    function deleteDiamondPack(Request $request)
    {
        $diamondPack = DiamondPacks::where('id', $request->diamond_pack_id)->first();
        $diamondPack->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Diamond Pack Deleted',
        ]);        
    }

    function fetchDiamondPackages(Request $request)
    {
        $totalData =  DiamondPacks::count();
        $rows = DiamondPacks::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = array(
            0 => 'id',
            1 => 'amount'
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = DiamondPacks::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = DiamondPacks::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  DiamondPacks::Where('amount', 'LIKE', "%{$search}%")
                                    ->orWhere('android_product_id', 'LIKE', "%{$search}%")
                                    ->orWhere('ios_product_id', 'LIKE', "%{$search}%")
                                    ->offset($start)
                                    ->limit($limit)
                                    ->orderBy($order, $dir)
                                    ->get();
            $totalFiltered = DiamondPacks::where('amount', 'LIKE', "%{$search}%")
                                    ->orWhere('android_product_id', 'LIKE', "%{$search}%")
                                    ->orWhere('ios_product_id', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = array();
        foreach ($result as $item) {
 
            $block = '<span class="float-end">
                            <a href="" rel="' . $item->id . '" 
                                class="btn btn-success edit mr-2" 
                                data-amount="'. $item->amount .'" 
                                data-android_product_id="'. $item->android_product_id .'"
                                data-ios_product_id="'. $item->ios_product_id .'"> 
                                Edit 
                            </a>
                            <a rel="'.$item->id.'" class="btn btn-danger delete text-white"> 
                                Delete
                            </a>
                        </span>';

            $data[] = array(
                $item->amount,
                $item->android_product_id,
                $item->ios_product_id,
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
}
