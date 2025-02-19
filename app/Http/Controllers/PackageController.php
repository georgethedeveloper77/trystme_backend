<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    function addPackage(Request $req){

        $count = Package::count();
        if($count < 3){
            $pack = new Package();
    
            $pack->description  = $req->description ;
            $pack->title = $req->title; 
            $pack->months = $req->months;
            $pack->appid = $req->appid;
            $pack->playid = $req->playid;
            $pack->price = $req->price;
            $pack->save();
        
            return json_encode(['status'=>true,'message'=>__('app.AddSuccessful') ]);
        }else{
            
            return json_encode(['status'=>false,'message'=>__('app.minimumPackage') ]);
        }
      
     }
    
     function fetchAllPackage(Request $request){
    
        $totalData =  Package::count();
        $rows = Package::orderBy('id', 'DESC')->get();
    
    
        $result = $rows;
    
        $columns = array(
            0 => 'id',
            1 => 'title'
        );
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalData = Package::count();
        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Package::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result =  Package::Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered =Package::where('id', 'LIKE', "%{$search}%")
                ->orWhere('title', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        foreach ($result as $item) {
            
    
            if($item->is_block == 0){
    $block  =   '<a href=""  rel="'.$item->id.'"   class="btn btn-primary  edit_cats mr-2"><i class="fas fa-edit"></i></a><a href = ""  rel = "'.$item->id.'" class = "btn btn-danger delete-cat text-white" > <i class="fas fa-trash-alt"></i> </a>';
            }
    
            $data[] = array(
             
             
    
             '<p>'.$item->title.'</p>', 
             '<p>'.$item->description.'</p>', 
             '<p>'.$item->price.'</p>', 
             '<p>'.$item->months.'</p>',
             '<p>'.$item->appid.'</p>',
             '<p>'.$item->playid.'</p>',
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
    
    function getPackageById($id){
        $data = Package::where('id',$id)->first();
    
        echo json_encode($data);
    }
    
    function updatePackage(Request $req){
    
    
        $pack = Package::find($req->id);
    
        $pack->description  = $req->description ;
        $pack->title = $req->title; 
        $pack->months = $req->months;
        $pack->appid = $req->appid;
        $pack->playid = $req->playid;
        $pack->price = $req->price;
        $pack->save();
    
        return json_encode(['status'=>true,'message'=>__('app.Updatesuccessful') ]);
     }
    
     function deletePackage($id){
    
        $data =  Package::where('id',$id);
    $data->delete();
    
    $data1['status'] = true;
  
    echo json_encode($data1);
    
    }
    
    function getPackage(){
        $data = Package::orderBy('id','DESC')->get();
    
        return json_encode(['status'=>true ,'message'=>__('app.fetchSuccessful'),'data'=> $data]);
    }
    
}
