<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Education;
use App\Models\User;
use DataTables;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with("getEducation","getCompany")->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="edit btn btn-primary btn-sm">Edit</a> <a href="javascript:void(0)" data-id="' . $row->id . '" class="delete btn btn-danger btn-sm">Delete</a>';

                    return $btn;
                })
                ->addColumn('image', function ($row) {
                    $btn = '<img src="'.url("storage/images/".$row->image).'" width="55%">';
                    return $btn;
                })
                ->addColumn('edu_id', function ($row) {
                    return @$row->getEducation->name;
                
                })
                ->addColumn('cmp_id', function ($row) {
                    return @$row->getCompany->name;
                
                })
                ->rawColumns(['action',"image","edu_id","cmp_id"])
                ->make(true);
        }
        $data["company"] = Company::get();
        $data["education"] = Education::get();
        return view("user-data", $data);
    }
    public function storeUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z]+$/u',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits:10',
            'cmp_id' => 'required',
            'edu_id' => 'required',
            'image' => 'required',
        ],[
          "cmp_id.required"=>"The company field is required."  ,
          "edu_id.required"=>"The education field is required."  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),"status"=>0
            ]);
        }
        $data = new User();
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->edu_id = $request->edu_id;
        $data->cmp_id = $request->cmp_id;
        $data->password = Hash::make("123");
        if ($request->image) {
            $name = $request->image->getClientOriginalName();
            Storage::disk("public")->putFileAs('images', new File($request->image), $name);
            $data->image = $name;
        }
        $data->save();

        return response()->json(["status" => 2]);
    }
    public function updateUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[a-zA-Z]+$/u',
            'email' => 'required|email|unique:users,email,'.$request->id,
            'phone' => 'required|digits:10',
            'cmp_id' => 'required',
            'edu_id' => 'required',
        ],[
          "cmp_id.required"=>"The company field is required."  ,
          "edu_id.required"=>"The education field is required."  
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),"status"=>0
            ]);
        }

        $data = User::find($request->id);
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->edu_id = $request->edu_id;
        $data->cmp_id = $request->cmp_id;
        $data->password = Hash::make("123");
        if ($request->image) {
            $name = $request->image->getClientOriginalName();
            Storage::disk("public")->putFileAs('images', new File($request->image), $name);
            $data->image = $name;
        }
        $data->save();

        return response()->json(["status" => 1]);
    }
    public function editUser(Request $request)
    {

        $data = User::find($request->id);
        return response()->json($data);
    }
    public function deleteUser(Request $request)
    {

        $data = User::find($request->id);
        $data->delete();
        return response()->json("data delete successfully");
    }
}
